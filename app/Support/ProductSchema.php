<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Collection;

class ProductSchema
{
    /**
     * Build Google Merchant / Product rich-result compatible JSON-LD.
     *
     * @param  Collection<int, ProductReview>|null  $reviews
     * @return array<string, mixed>
     */
    public static function forProduct(Product $product, ?Collection $reviews = null): array
    {
        $reviews ??= $product->relationLoaded('approvedReviews')
            ? $product->approvedReviews
            : $product->approvedReviews()->latest()->take(20)->get();

        $images = collect($product->images ?? [])
            ->map(fn ($path) => Seo::absolute($product->imageUrl($path)))
            ->filter()
            ->values()
            ->all();

        $url = route('products.show', $product->slug);
        $price = number_format($product->effective_price, 2, '.', '');
        $inStock = $product->isInStock();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            '@id' => $url.'#product',
            'name' => $product->name,
            'description' => Seo::description(
                $product->meta_description ?: $product->short_description ?: $product->description,
                $product->name
            ),
            'sku' => $product->sku,
            'mpn' => $product->part_number ?: $product->sku,
            'url' => $url,
            'image' => $images ?: [Seo::defaultOgImage()],
            'category' => $product->category?->name,
            'itemCondition' => match ($product->condition) {
                'used' => 'https://schema.org/UsedCondition',
                'refurbished' => 'https://schema.org/RefurbishedCondition',
                default => 'https://schema.org/NewCondition',
            },
            'offers' => [
                '@type' => 'Offer',
                '@id' => $url.'#offer',
                'url' => $url,
                'priceCurrency' => 'PKR',
                'price' => $price,
                'priceValidUntil' => now()->addYear()->toDateString(),
                'availability' => $inStock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => match ($product->condition) {
                    'used' => 'https://schema.org/UsedCondition',
                    'refurbished' => 'https://schema.org/RefurbishedCondition',
                    default => 'https://schema.org/NewCondition',
                },
                'seller' => [
                    '@type' => 'Organization',
                    'name' => config('site.name'),
                    'url' => Seo::siteUrl(),
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'PK',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 7,
                    'returnMethod' => 'https://schema.org/ReturnByMail',
                    'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility',
                ],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingDestination' => [
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'PK',
                    ],
                    'deliveryTime' => [
                        '@type' => 'ShippingDeliveryTime',
                        'handlingTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 1,
                            'maxValue' => 2,
                            'unitCode' => 'DAY',
                        ],
                        'transitTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 2,
                            'maxValue' => 7,
                            'unitCode' => 'DAY',
                        ],
                    ],
                    'shippingRate' => [
                        '@type' => 'MonetaryAmount',
                        'value' => '0',
                        'currency' => 'PKR',
                    ],
                ],
            ],
        ];

        if ($product->brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand,
            ];
        }

        if ($product->weight) {
            $schema['weight'] = [
                '@type' => 'QuantitativeValue',
                'value' => (float) $product->weight,
                'unitCode' => 'KGM',
            ];
        }

        $ratingCount = (int) $product->rating_count;

        if ($ratingCount > 0 && (float) $product->rating_avg > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format((float) $product->rating_avg, 1, '.', ''),
                'bestRating' => '5',
                'worstRating' => '1',
                'ratingCount' => $ratingCount,
                'reviewCount' => $ratingCount,
            ];
        }

        if ($reviews->isNotEmpty()) {
            $schema['review'] = $reviews->map(function (ProductReview $review) {
                $item = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $review->author_name,
                    ],
                    'datePublished' => $review->created_at?->toDateString(),
                    'reviewBody' => $review->body,
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => (string) $review->rating,
                        'bestRating' => '5',
                        'worstRating' => '1',
                    ],
                ];

                if ($review->title) {
                    $item['name'] = $review->title;
                }

                return $item;
            })->values()->all();
        }

        return array_filter($schema, fn ($value) => $value !== null && $value !== []);
    }
}
