<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageService;
use App\Support\ShopCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(private ProductImageService $productImages) {}

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

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->productAttributes();
        $data['slug'] = Str::slug($data['name']);
        $imageResult = $this->productImages->syncWithDebug($request, null, $data['name']);
        $data['images'] = $imageResult['images'];
        $data['video_path'] = $this->handleVideo($request);
        $data['specifications'] = $this->parseSpecifications($request);

        Product::create($data);
        ShopCache::flush();

        return $this->redirectAfterImageSync('Product created successfully.', $imageResult['debug']);
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->productAttributes();
        $data['slug'] = Str::slug($data['name']);
        $data['specifications'] = $this->parseSpecifications($request);
        $imageResult = $this->productImages->syncWithDebug($request, $product, $data['name']);
        $data['images'] = $imageResult['images'];

        if ($request->hasFile('video_file')) {
            if ($product->video_path) {
                Storage::disk('public')->delete($product->video_path);
            }
            $data['video_path'] = $this->handleVideo($request);
        }

        $product->update($data);
        ShopCache::flush();

        return $this->redirectAfterImageSync('Product updated successfully.', $imageResult['debug']);
    }

    public function destroy(Product $product)
    {
        $this->productImages->delete($product->images ?? []);

        if ($product->video_path) {
            Storage::disk('public')->delete($product->video_path);
        }

        $product->delete();
        ShopCache::flush();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function handleVideo(Request $request): ?string
    {
        if (! $request->hasFile('video_file')) {
            return null;
        }

        return $request->file('video_file')->store('products/videos', 'public');
    }

    /**
     * @param  array<string, mixed>  $debug
     */
    private function redirectAfterImageSync(string $message, array $debug): RedirectResponse
    {
        $redirect = redirect()->route('admin.products.index')->with('success', $message);

        if ($warnings = ($debug['warnings'] ?? [])) {
            $redirect->with('warning', implode(' ', $warnings));
        }

        if (config('app.debug_uploads')) {
            $redirect->with('upload_debug', $debug);
        }

        return $redirect;
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
