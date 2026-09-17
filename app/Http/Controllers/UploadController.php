<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        abort_if($path === '' || str_contains($path, '..'), 404);

        $absolute = $this->resolveAbsolutePath($path);

        abort_unless($absolute !== null, 404);

        return response()->file($absolute, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    private function resolveAbsolutePath(string $path): ?string
    {
        $disk = Storage::disk('public');
        $root = realpath($disk->path(''));

        if ($root && $disk->exists($path)) {
            $real = realpath($disk->path($path));

            if ($real && str_starts_with($real, $root.DIRECTORY_SEPARATOR)) {
                return $real;
            }
        }

        $legacyRoot = realpath(storage_path('app/public'));
        $legacyAbsolute = storage_path('app/public/'.$path);
        $legacyReal = is_file($legacyAbsolute) ? realpath($legacyAbsolute) : false;

        if ($legacyRoot && $legacyReal && str_starts_with($legacyReal, $legacyRoot.DIRECTORY_SEPARATOR)) {
            Log::debug('Serving upload from legacy storage/app/public path.', ['path' => $path]);

            return $legacyReal;
        }

        return null;
    }
}
