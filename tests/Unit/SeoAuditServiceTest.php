<?php

use App\Services\Seo\SeoAuditContext;
use App\Services\Seo\SeoAuditService;

test('flags empty meta title as critical', function () {
    $ctx = new SeoAuditContext(
        type: 'product',
        title: 'منتج تجريبي',
        metaTitle: '',
        metaDescription: str_repeat('و', 130),
    );

    $result = (new SeoAuditService)->audit($ctx);

    expect($result->score)->toBeLessThan(100);
    expect(collect($result->checks)->pluck('id'))->toContain('meta_title_empty');
});

test('flags missing focus keyword on blog post', function () {
    $ctx = new SeoAuditContext(
        type: 'blog_post',
        title: 'مقال',
        content: str_repeat('محتوى ', 80),
        metaTitle: str_repeat('ع', 40),
        metaDescription: str_repeat('و', 130),
        focusKeyword: '',
    );

    $result = (new SeoAuditService)->audit($ctx);

    expect(collect($result->checks)->pluck('id'))->toContain('focus_keyword_empty');
});

test('product name in meta title passes with flexible match', function () {
    $ctx = new SeoAuditContext(
        type: 'product',
        title: 'Windows 11 Pro - مفتاح تفعيل أصلي',
        metaTitle: 'Windows 11 Pro مفتاح تفعيل أصلي وسريع',
        metaDescription: str_repeat('و', 130),
        metaKeywords: 'ويندوز, تفعيل, مفتاح',
        slug: 'windows-11-pro',
        shortDescription: 'وصف مختصر',
        content: str_repeat('وصف ', 200),
    );

    $result = (new SeoAuditService)->audit($ctx);
    $ids = collect($result->checks)->pluck('id');

    expect($ids)->not->toContain('product_name_in_meta');
    expect($ids)->not->toContain('meta_title_mismatch');
});

test('good meta yields higher score', function () {
    $ctx = new SeoAuditContext(
        type: 'product',
        title: 'إضافة Elementor',
        slug: 'elementor-addon',
        metaTitle: str_repeat('ع', 45),
        metaDescription: str_repeat('و', 140),
        metaKeywords: 'ووردبريس, elementor, إضافة',
        shortDescription: 'وصف مختصر للمنتج',
        content: str_repeat('وصف ', 200),
    );

    $result = (new SeoAuditService)->audit($ctx);

    expect($result->score)->toBeGreaterThan(50);
});
