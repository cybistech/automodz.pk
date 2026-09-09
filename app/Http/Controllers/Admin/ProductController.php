<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageOptimizer;
use App\Support\ShopCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductController extends Controller
{
    public function __construct(private ImageOptimizer $imageOptimizer) {}

    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $data['slug'] = Str::slug($data['name']);
        $data['images'] = $this->syncImages($request, null, $data['name']);
        $data['video_path'] = $this->handleVideo($request);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['specifications'] = $this->parseSpecifications($request);
        $data['warranty'] = null;

        Product::create($data);
        ShopCache::flush();

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);
        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['specifications'] = $this->parseSpecifications($request);
        $data['warranty'] = null;
        $data['images'] = $this->syncImages($request, $product, $data['name']);

        if ($request->hasFile('video_file')) {
            if ($product->video_path) {
                Storage::disk('public')->delete($product->video_path);
            }
            $data['video_path'] = $this->handleVideo($request);
        }

        $product->update($data);
        ShopCache::flush();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->deleteProductImages($product->images ?? []);

        if ($product->video_path) {
            Storage::disk('public')->delete($product->video_path);
        }

        $product->delete();
        ShopCache::flush();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validateProduct(Request $request, ?int $productId = null): array
    {
        $skuRule = 'required|string|max:100|unique:products,sku';
        if ($productId) {
            $skuRule .= ','.$productId;
        }

        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => $skuRule,
            'brand' => 'nullable|string|max:100',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|in:new,used,refurbished',
            'part_number' => 'nullable|string|max:100',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_year_from' => 'nullable|string|max:4',
            'vehicle_year_to' => 'nullable|string|max:4',
            'weight' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:8192',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|string|max:500',
            'primary_image' => 'nullable|string|max:500',
            'video_file' => 'nullable|mimes:mp4,webm,mov|max:51200',
        ]);
    }

    /**
     * Keep / reorder / delete existing images, append new uploads, and put the main image first.
     *
     * @return list<string>
     */
    private function syncImages(Request $request, ?Product $product, string $productName): array
    {
        $current = array_values($product?->images ?? []);
        $kept = [];

        foreach ($request->input('existing_images', []) as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            if (in_array($path, $current, true) && ! in_array($path, $kept, true)) {
                $kept[] = $path;
            }
        }

        // On create there are no existing images; on update, omitted paths are deleted.
        if ($product) {
            $removed = array_values(array_diff($current, $kept));
            $this->deleteProductImages($removed);
        }

        $uploaded = $this->handleImages($request, $productName);
        $images = array_values(array_unique([...$kept, ...$uploaded]));

        $primary = $this->resolvePrimaryImagePath($request->input('primary_image'), $uploaded);

        if (is_string($primary) && $primary !== '' && in_array($primary, $images, true)) {
            $images = array_values(array_unique([
                $primary,
                ...array_filter($images, fn (string $path) => $path !== $primary),
            ]));
        }

        return $images;
    }

    /**
     * Map primary_image=new:0 style tokens (pending uploads) to stored paths.
     *
     * @param  list<string>  $uploaded
     */
    private function resolvePrimaryImagePath(mixed $primary, array $uploaded): mixed
    {
        if (! is_string($primary) || ! str_starts_with($primary, 'new:')) {
            return $primary;
        }

        $index = (int) substr($primary, 4);

        return $uploaded[$index] ?? $primary;
    }

    private function handleImages(Request $request, ?string $productName = null): array
    {
        $files = $request->file('images');

        if ($files === null) {
            $this->guardAgainstMissingUploads($request);

            return [];
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        try {
            return $this->imageOptimizer->storePublicImages(
                $files,
                'products',
                $productName ?? $request->input('name'),
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Product image upload failed', [
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'images' => 'Image upload failed: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Detect when the browser sent a multipart body but PHP dropped file fields
     * (common when post_max_size or upload_max_filesize is exceeded).
     */
    private function guardAgainstMissingUploads(Request $request): void
    {
        $contentLength = (int) ($request->server('CONTENT_LENGTH') ?? 0);
        $contentType = strtolower((string) ($request->server('CONTENT_TYPE') ?? ''));

        if ($contentLength < 1024 || ! str_contains($contentType, 'multipart/form-data')) {
            return;
        }

        throw ValidationException::withMessages([
            'images' => 'The image upload did not reach the server. The file may exceed PHP upload limits '
                .'(upload_max_filesize='.(ini_get('upload_max_filesize') ?: 'unknown')
                .', post_max_size='.(ini_get('post_max_size') ?: 'unknown').'). '
                .'Raise limits in .user.ini / MultiPHP INI Editor, then try again.',
        ]);
    }

    private function deleteProductImages(array $images): void
    {
        foreach ($images as $image) {
            if (! is_string($image) || $image === '') {
                continue;
            }

            Storage::disk('public')->delete($image);
            Storage::disk('public')->delete(Product::thumbPathFor($image));
        }
    }

    private function handleVideo(Request $request): ?string
    {
        if (! $request->hasFile('video_file')) {
            return null;
        }

        return $request->file('video_file')->store('products/videos', 'public');
    }

    private function parseSpecifications(Request $request): ?array
    {
        if (! $request->filled('spec_keys') || ! $request->filled('spec_values')) {
            return null;
        }

        $specs = [];
        foreach ($request->spec_keys as $index => $key) {
            $value = $request->spec_values[$index] ?? null;
            if ($key && $value) {
                $specs[$key] = $value;
            }
        }

        return empty($specs) ? null : $specs;
    }
}
