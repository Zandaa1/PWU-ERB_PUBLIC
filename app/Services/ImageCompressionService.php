<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class ImageCompressionService
{
    private const MAX_DIMENSION = 1920;

    private const JPEG_QUALITY = 82;

    private const WEBP_QUALITY = 82;

    private const PNG_COMPRESSION = 6;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(GdDriver::class);
    }

    public function isImage(UploadedFile $file): bool
    {
        $mime = strtolower($file->getMimeType() ?? '');
        $ext = strtolower($file->getClientOriginalExtension() ?? '');

        if (str_starts_with($mime, 'image/')) {
            return true;
        }

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif'], true);
    }

    /**
     * Compress image to web-friendly size.
     * Returns binary string of compressed image, or null if not an image or on failure (fallback to original).
     */
    public function compress(UploadedFile $file): ?string
    {
        if (! $this->isImage($file)) {
            return null;
        }

        try {
            $image = $this->manager->decodePath($file->getRealPath());

            // Scale down longest side to MAX_DIMENSION, never upscale
            $image->scaleDown(width: self::MAX_DIMENSION, height: self::MAX_DIMENSION);

            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $mime = strtolower($file->getMimeType() ?: '');

            // Keep original format for archival fidelity — web-friendly via scaling + quality
            $originalSize = $file->getSize();

            if (in_array($ext, ['jpg', 'jpeg'], true) || str_contains($mime, 'jpeg')) {
                $encoded = $image->encodeUsingFormat(Format::JPEG, quality: self::JPEG_QUALITY);
            } elseif ($ext === 'webp' || str_contains($mime, 'webp')) {
                $encoded = $image->encodeUsingFormat(Format::WEBP, quality: self::WEBP_QUALITY);
            } elseif ($ext === 'png') {
                $encoded = $image->encodeUsingFormat(Format::PNG);
            } else {
                // bmp/gif — convert to JPEG for web
                $encoded = $image->encodeUsingFormat(Format::JPEG, quality: self::JPEG_QUALITY);
            }

            $binary = (string) $encoded;

            // Only return compressed if it's actually smaller than original (avoid inflating small images)
            if (strlen($binary) >= $originalSize && $originalSize < 500 * 1024) {
                return null;
            }

            return $binary;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    public function compressedExtension(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif'], true)) {
            return $ext === 'jpeg' ? 'jpg' : $ext;
        }

        return $ext;
    }
}
