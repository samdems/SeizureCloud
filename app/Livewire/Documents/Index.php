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

    protected $listeners = ["document-uploaded" => "refreshDocuments"];

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

    public function toggleUploadModal()
    {
        $this->showUploadModal = !$this->showUploadModal;
    }

    public function refreshDocuments()
    {
        $this->showUploadModal = false;
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
