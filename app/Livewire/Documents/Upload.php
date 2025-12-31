<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Upload extends Component
{
    use WithFileUploads;

    public $file;
    public $title;
    public $description;
    public $category = "other";
    public $document_date;

    protected $rules = [
        "file" =>
            "required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp",
        "title" => "required|string|max:255",
        "description" => "nullable|string|max:1000",
        "category" =>
            "required|in:medical_report,prescription,test_result,scan,letter,insurance,other",
        "document_date" => "nullable|date",
    ];

    protected $messages = [
        "file.required" => "Please select a file to upload.",
        "file.max" => "File size must be less than 10MB.",
        "file.mimes" =>
            "File must be a PDF, Word document, or image (JPG, PNG, GIF, WebP).",
        "title.required" => "Please enter a title for your document.",
        "category.required" => "Please select a category.",
    ];

    public function updatedFile()
    {
        Log::info("File upload started", [
            "user_id" => auth()->id(),
            "file_name" => $this->file
                ? $this->file->getClientOriginalName()
                : "null",
            "file_size" => $this->file ? $this->file->getSize() : "null",
            "file_type" => $this->file ? $this->file->getMimeType() : "null",
        ]);

        try {
            $this->validate([
                "file" =>
                    "required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp",
            ]);

            // Auto-populate title from filename if empty
            if (empty($this->title) && $this->file) {
                $this->title = pathinfo(
                    $this->file->getClientOriginalName(),
                    PATHINFO_FILENAME,
                );
            }

            Log::info("File validation passed", [
                "user_id" => auth()->id(),
                "file_name" => $this->file->getClientOriginalName(),
            ]);
        } catch (\Exception $e) {
            Log::error("File upload validation failed", [
                "user_id" => auth()->id(),
                "error" => $e->getMessage(),
                "file_name" => $this->file
                    ? $this->file->getClientOriginalName()
                    : "null",
            ]);

            $this->dispatch("notify", [
                "type" => "error",
                "message" => "File upload failed: " . $e->getMessage(),
            ]);
        }
    }

    public function save()
    {
        Log::info("Document save started", [
            "user_id" => auth()->id(),
            "has_file" => !is_null($this->file),
            "title" => $this->title,
            "category" => $this->category,
        ]);

        try {
            $this->validate();

            if (!$this->file) {
                throw new \Exception("File is missing after validation");
            }

            Log::info("Document validation passed, starting file storage", [
                "user_id" => auth()->id(),
                "file_name" => $this->file->getClientOriginalName(),
                "file_size" => $this->file->getSize(),
                "storage_disk" => "private",
            ]);

            // Store the file
            $fileName =
                time() .
                "_" .
                Str::slug(
                    pathinfo(
                        $this->file->getClientOriginalName(),
                        PATHINFO_FILENAME,
                    ),
                ) .
                "." .
                $this->file->getClientOriginalExtension();

            $filePath = $this->file->storeAs("documents", $fileName, "private");

            if (!$filePath) {
                throw new \Exception("Failed to store file to disk");
            }

            Log::info("File stored successfully", [
                "user_id" => auth()->id(),
                "file_path" => $filePath,
                "file_name" => $fileName,
            ]);

            // Create document record
            $document = new Document();
            $document->user_id = auth()->id();
            $document->title = $this->title;
            $document->description = $this->description;
            $document->file_name = $this->file->getClientOriginalName();
            $document->file_path = $filePath;
            $document->file_type = $this->file->getMimeType();
            $document->file_size = $this->file->getSize();
            $document->category = $this->category;
            $document->document_date = $this->document_date;
            $document->save();

            Log::info("Document record created successfully", [
                "user_id" => auth()->id(),
                "document_id" => $document->id,
                "file_path" => $filePath,
            ]);

            // Reset form
            $this->reset([
                "file",
                "title",
                "description",
                "category",
                "document_date",
            ]);
            $this->category = "other";

            // Dispatch success event
            $this->dispatch("document-uploaded");
            $this->dispatch("notify", [
                "type" => "success",
                "message" => "Document uploaded successfully!",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Document validation failed", [
                "user_id" => auth()->id(),
                "errors" => $e->errors(),
                "file_present" => !is_null($this->file),
            ]);

            $this->dispatch("notify", [
                "type" => "error",
                "message" =>
                    "Validation failed: " .
                    collect($e->errors())->flatten()->first(),
            ]);
        } catch (\Exception $e) {
            Log::error("Document upload failed", [
                "user_id" => auth()->id(),
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
                "file_present" => !is_null($this->file),
                "php_upload_max_filesize" => ini_get("upload_max_filesize"),
                "php_post_max_size" => ini_get("post_max_size"),
                "php_memory_limit" => ini_get("memory_limit"),
            ]);

            $this->dispatch("notify", [
                "type" => "error",
                "message" => "Failed to upload document: " . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view("livewire.documents.upload", [
            "categories" => Document::getCategories(),
        ]);
    }
}
