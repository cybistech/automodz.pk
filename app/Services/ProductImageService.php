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

/**
 * @phpstan-type UploadDebug array{
 *     product_id: int|null,
 *     product_name: string,
 *     content_length: int,
 *     post_max_bytes: int,
 *     upload_max_bytes: int,
 *     content_type: string|null,
 *     images_attempted: int,
 *     has_file_images: bool,
 *     collected_file_count: int,
 *     raw_files: list<array{name: string|null, size: int, error: int, error_label: string}>,
 *     incoming_files: list<array{name: string, size: int, mime: string|null, error: int, valid: bool}>,
 *     existing_images_count: int,
 *     kept_count: int,
 *     removed_count: int,
 *     uploaded_count: int,
 *     uploaded_paths: list<string>,
 *     final_images: list<string>,
 *     disk_checks: array<string, mixed>,
 *     path_checks: list<array{path: string, main_exists: bool, thumb_exists: bool, main_bytes: int|null, thumb_bytes: int|null}>,
 *     warnings: list<string>,
 * }
 */

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
        return $this->syncWithDebug($request, $product, $productName)['images'];
    }

    /**
     * @return array{images: list<string>, debug: UploadDebug}
     */
    public function syncWithDebug(Request $request, ?Product $product, string $productName): array
    {
        $debug = $this->buildRequestDiagnostics($request, $product, $productName);

        $current = array_values($product?->images ?? []);
        $kept = $this->resolveKeptPaths($request, $current);
        $debug['existing_images_count'] = count($request->input('existing_images', []));
        $debug['kept_count'] = count($kept);

        $removed = [];
        if ($product) {
            $removed = array_values(array_diff($current, $kept));
            $this->delete($removed);
        }
        $debug['removed_count'] = count($removed);

        $uploaded = $this->upload($request, $productName);
        $debug['uploaded_count'] = count($uploaded);
        $debug['uploaded_paths'] = $uploaded;

        $images = array_values(array_unique([...$kept, ...$uploaded]));
        $images = $this->applyPrimaryOrder($images, $request->input('primary_image'));
        $debug['final_images'] = $images;
        $debug['path_checks'] = $this->verifyStoredPaths($images);
        $debug['warnings'] = $this->collectWarnings($debug);

        Log::info('Product image sync', $debug);

        return ['images' => $images, 'debug' => $debug];
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
     * @return UploadDebug
     */
    private function buildRequestDiagnostics(Request $request, ?Product $product, string $productName): array
    {
        $files = $this->collectFiles($request);

        return [
            'product_id' => $product?->id,
            'product_name' => $productName,
            'content_length' => (int) $request->server('CONTENT_LENGTH', 0),
            'post_max_bytes' => UploadLimits::postMaxBytes(),
            'upload_max_bytes' => UploadLimits::uploadMaxBytes(),
            'content_type' => $request->header('Content-Type'),
            'images_attempted' => (int) $request->input('images_attempted', 0),
            'has_file_images' => $request->hasFile('images'),
            'collected_file_count' => count($files),
            'raw_files' => $this->describeRawFiles($request),
            'incoming_files' => array_map(
                fn (UploadedFile $file, int $index) => [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize() ?: 0,
                    'mime' => $file->getClientMimeType(),
                    'error' => $file->getError(),
                    'valid' => $file->isValid(),
                ],
                $files,
                array_keys($files),
            ),
            'existing_images_count' => 0,
            'kept_count' => 0,
            'removed_count' => 0,
            'uploaded_count' => 0,
            'uploaded_paths' => [],
            'final_images' => [],
            'disk_checks' => $this->diskChecks(),
            'path_checks' => [],
            'warnings' => [],
        ];
    }

    /**
     * @return list<array{name: string|null, size: int, error: int, error_label: string}>
     */
    private function describeRawFiles(Request $request): array
    {
        $raw = $_FILES['images'] ?? null;

        if (! is_array($raw)) {
            return [];
        }

        $names = $raw['name'] ?? null;
        $sizes = $raw['size'] ?? null;
        $errors = $raw['error'] ?? null;

        if (! is_array($names)) {
            return [[
                'name' => is_string($names) ? $names : null,
                'size' => is_int($sizes) ? $sizes : (int) ($sizes ?? 0),
                'error' => is_int($errors) ? $errors : (int) ($errors ?? UPLOAD_ERR_NO_FILE),
                'error_label' => $this->uploadErrorLabel(is_int($errors) ? $errors : (int) ($errors ?? UPLOAD_ERR_NO_FILE)),
            ]];
        }

        $described = [];
        foreach ($names as $index => $name) {
            $error = is_array($errors) ? (int) ($errors[$index] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;
            $described[] = [
                'name' => is_string($name) ? $name : null,
                'size' => is_array($sizes) ? (int) ($sizes[$index] ?? 0) : 0,
                'error' => $error,
                'error_label' => $this->uploadErrorLabel($error),
            ];
        }

        return $described;
    }

    /**
     * @return array<string, mixed>
     */
    private function diskChecks(): array
    {
        $disk = Storage::disk('public');
        $productsDir = $disk->path('products');
        $thumbsDir = $disk->path('products/thumbs');
        $uploadsLink = base_path('uploads');

        return [
            'public_disk_root' => $disk->path(''),
            'products_dir' => $productsDir,
            'products_dir_exists' => is_dir($productsDir),
            'products_dir_writable' => is_dir($productsDir) && is_writable($productsDir),
            'thumbs_dir_exists' => is_dir($thumbsDir),
            'thumbs_dir_writable' => is_dir($thumbsDir) && is_writable($thumbsDir),
            'uploads_link_exists' => file_exists($uploadsLink),
            'uploads_link_is_link' => is_link($uploadsLink),
            'uploads_link_target' => is_link($uploadsLink) ? readlink($uploadsLink) : null,
        ];
    }

    /**
     * @param  list<string>  $paths
     * @return list<array{path: string, main_exists: bool, thumb_exists: bool, main_bytes: int|null, thumb_bytes: int|null}>
     */
    private function verifyStoredPaths(array $paths): array
    {
        $disk = Storage::disk('public');
        $checks = [];

        foreach ($paths as $path) {
            $thumb = Product::thumbPathFor($path);
            $checks[] = [
                'path' => $path,
                'main_exists' => $disk->exists($path),
                'thumb_exists' => $disk->exists($thumb),
                'main_bytes' => $disk->exists($path) ? $disk->size($path) : null,
                'thumb_bytes' => $disk->exists($thumb) ? $disk->size($thumb) : null,
            ];
        }

        return $checks;
    }

    /**
     * @param  UploadDebug  $debug
     * @return list<string>
     */
    private function collectWarnings(array $debug): array
    {
        $warnings = [];

        if ($debug['images_attempted'] > 0 && $debug['collected_file_count'] === 0) {
            $warnings[] = "Browser reported {$debug['images_attempted']} new image(s) but PHP received none. Check post_max_size ("
                .UploadLimits::humanPostMax().') and upload_max_filesize on PHP-FPM/Apache (not just CLI).';
        }

        if ($debug['content_length'] > 0 && $debug['post_max_bytes'] > 0 && $debug['content_length'] > $debug['post_max_bytes']) {
            $warnings[] = 'Request body exceeds post_max_size ('.UploadLimits::humanPostMax().').';
        }

        foreach ($debug['path_checks'] as $check) {
            if (! $check['main_exists']) {
                $warnings[] = "Image file missing on disk: {$check['path']}";

                continue;
            }

            if (! $check['thumb_exists']) {
                $warnings[] = 'Thumbnail missing on disk: '.Product::thumbPathFor($check['path']);
            }
        }

        if (($debug['disk_checks']['products_dir_writable'] ?? false) === false) {
            $warnings[] = 'products upload directory is not writable by PHP.';
        }

        return $warnings;
    }

    private function uploadErrorLabel(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_OK => 'OK',
            UPLOAD_ERR_INI_SIZE => 'INI_SIZE',
            UPLOAD_ERR_FORM_SIZE => 'FORM_SIZE',
            UPLOAD_ERR_PARTIAL => 'PARTIAL',
            UPLOAD_ERR_NO_FILE => 'NO_FILE',
            UPLOAD_ERR_NO_TMP_DIR => 'NO_TMP_DIR',
            UPLOAD_ERR_CANT_WRITE => 'CANT_WRITE',
            UPLOAD_ERR_EXTENSION => 'EXTENSION',
            default => 'UNKNOWN',
        };
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
