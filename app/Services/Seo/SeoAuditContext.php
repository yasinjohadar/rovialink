<?php

namespace App\Services\Seo;

class SeoAuditContext
{
    public function __construct(
        public string $type,
        public string $title,
        public string $slug = '',
        public string $content = '',
        public string $shortDescription = '',
        public string $excerpt = '',
        public string $metaTitle = '',
        public string $metaDescription = '',
        public string $metaKeywords = '',
        public string $focusKeyword = '',
        public string $canonicalUrl = '',
        public string $featuredImageAlt = '',
        public string $language = 'ar',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: (string) ($data['type'] ?? 'product'),
            title: (string) ($data['title'] ?? $data['name'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            content: (string) ($data['content'] ?? $data['description'] ?? ''),
            shortDescription: (string) ($data['short_description'] ?? ''),
            excerpt: (string) ($data['excerpt'] ?? ''),
            metaTitle: (string) ($data['meta_title'] ?? ''),
            metaDescription: (string) ($data['meta_description'] ?? ''),
            metaKeywords: (string) ($data['meta_keywords'] ?? ''),
            focusKeyword: (string) ($data['focus_keyword'] ?? ''),
            canonicalUrl: (string) ($data['canonical_url'] ?? ''),
            featuredImageAlt: (string) ($data['featured_image_alt'] ?? ''),
            language: (string) ($data['language'] ?? 'ar'),
        );
    }

    public function isBlogPost(): bool
    {
        return $this->type === 'blog_post';
    }

    public function isProduct(): bool
    {
        return $this->type === 'product';
    }

    public function bodyText(): string
    {
        return strip_tags($this->content);
    }

    public function primaryText(): string
    {
        if ($this->isProduct()) {
            return trim($this->shortDescription."\n".$this->bodyText());
        }

        return trim($this->excerpt."\n".$this->bodyText());
    }
}
