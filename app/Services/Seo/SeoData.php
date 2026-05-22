<?php

namespace App\Services\Seo;

class SeoData
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $keywords = null,
        public ?string $canonical = null,
        public string $robots = 'index,follow',
        public string $ogType = 'website',
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogImage = null,
        public ?string $ogUrl = null,
        public ?string $ogLocale = null,
        public ?string $ogSiteName = null,
        public string $twitterCard = 'summary_large_image',
        public ?string $twitterTitle = null,
        public ?string $twitterDescription = null,
        public ?string $twitterImage = null,
        public ?string $twitterCreator = null,
        public ?string $twitterSite = null,
        public ?string $articlePublishedTime = null,
        public ?string $articleModifiedTime = null,
        public ?string $prevUrl = null,
        public ?string $nextUrl = null,
        public array $jsonLd = [],
    ) {}

    public function ogTitle(): string
    {
        return $this->ogTitle ?? $this->title;
    }

    public function ogDescription(): string
    {
        return $this->ogDescription ?? $this->description ?? '';
    }

    public function twitterTitle(): string
    {
        return $this->twitterTitle ?? $this->ogTitle();
    }

    public function twitterDescription(): string
    {
        return $this->twitterDescription ?? $this->ogDescription();
    }

    public function jsonLdScripts(): array
    {
        $scripts = [];
        foreach ($this->jsonLd as $schema) {
            if (!empty($schema)) {
                $scripts[] = $schema;
            }
        }
        return $scripts;
    }
}
