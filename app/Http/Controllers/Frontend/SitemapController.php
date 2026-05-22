<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $urls[] = $this->entry(route('frontend.home'), now(), 'daily', '1.0');
        $urls[] = $this->entry(route('frontend.shop.index'), now(), 'daily', '0.9');
        $urls[] = $this->entry(route('frontend.categories.index'), now(), 'weekly', '0.8');
        $urls[] = $this->entry(route('frontend.blog.index'), now(), 'daily', '0.9');

        Category::active()->root()->get()->each(function ($category) use (&$urls) {
            $urls[] = $this->entry(
                route('frontend.category.show', $category->slug),
                $category->updated_at,
                'weekly',
                '0.7'
            );
        });

        BlogCategory::where('is_active', true)->get()->each(function ($category) use (&$urls) {
            if ($category->is_indexable ?? true) {
                $urls[] = $this->entry(
                    route('frontend.blog.category', $category->slug),
                    $category->updated_at,
                    'weekly',
                    '0.6'
                );
            }
        });

        BlogPost::published()
            ->where('is_indexable', true)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at', 'published_at'])
            ->each(function ($post) use (&$urls) {
                $urls[] = $this->entry(
                    route('frontend.blog.show', $post->slug),
                    $post->updated_at ?? $post->published_at,
                    'weekly',
                    '0.8'
                );
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "\nSitemap: {$sitemapUrl}\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    protected function entry(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        $date = $lastmod ? (\Carbon\Carbon::parse($lastmod)->toAtomString()) : now()->toAtomString();

        return [
            'loc' => $loc,
            'lastmod' => $date,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
