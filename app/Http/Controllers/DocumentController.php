<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Download a document
     */
    public function download(Document $document): StreamedResponse
    {
        // Authorize download access
        $this->authorize("download", $document);

        // Check if file exists
        if (!Storage::disk("private")->exists($document->file_path)) {
            abort(404, "Document file not found.");
        }

        return Storage::disk("private")->download(
            $document->file_path,
            $document->file_name,
        );
    }

    /**
     * View/preview a document (inline)
     */
    public function view(Document $document)
    {
        // Authorize view access
        $this->authorize("view", $document);

        // Check if file exists
        if (!Storage::disk("private")->exists($document->file_path)) {
            abort(404, "Document file not found.");
        }

        // For images and PDFs, show inline
        if ($document->isImage() || $document->isPdf()) {
            return Storage::disk("private")->response(
                $document->file_path,
                $document->file_name,
            );
        }

        // Otherwise, download
        return Storage::disk("private")->download(
            $document->file_path,
            $document->file_name,
        );
    }

    /**
     * Show the documents page
     */
    public function index()
    {
        // Authorize viewing documents
        $this->authorize("viewAny", Document::class);

        return view("documents.index");
    }
}
