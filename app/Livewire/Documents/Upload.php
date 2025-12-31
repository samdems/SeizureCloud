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
    }

    public function save()
    {
        $this->validate();

        try {
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
        } catch (\Exception $e) {
            Log::error("Document upload failed", [
                "user_id" => auth()->id(),
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
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
