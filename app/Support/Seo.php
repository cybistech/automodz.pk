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
        return self::absolute(config('seo.default_og_image', '/images/logo.svg'));
    }
}
