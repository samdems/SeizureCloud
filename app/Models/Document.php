<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        "user_id",
        "title",
        "description",
        "file_name",
        "file_path",
        "file_type",
        "file_size",
        "category",
        "document_date",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "document_date" => "date",
            "file_size" => "integer",
        ];
    }

    /**
     * Get the user that owns the document
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get available document categories
     */
    public static function getCategories(): array
    {
        return [
            "medical_report" => "Medical Report",
            "prescription" => "Prescription",
            "test_result" => "Test Result",
            "scan" => "Scan/Imaging",
            "letter" => "Letter",
            "insurance" => "Insurance",
            "other" => "Other",
        ];
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ["B", "KB", "MB", "GB"];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . " " . $units[$i];
    }

    /**
     * Get the full storage path
     */
    public function getStoragePath(): string
    {
        return Storage::disk("private")->path($this->file_path);
    }

    /**
     * Get the download URL
     */
    public function getDownloadUrl(): string
    {
        return route("documents.download", $this->id);
    }

    /**
     * Get the view URL
     */
    public function getViewUrl(): string
    {
        return route("documents.view", $this->id);
    }

    /**
     * Check if the document is an image
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
            "image/webp",
        ]);
    }

    /**
     * Check if the document is a PDF
     */
    public function isPdf(): bool
    {
        return $this->file_type === "application/pdf";
    }

    /**
     * Delete the document and its file
     */
    public function deleteWithFile(): bool
    {
        // Delete the physical file
        if (Storage::disk("private")->exists($this->file_path)) {
            Storage::disk("private")->delete($this->file_path);
        }

        // Delete the database record
        return $this->delete();
    }

    /**
     * Get the category label
     */
    public function getCategoryLabelAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? "Other";
    }
}
