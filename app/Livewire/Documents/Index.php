<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;

    public $search = "";
    public $categoryFilter = "";
    public $sortBy = "created_at";
    public $sortDirection = "desc";
    public $showUploadModal = false;
    public $showEditModal = false;
    public $editingDocument = null;
    public $editTitle;
    public $editDescription;
    public $editCategory;
    public $editDocumentDate;

    protected $listeners = [
        "document-uploaded" => "refreshDocuments",
        "document-updated" => "refreshDocuments",
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection =
                $this->sortDirection === "asc" ? "desc" : "asc";
        } else {
            $this->sortBy = $column;
            $this->sortDirection = "asc";
        }
    }

    public function deleteDocument($documentId)
    {
        $document = Document::where("user_id", auth()->id())->find($documentId);

        if ($document) {
            try {
                $document->deleteWithFile();
                $this->dispatch("notify", [
                    "type" => "success",
                    "message" => "Document deleted successfully.",
                ]);
            } catch (\Exception $e) {
                $this->dispatch("notify", [
                    "type" => "error",
                    "message" =>
                        "Failed to delete document: " . $e->getMessage(),
                ]);
            }
        }
    }

    public function downloadDocument($documentId)
    {
        $document = Document::where("user_id", auth()->id())->find($documentId);

        if (
            $document &&
            Storage::disk("private")->exists($document->file_path)
        ) {
            return Storage::disk("private")->download(
                $document->file_path,
                $document->file_name,
            );
        }

        $this->dispatch("notify", [
            "type" => "error",
            "message" => "Document not found.",
        ]);
    }

    public function editDocument($documentId)
    {
        $document = Document::where("user_id", auth()->id())->find($documentId);

        if ($document) {
            $this->authorize("update", $document);

            $this->editingDocument = $document;
            $this->editTitle = $document->title;
            $this->editDescription = $document->description;
            $this->editCategory = $document->category;
            $this->editDocumentDate = $document->document_date
                ? $document->document_date->format("Y-m-d")
                : null;
            $this->showEditModal = true;
        }
    }

    public function updateDocument()
    {
        $this->validate([
            "editTitle" => "required|string|max:255",
            "editDescription" => "nullable|string|max:1000",
            "editCategory" =>
                "required|in:medical_report,prescription,test_result,scan,letter,insurance,other",
            "editDocumentDate" => "nullable|date",
        ]);

        if ($this->editingDocument) {
            try {
                $this->editingDocument->update([
                    "title" => $this->editTitle,
                    "description" => $this->editDescription,
                    "category" => $this->editCategory,
                    "document_date" => $this->editDocumentDate,
                ]);

                $this->dispatch("notify", [
                    "type" => "success",
                    "message" => "Document updated successfully.",
                ]);

                $this->showEditModal = false;
                $this->editingDocument = null;
                $this->reset([
                    "editTitle",
                    "editDescription",
                    "editCategory",
                    "editDocumentDate",
                ]);
            } catch (\Exception $e) {
                $this->dispatch("notify", [
                    "type" => "error",
                    "message" =>
                        "Failed to update document: " . $e->getMessage(),
                ]);
            }
        }
    }

    public function toggleUploadModal()
    {
        $this->showUploadModal = !$this->showUploadModal;
    }

    public function toggleEditModal()
    {
        $this->showEditModal = !$this->showEditModal;
        if (!$this->showEditModal) {
            $this->editingDocument = null;
            $this->reset([
                "editTitle",
                "editDescription",
                "editCategory",
                "editDocumentDate",
            ]);
        }
    }

    public function refreshDocuments()
    {
        $this->showUploadModal = false;
        $this->showEditModal = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Document::where("user_id", auth()->id());

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where("title", "like", "%{$this->search}%")
                    ->orWhere("description", "like", "%{$this->search}%")
                    ->orWhere("file_name", "like", "%{$this->search}%");
            });
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where("category", $this->categoryFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $documents = $query->paginate(10);

        return view("livewire.documents.index", [
            "documents" => $documents,
            "categories" => Document::getCategories(),
        ]);
    }
}
