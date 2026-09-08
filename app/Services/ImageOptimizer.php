<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

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
        if (! $file->isValid()) {
            throw new RuntimeException($this->uploadErrorMessage($file, 0));
        }

        $directory = trim($directory, '/');
        $this->ensureDirectory($directory);
        $this->ensureDirectory($directory.'/thumbs');

        if ($basename && $resolveUnique) {
            $basename = $this->uniqueBasename($directory, $basename);
        } elseif (! $basename) {
            $basename = (string) Str::uuid();
        } else {
            $basename = Str::slug($basename) ?: (string) Str::uuid();
        }

        if (! extension_loaded('gd')) {
            return $this->storeOriginal($file, $directory, $basename);
        }

        try {
            return $this->optimizeAndStore($file, $directory, $basename);
        } catch (Throwable $e) {
            Log::warning('Image optimization failed, storing original upload.', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return $this->storeOriginal($file, $directory, $basename);
        }
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

        if (! is_array($files)) {
            return [];
        }

        $paths = [];
        $slug = $nameSlug ? Str::slug($nameSlug) : '';
        $slug = rtrim(Str::limit(trim($slug, '-'), 80, ''), '-');

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            // Empty multi-file slots — skip.
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (! $file->isValid()) {
                throw new RuntimeException($this->uploadErrorMessage($file, (int) $index));
            }

            $basename = null;

            if ($slug !== '') {
                $candidate = ((int) $index === 0 && $paths === [])
                    ? $slug
                    : "{$slug}-".(count($paths) + 1);
                $basename = $this->uniqueBasename($directory, $candidate);
            }

            $paths[] = $this->storePublicImage($file, $directory, $basename, false);
        }

        return $paths;
    }

    private function optimizeAndStore(UploadedFile $file, string $directory, string $basename): string
    {
        $source = $this->loadImage($file);
        $width = imagesx($source);
        $height = imagesy($source);

        $main = $this->resizeCopy($source, $width, $height, $this->maxWidth, $this->maxHeight);
        $storedPath = $this->encodeImage($main, $directory, $basename, $this->quality);

        if ($main !== $source) {
            imagedestroy($main);
        }

        $thumb = $this->resizeCopy($source, $width, $height, $this->thumbWidth, $this->thumbWidth);
        $this->encodeImage($thumb, $directory.'/thumbs', $basename, $this->thumbQuality);

        if ($thumb !== $source) {
            imagedestroy($thumb);
        }

        imagedestroy($source);

        return $storedPath;
    }

    private function storeOriginal(UploadedFile $file, string $directory, string $basename): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? $extension : 'jpg';
        $relative = trim($directory, '/').'/'.$basename.'.'.$extension;

        Storage::disk('public')->put($relative, file_get_contents($file->getRealPath()));

        // Copy as thumb so listings still have a path to resolve.
        Storage::disk('public')->put(
            trim($directory, '/').'/thumbs/'.$basename.'.'.$extension,
            Storage::disk('public')->get($relative)
        );

        return $relative;
    }

    private function ensureDirectory(string $directory): void
    {
        Storage::disk('public')->makeDirectory($directory);

        $path = Storage::disk('public')->path($directory);

        if (! is_dir($path)) {
            if (! @mkdir($path, 0775, true) && ! is_dir($path)) {
                throw new RuntimeException("Could not create upload directory: {$directory}");
            }
        }

        if (! is_writable($path)) {
            @chmod($path, 0775);
        }

        if (! is_writable($path)) {
            throw new RuntimeException("Upload directory is not writable: {$directory}. Run ./fix-permissions.sh on the server.");
        }
    }

    /**
     * @return \GdImage|resource
     */
    private function loadImage(UploadedFile $file)
    {
        $path = $file->getRealPath();

        if ($path === false || ! is_readable($path)) {
            throw new RuntimeException('Uploaded image could not be read. Check PHP upload_max_filesize / post_max_size.');
        }

        $mime = $this->detectMime($file);

        $image = match (true) {
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg'), str_contains($mime, 'pjpeg') => @imagecreatefromjpeg($path),
            str_contains($mime, 'png') => @imagecreatefrompng($path),
            str_contains($mime, 'gif') => @imagecreatefromgif($path),
            str_contains($mime, 'webp') => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported or corrupt image. Use JPG, PNG, GIF, or WebP under 8MB.');
        }

        if (! imageistruecolor($image)) {
            @imagepalettetotruecolor($image);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function detectMime(UploadedFile $file): string
    {
        $mime = strtolower((string) ($file->getMimeType() ?: ''));
        $extension = strtolower($file->getClientOriginalExtension());

        if ($mime === '' || $mime === 'application/octet-stream' || $mime === 'text/plain') {
            $path = $file->getRealPath();
            if ($path && function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detected = finfo_file($finfo, $path);
                    finfo_close($finfo);
                    if (is_string($detected) && $detected !== '') {
                        $mime = strtolower($detected);
                    }
                }
            }
        }

        if ($mime === '' || $mime === 'application/octet-stream') {
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

    /**
     * @param  \GdImage|resource  $source
     * @return \GdImage|resource
     */
    private function resizeCopy($source, int $width, int $height, int $maxWidth, int $maxHeight)
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

        return $dest;
    }

    /**
     * @param  \GdImage|resource  $image
     */
    private function encodeImage($image, string $directory, string $basename, int $quality): string
    {
        $directory = trim($directory, '/');
        $this->ensureDirectory($directory);

        $webpRelative = $directory.'/'.$basename.'.webp';
        $webpFull = Storage::disk('public')->path($webpRelative);

        if (function_exists('imagewebp') && @imagewebp($image, $webpFull, $quality)) {
            @chmod($webpFull, 0644);

            return $webpRelative;
        }

        $jpegRelative = $directory.'/'.$basename.'.jpg';
        $jpegFull = Storage::disk('public')->path($jpegRelative);

        if (! @imagejpeg($image, $jpegFull, min($quality + 7, 90))) {
            throw new RuntimeException('Failed to save optimized image. Check storage permissions.');
        }

        @chmod($jpegFull, 0644);
        Log::warning('WebP unavailable, saved JPEG fallback.', ['path' => $jpegRelative]);

        return $jpegRelative;
    }

    /**
     * Human-readable message for a failed PHP upload.
     */
    public function describeUploadError(UploadedFile $file, int $index = 0): string
    {
        return $this->uploadErrorMessage($file, $index);
    }

    private function uploadErrorMessage(UploadedFile $file, int $index): string
    {
        $label = 'Image '.($index + 1);
        $uploadMax = ini_get('upload_max_filesize') ?: '2M';
        $postMax = ini_get('post_max_size') ?: '8M';

        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "{$label} is too large. Server limit is {$uploadMax} (post_max_size {$postMax}). Compress the image or raise PHP upload limits.",
            UPLOAD_ERR_PARTIAL => "{$label} was only partially uploaded. Please try again.",
            UPLOAD_ERR_NO_FILE => "{$label} was not uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "{$label} failed: missing temp folder on server.",
            UPLOAD_ERR_CANT_WRITE => "{$label} failed: cannot write to disk. Run ./fix-permissions.sh.",
            default => "{$label} failed to upload (error {$file->getError()}). Check server upload limits.",
        };
    }

    private function uniqueBasename(string $directory, string $basename): string
    {
        $candidate = Str::slug($basename, '-');
        $candidate = rtrim(Str::limit(trim($candidate, '-'), 80, ''), '-');

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

        foreach (['webp', 'jpg', 'jpeg', 'png', 'gif'] as $ext) {
            if (Storage::disk('public')->exists("{$directory}/{$basename}.{$ext}")
                || Storage::disk('public')->exists("{$directory}/thumbs/{$basename}.{$ext}")) {
                return true;
            }
        }

        return false;
    }
}
