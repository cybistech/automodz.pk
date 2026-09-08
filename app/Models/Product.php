<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\StorageUrl;
use Illuminate\Support\Facades\Storage;
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
            'rating_avg' => 'float',
            'rating_count' => 'integer',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->approved()->latest();
    }

    public function refreshRatingStats(): void
    {
        $stats = $this->reviews()->approved()
            ->selectRaw('COUNT(*) as aggregate_count, COALESCE(AVG(rating), 0) as aggregate_avg')
            ->first();

        $this->forceFill([
            'rating_count' => (int) ($stats->aggregate_count ?? 0),
            'rating_avg' => round((float) ($stats->aggregate_avg ?? 0), 2),
        ])->save();
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

    public function imageUrl(?string $path = null, bool $thumb = false): ?string
    {
        $path ??= $this->primary_image;

        if (! $path) {
            return null;
        }

        if ($thumb) {
            $thumbPath = self::thumbPathFor($path);

            if (Storage::disk('public')->exists($thumbPath)) {
                return StorageUrl::public($thumbPath);
            }
        }

        return StorageUrl::public($path);
    }

    public function imageAlt(int $index = 0): string
    {
        $alt = trim($this->name);

        if ($index > 0) {
            $alt .= ' — photo '.($index + 1);
        }

        return $alt;
    }

    public static function thumbPathFor(string $path): string
    {
        $directory = trim(dirname($path), '/');
        $filename = basename($path);

        if (str_ends_with($directory, '/thumbs')) {
            return $directory.'/'.$filename;
        }

        return $directory.'/thumbs/'.$filename;
    }

    public function getVideoSourceAttribute(): ?string
    {
        if ($this->video_path) {
            return StorageUrl::public($this->video_path);
        }

        return $this->video_url;
    }

    public function videoEmbedUrl(): ?string
    {
        $url = $this->video_url;

        if (! is_string($url) || $url === '') {
            return null;
        }

        if (str_contains($url, 'youtube.com/watch')) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

            return isset($params['v']) && $params['v'] !== ''
                ? 'https://www.youtube.com/embed/'.$params['v']
                : $url;
        }

        if (str_contains($url, 'youtu.be/')) {
            $id = basename((string) parse_url($url, PHP_URL_PATH));

            return $id !== '' ? 'https://www.youtube.com/embed/'.$id : $url;
        }

        if (str_contains($url, 'vimeo.com/')) {
            $id = basename((string) parse_url($url, PHP_URL_PATH));

            return $id !== '' ? 'https://player.vimeo.com/video/'.$id : $url;
        }

        return $url;
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

    public function scopeForListing($query)
    {
        return $query->select([
            'id',
            'category_id',
            'name',
            'slug',
            'sku',
            'brand',
            'price',
            'sale_price',
            'stock',
            'images',
            'is_featured',
            'is_active',
            'rating_avg',
            'rating_count',
            'created_at',
        ]);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->sale_price || $this->sale_price >= $this->price) {
            return null;
        }

        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }
}
