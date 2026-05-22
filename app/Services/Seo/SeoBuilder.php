<?php

namespace App\Services\Seo;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoBuilder
{
    public static function absoluteUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return url($path);
    }

    public static function resolveImage(?string $path, bool $useBlogHelper = true): ?string
    {
        if (empty($path)) {
            return self::defaultOgImage();
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if ($useBlogHelper && function_exists('blog_image_url')) {
            return blog_image_url($path);
        }

        return self::absoluteUrl($path);
    }

    public static function defaultOgImage(): string
    {
        $image = config('seo.default_og_image', '/frontend/assets/images/logo.png');

        return self::absoluteUrl($image) ?? asset('frontend/assets/images/logo.png');
    }

    public static function resolveRobots(
        ?string $robotsMeta,
        ?bool $isIndexable = true,
        ?bool $isFollowable = true
    ): string {
        if (!empty($robotsMeta)) {
            return $robotsMeta;
        }

        $index = ($isIndexable ?? true) ? 'index' : 'noindex';
        $follow = ($isFollowable ?? true) ? 'follow' : 'nofollow';

        return "{$index},{$follow}";
    }

    public static function truncate(?string $text, int $length = 160): ?string
    {
        if (empty($text)) {
            return null;
        }

        $plain = trim(strip_tags($text));

        return Str::limit($plain, $length, '…');
    }

    public static function forPost(BlogPost $post): SeoData
    {
        $canonical = $post->canonical_url ?: $post->url;
        $description = self::truncate($post->meta_description ?: $post->excerpt, 160);
        $ogImage = self::resolveImage($post->og_image ?: $post->featured_image);
        $twitterImage = self::resolveImage($post->twitter_image ?: $post->og_image ?: $post->featured_image);
        $schemaImage = self::resolveImage($post->schema_image ?: $post->featured_image);

        $jsonLd = [
            self::blogPostingSchema($post, $schemaImage, $canonical),
        ];

        if (!empty($post->breadcrumb_schema) && is_array($post->breadcrumb_schema)) {
            $jsonLd[] = $post->breadcrumb_schema;
        } else {
            $jsonLd[] = self::breadcrumbSchemaForPost($post);
        }

        $published = $post->schema_published_time ?? $post->published_at;
        $modified = $post->schema_modified_time ?? $post->updated_at;

        return new SeoData(
            title: ($post->meta_title ?: $post->title) . ' - ' . config('seo.site_name'),
            description: $description,
            keywords: $post->meta_keywords,
            canonical: $canonical,
            robots: self::resolveRobots($post->robots_meta, $post->is_indexable, $post->is_followable),
            ogType: $post->og_type ?: 'article',
            ogTitle: $post->og_title ?: $post->title,
            ogDescription: self::truncate($post->og_description ?: $post->excerpt, 200),
            ogImage: $ogImage,
            ogUrl: $canonical,
            ogLocale: $post->og_locale ?: config('seo.locale'),
            ogSiteName: config('seo.site_name'),
            twitterCard: $post->twitter_card ?: 'summary_large_image',
            twitterTitle: $post->twitter_title,
            twitterDescription: $post->twitter_description,
            twitterImage: $twitterImage,
            twitterCreator: $post->twitter_creator,
            twitterSite: config('seo.twitter_site') ?: null,
            articlePublishedTime: $published?->toIso8601String(),
            articleModifiedTime: $modified?->toIso8601String(),
            jsonLd: array_filter($jsonLd),
        );
    }

    public static function forPage(
        string $title,
        ?string $description = null,
        ?string $canonical = null,
        ?string $keywords = null
    ): SeoData {
        $canonical = $canonical ?? url()->current();

        return new SeoData(
            title: $title,
            description: $description ?? config('seo.default_description'),
            keywords: $keywords ?? config('seo.default_keywords'),
            canonical: $canonical,
            robots: 'index,follow',
            ogType: 'website',
            ogTitle: $title,
            ogDescription: $description ?? config('seo.default_description'),
            ogImage: self::defaultOgImage(),
            ogUrl: $canonical,
            ogLocale: config('seo.locale'),
            ogSiteName: config('seo.site_name'),
            twitterCard: 'summary_large_image',
            twitterTitle: $title,
            twitterDescription: $description ?? config('seo.default_description'),
            twitterImage: self::defaultOgImage(),
            twitterSite: config('seo.twitter_site') ?: null,
        );
    }

    public static function forBlogIndex(
        Request $request,
        $posts = null,
        ?BlogCategory $activeCategory = null,
        ?BlogTag $activeTag = null
    ): SeoData {
        $title = config('seo.blog.index_title');
        $description = config('seo.blog.index_description');
        $canonical = route('frontend.blog.index');

        if ($activeCategory) {
            $title = ($activeCategory->meta_title ?: 'مدونة ' . $activeCategory->name) . config('seo.blog.category_title_suffix');
            $description = self::truncate($activeCategory->meta_description ?: $activeCategory->description, 160) ?? $description;
            $canonical = route('frontend.blog.category', $activeCategory->slug);
        } elseif ($activeTag) {
            $title = config('seo.blog.tag_title_prefix') . $activeTag->name . config('seo.blog.tag_title_suffix');
            $description = self::truncate($activeTag->description ?? $activeTag->name, 160) ?? $description;
            $canonical = route('frontend.blog.tag', $activeTag->slug);
        } elseif ($request->filled('search')) {
            $title = 'بحث: ' . $request->search . ' - مدونة ' . config('seo.site_name');
            $canonical = $request->fullUrl();
        }

        if ($request->has('page') && (int) $request->page > 1) {
            $canonical = $request->fullUrl();
        }

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => config('seo.site_name') . ' - المدونة',
                'description' => $description,
                'url' => route('frontend.blog.index'),
                'inLanguage' => config('seo.language'),
                'publisher' => self::organizationSchema(),
            ],
        ];

        $prevUrl = $posts && method_exists($posts, 'previousPageUrl') ? $posts->previousPageUrl() : null;
        $nextUrl = $posts && method_exists($posts, 'nextPageUrl') ? $posts->nextPageUrl() : null;

        return new SeoData(
            title: $title,
            description: $description,
            keywords: config('seo.default_keywords'),
            canonical: $canonical,
            robots: 'index,follow',
            ogType: 'website',
            ogTitle: $title,
            ogDescription: $description,
            ogImage: self::defaultOgImage(),
            ogUrl: $canonical,
            ogLocale: config('seo.locale'),
            ogSiteName: config('seo.site_name'),
            twitterCard: 'summary_large_image',
            twitterTitle: $title,
            twitterDescription: $description,
            twitterImage: self::defaultOgImage(),
            twitterSite: config('seo.twitter_site') ?: null,
            prevUrl: $prevUrl,
            nextUrl: $nextUrl,
            jsonLd: $jsonLd,
        );
    }

    protected static function blogPostingSchema(BlogPost $post, ?string $image, string $url): array
    {
        $published = $post->schema_published_time ?? $post->published_at;
        $modified = $post->schema_modified_time ?? $post->updated_at;

        return [
            '@context' => 'https://schema.org',
            '@type' => $post->schema_type ?: 'BlogPosting',
            'headline' => $post->schema_headline ?: $post->title,
            'description' => self::truncate($post->schema_description ?: $post->excerpt, 300),
            'image' => $image ? [$image] : [],
            'datePublished' => $published?->toIso8601String(),
            'dateModified' => $modified?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->schema_author_name ?: $post->author?->name ?? config('seo.site_name'),
                'url' => $post->schema_author_url,
            ],
            'publisher' => self::organizationSchema(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'url' => $url,
            'inLanguage' => $post->language ?: config('seo.language'),
        ];
    }

    protected static function breadcrumbSchemaForPost(BlogPost $post): array
    {
        $items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'الرئيسية',
                'item' => route('frontend.home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'المدونة',
                'item' => route('frontend.blog.index'),
            ],
        ];

        $position = 3;

        if ($post->category) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $post->category->name,
                'item' => route('frontend.blog.category', $post->category->slug),
            ];
        }

        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $post->title,
            'item' => $post->url,
        ];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    protected static function organizationSchema(): array
    {
        return [
            '@type' => 'Organization',
            'name' => config('seo.site_name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => self::absoluteUrl(config('seo.organization_logo')),
            ],
        ];
    }
}
