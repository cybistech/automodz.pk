<?php

namespace App\Services;

use App\Models\Product;
use App\Support\UploadLimits;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductImageService
{
    public function __construct(private ImageOptimizer $imageOptimizer) {}

    /**
     * Detect PHP-level upload failures before Laravel validation runs.
     *
     * @throws ValidationException
     */
    public function assertUploadsReachable(Request $request): void
    {
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);
        $postMax = UploadLimits::postMaxBytes();

        if ($contentLength > 0 && $postMax > 0 && $contentLength > $postMax) {
            throw ValidationException::withMessages([
                'images' => 'The upload is too large for the server (post_max_size is '
                    .UploadLimits::humanPostMax().'). Reduce image sizes or upload fewer files at once.',
            ]);
        }

        // When post_max_size is exceeded PHP empties $_POST and $_FILES entirely.
        if ($contentLength > 0 && $postMax > 0 && $contentLength > $postMax * 0.9 && ! $request->hasFile('images') && ! $request->filled('name')) {
            throw ValidationException::withMessages([
                'images' => 'Upload rejected by the server. Total request size exceeds '
                    .UploadLimits::humanPostMax().'. Try smaller images.',
            ]);
        }

        foreach ($this->collectFiles($request) as $index => $file) {
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (! $file->isValid()) {
                throw ValidationException::withMessages([
                    'images' => $this->imageOptimizer->describeUploadError($file, $index),
                ]);
            }
        }
    }

    /**
     * Keep / reorder / delete existing images, upload new files, put main image first.
     *
     * @return list<string>
     */
    public function sync(Request $request, ?Product $product, string $productName): array
    {
        $current = array_values($product?->images ?? []);
        $kept = $this->resolveKeptPaths($request, $current);

        if ($product) {
            $removed = array_values(array_diff($current, $kept));
            $this->delete($removed);
        }

        $uploaded = $this->upload($request, $productName);
        $images = array_values(array_unique([...$kept, ...$uploaded]));

        return $this->applyPrimaryOrder($images, $request->input('primary_image'));
    }

    /**
     * @return list<string>
     */
    public function upload(Request $request, string $productName): array
    {
        $files = $this->collectFiles($request);

        if ($files === []) {
            return [];
        }

        try {
            return $this->imageOptimizer->storePublicImages($files, 'products', $productName);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Product image upload failed', [
                'product' => $productName,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'images' => 'Image upload failed: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * @param  list<string>  $paths
     */
    public function delete(array $paths): void
    {
        foreach ($paths as $image) {
            if (! is_string($image) || $image === '') {
                continue;
            }

            Storage::disk('public')->delete($image);
            Storage::disk('public')->delete(Product::thumbPathFor($image));
        }
    }

    /**
     * @param  list<string>  $current
     * @return list<string>
     */
    private function resolveKeptPaths(Request $request, array $current): array
    {
        $kept = [];

        foreach ($request->input('existing_images', []) as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            if (in_array($path, $current, true) && ! in_array($path, $kept, true)) {
                $kept[] = $path;
            }
        }

        return $kept;
    }

    /**
     * @param  list<string>  $images
     * @return list<string>
     */
    private function applyPrimaryOrder(array $images, mixed $primary): array
    {
        if (! is_string($primary) || $primary === '' || ! in_array($primary, $images, true)) {
            return $images;
        }

        return array_values(array_unique([
            $primary,
            ...array_filter($images, fn (string $path) => $path !== $primary),
        ]));
    }

    /**
     * @return list<UploadedFile>
     */
    private function collectFiles(Request $request): array
    {
        $files = $request->file('images');

        if ($files === null) {
            return [];
        }

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        return array_values(array_filter(
            $files,
            fn ($file) => $file instanceof UploadedFile && $file->getError() !== UPLOAD_ERR_NO_FILE,
        ));
    }
}
