<?php

namespace App\Http\Controllers;

use App\Services\VideoUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    protected VideoUploadService $videoUploadService;

    public function __construct(VideoUploadService $videoUploadService)
    {
        $this->videoUploadService = $videoUploadService;
    }

    /**
     * View video by secure token
     */
    public function view(string $token, Request $request)
    {
        $videoData = $this->videoUploadService->getVideoByToken($token);

        if (!$videoData) {
            abort(404, "Video not found or access expired.");
        }

        $seizure = $videoData["seizure"];
        $filePath = $videoData["file_path"];
        $mimeType = $videoData["mime_type"];

        // Check if it's a download request
        if ($request->has("download")) {
            return $this->downloadVideo($seizure, $filePath);
        }

        // Always stream the video directly
        return $this->streamVideo($seizure, $filePath, $mimeType, $request);
    }

    /**
     * Stream video file with range support
     */
    protected function streamVideo(
        $seizure,
        $filePath,
        $mimeType,
        Request $request,
    ) {
        if (!Storage::disk("private")->exists($filePath)) {
            abort(404, "Video file not found.");
        }

        $fileSize = Storage::disk("private")->size($filePath);
        $stream = Storage::disk("private")->readStream($filePath);

        if (!$stream) {
            abort(500, "Unable to read video file.");
        }

        // Handle range requests for video streaming
        $headers = [
            "Content-Type" => $mimeType,
            "Accept-Ranges" => "bytes",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache",
            "Expires" => "0",
        ];

        // Handle range requests for video seeking/streaming
        if ($request->hasHeader("Range")) {
            return $this->handleRangeRequest(
                $stream,
                $fileSize,
                $request->header("Range"),
                $mimeType,
            );
        }

        $headers["Content-Length"] = $fileSize;

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            200,
            $headers,
        );
    }

    /**
     * Download video file
     */
    protected function downloadVideo($seizure, $filePath)
    {
        if (!Storage::disk("private")->exists($filePath)) {
            abort(404, "Video file not found.");
        }

        $originalFilename = pathinfo($filePath, PATHINFO_BASENAME);
        $downloadFilename = "seizure_video_{$seizure->id}_{$seizure->start_time->format(
            "Y-m-d_H-i",
        )}.{$this->getExtension($filePath)}";

        return Storage::disk("private")->download(
            $filePath,
            $downloadFilename,
            [
                "Cache-Control" => "no-cache, must-revalidate",
                "Pragma" => "no-cache",
                "Expires" => "0",
            ],
        );
    }

    /**
     * Handle HTTP range requests for video streaming
     */
    protected function handleRangeRequest(
        $stream,
        int $fileSize,
        string $range,
        string $mimeType,
    ) {
        // Parse the range header
        if (!preg_match("/bytes=(\d+)-(\d*)/", $range, $matches)) {
            abort(416, "Requested Range Not Satisfiable");
        }

        $start = (int) $matches[1];
        $end = $matches[2] !== "" ? (int) $matches[2] : $fileSize - 1;

        // Validate range
        if ($start > $end || $start >= $fileSize || $end >= $fileSize) {
            abort(416, "Requested Range Not Satisfiable");
        }

        $length = $end - $start + 1;

        // Seek to the start position
        fseek($stream, $start);

        $headers = [
            "Content-Type" => $mimeType,
            "Accept-Ranges" => "bytes",
            "Content-Length" => $length,
            "Content-Range" => "bytes {$start}-{$end}/{$fileSize}",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache",
            "Expires" => "0",
        ];

        return response()->stream(
            function () use ($stream, $length) {
                $remaining = $length;
                while ($remaining > 0 && !feof($stream)) {
                    $chunkSize = min(8192, $remaining);
                    echo fread($stream, $chunkSize);
                    $remaining -= $chunkSize;
                    flush();
                }
                fclose($stream);
            },
            206, // Partial Content
            $headers,
        );
    }

    /**
     * Get file extension from path
     */
    protected function getExtension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
}
