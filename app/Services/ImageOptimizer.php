<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageOptimizer
{
    private int $maxWidth;

    private int $maxHeight;

    private int $thumbWidth;

    private int $quality;

    private int $thumbQuality;

    public function __construct()
    {
        $this->maxWidth = (int) config('media.max_width', 1200);
        $this->maxHeight = (int) config('media.max_height', 1200);
        $this->thumbWidth = (int) config('media.thumb_width', 480);
        $this->quality = (int) config('media.quality', 78);
        $this->thumbQuality = (int) config('media.thumb_quality', 75);
    }

    public function storePublicImage(UploadedFile $file, string $directory, ?string $basename = null, bool $resolveUnique = true): string
    {
        $directory = trim($directory, '/');
        $this->ensureDirectory($directory);
        $this->ensureDirectory($directory.'/thumbs');

        $source = $this->loadImage($file);
        $width = imagesx($source);
        $height = imagesy($source);

        if ($basename && $resolveUnique) {
            $basename = $this->uniqueBasename($directory, $basename);
        } elseif (! $basename) {
            $basename = (string) Str::uuid();
        }

        $optimized = $this->resize($source, $width, $height, $this->maxWidth, $this->maxHeight);
        $storedPath = $this->encodeImage($optimized, $directory, $basename, $this->quality);
        imagedestroy($optimized);

        // Always create a lightweight thumbnail for listings/cart.
        $thumbSource = $this->loadImage($file);
        $thumb = $this->resize($thumbSource, $width, $height, $this->thumbWidth, $this->thumbWidth);
        $this->encodeImage($thumb, $directory.'/thumbs', $basename, $this->thumbQuality);
        imagedestroy($thumb);

        return $storedPath;
    }

    /**
     * @param  array<int, UploadedFile|null>|UploadedFile|null  $files
     * @return list<string>
     */
    public function storePublicImages(array|UploadedFile|null $files, string $directory, ?string $nameSlug = null): array
    {
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        $paths = [];
        $slug = $nameSlug ? Str::slug($nameSlug) : '';
        $slug = Str::limit(trim($slug, '-'), 80, '');

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            if (! $file->isValid()) {
                throw new RuntimeException($this->uploadErrorMessage($file, $index));
            }

            $basename = null;

            if ($slug !== '') {
                $candidate = $index === 0 ? $slug : "{$slug}-".($index + 1);
                $basename = $this->uniqueBasename($directory, $candidate);
            }

            $paths[] = $this->storePublicImage($file, $directory, $basename, resolveUnique: false);
        }

        return $paths;
    }

    private function ensureDirectory(string $directory): void
    {
        Storage::disk('public')->makeDirectory($directory);

        $path = Storage::disk('public')->path($directory);

        if (! is_dir($path) || ! is_writable($path)) {
            throw new RuntimeException("Upload directory is not writable: {$directory}");
        }
    }

    private function loadImage(UploadedFile $file): GdImage
    {
        $path = $file->getRealPath();

        if ($path === false || ! is_readable($path)) {
            throw new RuntimeException('Uploaded image could not be read.');
        }

        $image = match ($this->detectMime($file)) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported image type. Use JPG, PNG, GIF, or WebP.');
        }

        return $this->normalize($image);
    }

    private function detectMime(UploadedFile $file): string
    {
        $mime = $file->getMimeType() ?: '';
        $extension = strtolower($file->getClientOriginalExtension());

        if ($mime === 'application/octet-stream' || $mime === '') {
            $mime = match ($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => $mime,
            };
        }

        return $mime;
    }

    private function normalize(GdImage $image): GdImage
    {
        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function resize(GdImage $source, int $width, int $height, int $maxWidth, int $maxHeight): GdImage
    {
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return $source;
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        return $dest;
    }

    private function encodeImage(GdImage $image, string $directory, string $basename, int $quality): string
    {
        $webpRelative = trim($directory, '/').'/'.$basename.'.webp';
        $webpFull = Storage::disk('public')->path($webpRelative);

        if (function_exists('imagewebp') && @imagewebp($image, $webpFull, $quality)) {
            return $webpRelative;
        }

        $jpegRelative = trim($directory, '/').'/'.$basename.'.jpg';
        $jpegFull = Storage::disk('public')->path($jpegRelative);

        if (! @imagejpeg($image, $jpegFull, min($quality + 7, 90))) {
            throw new RuntimeException('Failed to save optimized image.');
        }

        Log::warning('WebP unavailable, saved JPEG fallback.', ['path' => $jpegRelative]);

        return $jpegRelative;
    }

    private function uploadErrorMessage(UploadedFile $file, int $index): string
    {
        $label = 'Image '.($index + 1);

        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "{$label} is too large. Max upload is 8MB per image.",
            UPLOAD_ERR_PARTIAL => "{$label} was only partially uploaded. Please try again.",
            UPLOAD_ERR_NO_FILE => "{$label} was not uploaded.",
            default => "{$label} failed to upload. Check server upload limits.",
        };
    }

    private function uniqueBasename(string $directory, string $basename): string
    {
        $candidate = Str::slug($basename, '-');
        $candidate = Str::limit(trim($candidate, '-'), 80, '');
        $candidate = rtrim($candidate, '-');

        if ($candidate === '') {
            return (string) Str::uuid();
        }

        if (! $this->basenameExists($directory, $candidate)) {
            return $candidate;
        }

        $suffix = 2;

        while ($this->basenameExists($directory, "{$candidate}-{$suffix}")) {
            $suffix++;
        }

        return "{$candidate}-{$suffix}";
    }

    private function basenameExists(string $directory, string $basename): bool
    {
        $directory = trim($directory, '/');

        return Storage::disk('public')->exists("{$directory}/{$basename}.webp")
            || Storage::disk('public')->exists("{$directory}/{$basename}.jpg")
            || Storage::disk('public')->exists("{$directory}/thumbs/{$basename}.webp")
            || Storage::disk('public')->exists("{$directory}/thumbs/{$basename}.jpg");
    }
}
