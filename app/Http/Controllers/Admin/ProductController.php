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
        $data['images'] = $this->handleImages($request);
        $data['video_path'] = $this->handleVideo($request);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['specifications'] = $this->parseSpecifications($request);

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

        $newImages = $this->handleImages($request);
        if (! empty($newImages)) {
            foreach ($product->images ?? [] as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            $data['images'] = $newImages;
        }

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
        foreach ($product->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }

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
            'warranty' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'video_file' => 'nullable|mimes:mp4,webm,mov|max:51200',
        ]);
    }

    private function handleImages(Request $request): array
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        $paths = [];
        foreach ($request->file('images') as $image) {
            $paths[] = $this->imageOptimizer->storePublicImage($image, 'products');
        }

        return $paths;
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
