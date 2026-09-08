<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(private SitemapService $sitemap) {}

    public function index(): Response
    {
        return $this->xml('sitemap.index', [
            'sitemaps' => [
                ['loc' => route('sitemap.pages'), 'lastmod' => now()],
                ['loc' => route('sitemap.categories'), 'lastmod' => now()],
                ['loc' => route('sitemap.products'), 'lastmod' => now()],
            ],
        ]);
    }

    public function pages(): Response
    {
        return $this->xml('sitemap.urlset', ['urls' => $this->sitemap->pages()]);
    }

    public function categories(): Response
    {
        return $this->xml('sitemap.urlset', ['urls' => $this->sitemap->categories()]);
    }

    public function products(): Response
    {
        return $this->xml('sitemap.urlset', ['urls' => $this->sitemap->products()]);
    }

    public function html()
    {
        $data = $this->sitemap->htmlData();

        return view('shop.sitemap', $data);
    }

    public function robots(): Response
    {
        $content = view('seo.robots', [
            'siteUrl' => $this->sitemap->baseUrl(),
            'disallowed' => config('seo.disallowed_paths', []),
            'aiCrawlers' => config('seo.ai_crawlers', []),
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function llms(): Response
    {
        $content = view('seo.llms', [
            'siteUrl' => $this->sitemap->baseUrl(),
            'siteName' => config('site.name'),
            'description' => config('site.description'),
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function xml(string $view, array $data): Response
    {
        return response()
            ->view($view, $data)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
