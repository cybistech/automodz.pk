<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageOptimizer
{
    public function __construct(
        private int $maxWidth = 1600,
        private int $maxHeight = 1600,
        private int $quality = 85,
    ) {}

    public function storePublicImage(UploadedFile $file, string $directory): string
    {
        $source = $this->loadImage($file);
        $width = imagesx($source);
        $height = imagesy($source);
        $image = $this->resize($source, $width, $height);

        $filename = trim($directory, '/').'/'.Str::uuid().'.webp';
        Storage::disk('public')->makeDirectory(trim($directory, '/'));

        $path = Storage::disk('public')->path($filename);

        if (! imagewebp($image, $path, $this->quality)) {
            imagedestroy($image);
            throw new RuntimeException('Failed to encode image as WebP.');
        }

        imagedestroy($image);

        return $filename;
    }

    private function loadImage(UploadedFile $file): GdImage
    {
        $path = $file->getRealPath();

        $image = match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported or corrupt image.');
        }

        return $image;
    }

    private function resize(GdImage $source, int $width, int $height): GdImage
    {
        if ($width <= $this->maxWidth && $height <= $this->maxHeight) {
            imagesavealpha($source, true);

            return $source;
        }

        $ratio = min($this->maxWidth / $width, $this->maxHeight / $height);
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
}
