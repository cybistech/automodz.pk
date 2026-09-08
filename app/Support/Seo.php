<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Seo
{
    public static function siteUrl(): string
    {
        return rtrim(config('site.url'), '/');
    }

    public static function absolute(?string $path): string
    {
        if (! $path) {
            return self::siteUrl();
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return self::siteUrl().'/'.ltrim($path, '/');
    }

    public static function description(?string $text, ?string $fallback = null): string
    {
        $text = trim(strip_tags($text ?: ''));

        if ($text === '') {
            $text = $fallback ?? config('site.description');
        }

        return Str::limit($text, 160, '…');
    }

    public static function title(string $text): string
    {
        return Str::limit(trim(strip_tags($text)), 60, '…');
    }

    public static function listingCanonical(Request $request): string
    {
        $params = array_filter([
            'category' => $request->query('category'),
            'page' => $request->integer('page') > 1 ? $request->integer('page') : null,
        ], fn ($value) => $value !== null && $value !== '');

        return route('products.index', $params);
    }

    public static function defaultOgImage(): string
    {
        return self::absolute(config('seo.default_og_image', '/images/og-image.jpg'));
    }

    /**
     * Resolve Open Graph / Twitter image metadata for a page.
     *
     * @param  string|null  $image  Absolute or site-relative path/URL (main product or banner)
     * @return array{url: string, alt: string, width: ?int, height: ?int, type: ?string}
     */
    public static function ogImage(?string $image = null, ?string $alt = null): array
    {
        $path = $image ?: config('seo.default_og_image', '/images/og-image.jpg');
        $url = self::absolute($path);
        $isDefault = ! $image || self::absolute($image) === self::defaultOgImage()
            || rtrim(parse_url($url, PHP_URL_PATH) ?: '', '/') === rtrim(config('seo.default_og_image'), '/');

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
        $type = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => $isDefault ? config('seo.default_og_image_type') : null,
        };

        return [
            'url' => $url,
            'alt' => $alt ?: (config('site.name').' — '.config('site.tagline')),
            'width' => $isDefault ? (int) config('seo.default_og_image_width', 1200) : null,
            'height' => $isDefault ? (int) config('seo.default_og_image_height', 630) : null,
            'type' => $type,
        ];
    }

    /** First / main homepage banner (stable, not shuffled). */
    public static function mainBanner(): array
    {
        $slides = config('seo.hero_slides', []);
        $first = $slides[0] ?? [
            'src' => config('seo.default_og_image', '/images/og-image.jpg'),
            'alt' => config('site.name').' — '.config('site.tagline'),
        ];

        return [
            'src' => $first['src'],
            'alt' => $first['alt'] ?? (config('site.name').' — '.config('site.tagline')),
        ];
    }

    /** @return list<array{src: string, alt: string}> */
    public static function heroSlides(bool $shuffle = true): array
    {
        $slides = array_values(config('seo.hero_slides', []));

        if ($shuffle && count($slides) > 1) {
            shuffle($slides);
        }

        return $slides;
    }
}
