<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'brand',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'price',
        'sale_price',
        'stock',
        'condition',
        'part_number',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year_from',
        'vehicle_year_to',
        'warranty',
        'weight',
        'specifications',
        'images',
        'video_url',
        'video_path',
        'is_featured',
        'is_active',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'specifications' => 'array',
            'images' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $images = $this->images ?? [];

        return $images[0] ?? null;
    }

    public function getVideoSourceAttribute(): ?string
    {
        if ($this->video_path) {
            return asset('storage/'.$this->video_path);
        }

        return $this->video_url;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->sale_price || $this->sale_price >= $this->price) {
            return null;
        }

        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }
}
