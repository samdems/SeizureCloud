<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    /**
     * Generate QR code for video URL
     */
    public function generateVideoQrCode(string $url, int $size = 200): string
    {
        $builder = new Builder(
            writer: new SvgWriter(),
            data: $url,
            encoding: new Encoding("UTF-8"),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 10,
        );

        $result = $builder->build();

        return "data:image/svg+xml;base64," .
            base64_encode($result->getString());
    }

    /**
     * Generate QR code with custom label
     */
    public function generateQrCodeWithLabel(
        string $url,
        string $label = "",
        int $size = 200,
    ): string {
        $builder = new Builder(
            writer: new SvgWriter(),
            data: $url,
            encoding: new Encoding("UTF-8"),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 10,
            labelText: $label,
        );

        $result = $builder->build();

        return "data:image/svg+xml;base64," .
            base64_encode($result->getString());
    }

    /**
     * Generate QR code as base64 data URI for PDF embedding
     */
    public function generateForPdf(string $url, int $size = 150): string
    {
        return $this->generateVideoQrCode($url, $size);
    }

    /**
     * Validate URL before QR code generation
     */
    public function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Generate QR code for seizure video with metadata
     */
    public function generateSeizureVideoQrCode(
        string $videoUrl,
        array $metadata = [],
    ): string {
        if (!$this->isValidUrl($videoUrl)) {
            throw new \InvalidArgumentException(
                "Invalid URL provided for QR code generation",
            );
        }

        $size = $metadata["size"] ?? 150;
        $label = $metadata["label"] ?? "Scan to view seizure video";

        return $this->generateQrCodeWithLabel($videoUrl, $label, $size);
    }

    /**
     * Generate multiple QR codes for different purposes
     */
    public function generateMultiple(array $urls): array
    {
        $qrCodes = [];

        foreach ($urls as $key => $url) {
            if ($this->isValidUrl($url)) {
                $qrCodes[$key] = $this->generateVideoQrCode($url);
            }
        }

        return $qrCodes;
    }

    /**
     * Get recommended QR code size based on usage
     */
    public function getRecommendedSize(string $usage = "pdf"): int
    {
        return match ($usage) {
            "pdf" => 120,
            "web" => 200,
            "mobile" => 150,
            "print" => 300,
            default => 150,
        };
    }
}
