<?php

namespace App\Http\Requests\Admin;

use App\Services\ProductImageService;
use App\Support\UploadLimits;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        app(ProductImageService::class)->assertUploadsReachable($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;
        $maxKb = UploadLimits::effectiveMaxUploadKb();

        $skuRule = 'required|string|max:100|unique:products,sku';
        if ($productId) {
            $skuRule .= ','.$productId;
        }

        return [
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
            'images' => 'nullable|array|max:20',
            'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:'.$maxKb,
            'existing_images' => 'nullable|array|max:20',
            'existing_images.*' => 'nullable|string|max:500',
            'primary_image' => 'nullable|string|max:500',
            'video_file' => 'nullable|mimes:mp4,webm,mov|max:51200',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxMb = UploadLimits::effectiveMaxUploadMb();

        return [
            'images.*.image' => 'Each file must be a valid image (JPG, PNG, GIF, or WebP).',
            'images.*.max' => "Each image must be {$maxMb}MB or smaller.",
            'images.max' => 'You can upload up to 20 images per product.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function productAttributes(): array
    {
        $data = $this->validated();
        unset($data['images'], $data['existing_images'], $data['primary_image'], $data['video_file']);

        $data['is_featured'] = $this->boolean('is_featured');
        $data['is_active'] = $this->boolean('is_active', true);
        $data['warranty'] = null;

        return $data;
    }
}
