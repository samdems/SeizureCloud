<?php

namespace App\Services;

use App\Models\Seizure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VideoUploadService
{
    private const VIDEO_DISK = "private";
    private const VIDEO_DIRECTORY = "seizure-videos";
    private const TOKEN_EXPIRY_DAYS = null; // No expiration
    private const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100MB

    private const ALLOWED_EXTENSIONS = ["mp4", "mov", "avi", "mkv", "webm"];
    private const ALLOWED_MIME_TYPES = [
        "video/mp4",
        "video/quicktime",
        "video/x-msvideo",
        "video/x-matroska",
        "video/webm",
    ];

    /**
     * Upload a video file for a seizure record
     */
    public function uploadVideo(Seizure $seizure, UploadedFile $file): bool
    {
        // Validate the file
        if (!$this->validateVideo($file)) {
            return false;
        }

        // Remove existing video if present
        $this->removeExistingVideo($seizure);

        // Generate secure filename
        $filename = $this->generateSecureFilename($seizure->id, $file);

        // Store the file
        $path = $file->storeAs(
            self::VIDEO_DIRECTORY,
            $filename,
            self::VIDEO_DISK,
        );

        if (!$path) {
            return false;
        }

        // Generate public token (no expiration)
        $token = $this->generateSecureToken();

        // Update seizure record
        $seizure->update([
            "video_file_path" => $path,
            "video_public_token" => $token,
            "video_expires_at" => null, // Never expires
            "has_video_evidence" => true,
        ]);

        return true;
    }

    /**
     * Remove video from seizure record
     */
    public function removeVideo(Seizure $seizure): bool
    {
        $this->removeExistingVideo($seizure);

        // Clear video fields from database
        $seizure->update([
            "video_file_path" => null,
            "video_public_token" => null,
            "video_expires_at" => null,
            "has_video_evidence" => false,
        ]);

        return true;
    }

    /**
     * Get video file contents by token
     */
    public function getVideoByToken(string $token): ?array
    {
        $seizure = Seizure::where("video_public_token", $token)->first();

        if (!$seizure || !$seizure->hasValidVideo()) {
            return null;
        }

        if (
            !Storage::disk(self::VIDEO_DISK)->exists($seizure->video_file_path)
        ) {
            // File missing, clean up database
            $seizure->update([
                "video_file_path" => null,
                "video_public_token" => null,
                "video_expires_at" => null,
            ]);
            return null;
        }

        return [
            "seizure" => $seizure,
            "file_path" => $seizure->video_file_path,
            "mime_type" => $this->getMimeTypeFromPath(
                $seizure->video_file_path,
            ),
        ];
    }

    /**
     * Extend video token expiry (deprecated - videos no longer expire)
     */
    public function extendTokenExpiry(Seizure $seizure, int $days = null): bool
    {
        // Videos no longer expire, always return true for existing videos
        return $seizure->video_public_token !== null;
    }

    /**
     * Generate a new token for existing video
     */
    public function regenerateToken(Seizure $seizure): bool
    {
        if (!$seizure->video_file_path) {
            return false;
        }

        $token = $this->generateSecureToken();

        $seizure->update([
            "video_public_token" => $token,
            "video_expires_at" => null, // Never expires
        ]);

        return true;
    }

    /**
     * Clean up expired video tokens (deprecated - videos no longer expire)
     */
    public function cleanupExpiredTokens(): int
    {
        // Videos no longer expire, return 0
        return 0;
    }

    /**
     * Get video file size in MB
     */
    public function getVideoSize(Seizure $seizure): ?float
    {
        if (
            !$seizure->video_file_path ||
            !Storage::disk(self::VIDEO_DISK)->exists($seizure->video_file_path)
        ) {
            return null;
        }

        $bytes = Storage::disk(self::VIDEO_DISK)->size(
            $seizure->video_file_path,
        );
        return round($bytes / (1024 * 1024), 2);
    }

    /**
     * Validate uploaded video file
     */
    private function validateVideo(UploadedFile $file): bool
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return false;
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return false;
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return false;
        }

        return true;
    }

    /**
     * Remove existing video file from storage
     */
    private function removeExistingVideo(Seizure $seizure): void
    {
        if (
            $seizure->video_file_path &&
            Storage::disk(self::VIDEO_DISK)->exists($seizure->video_file_path)
        ) {
            Storage::disk(self::VIDEO_DISK)->delete($seizure->video_file_path);
        }
    }

    /**
     * Generate secure filename for video
     */
    private function generateSecureFilename(
        int $seizureId,
        UploadedFile $file,
    ): string {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = Carbon::now()->format("Y-m-d_H-i-s");
        $hash = Str::random(16);

        return "seizure_{$seizureId}_{$timestamp}_{$hash}.{$extension}";
    }

    /**
     * Generate secure public token
     */
    private function generateSecureToken(): string
    {
        do {
            $token = Str::random(64);
        } while (Seizure::where("video_public_token", $token)->exists());

        return $token;
    }

    /**
     * Get MIME type from file path
     */
    private function getMimeTypeFromPath(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            "mp4" => "video/mp4",
            "mov" => "video/quicktime",
            "avi" => "video/x-msvideo",
            "mkv" => "video/x-matroska",
            "webm" => "video/webm",
            default => "video/mp4",
        };
    }

    /**
     * Get allowed file extensions for validation
     */
    public static function getAllowedExtensions(): array
    {
        return self::ALLOWED_EXTENSIONS;
    }

    /**
     * Get max file size in MB
     */
    public static function getMaxFileSizeMB(): int
    {
        return self::MAX_FILE_SIZE / (1024 * 1024);
    }
}
