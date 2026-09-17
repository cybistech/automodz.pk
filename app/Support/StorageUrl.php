<?php

namespace App\Support;

class StorageUrl
{
    public static function public(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        // media.php avoids cPanel 403 on /uploads/ when Apache blocks direct file access.
        return '/media.php?p='.rawurlencode($path);
    }
}
