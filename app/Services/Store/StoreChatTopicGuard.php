<?php

namespace App\Services\Store;

class StoreChatTopicGuard
{
    /**
     * @var array<int, string>
     */
    protected array $productKeywords = [
        'منتج', 'منتجات', 'سعر', 'أسعار', 'شراء', 'اشتري', 'عندكم', 'متوفر',
        'ويندوز', 'windows', 'office', 'ووردبريس', 'wordpress', 'elementor',
        'قالب', 'template', 'theme', 'إضافة', 'plugin', 'بلجن', 'تفعيل', 'ترخيص',
        'license', 'key', 'مفتاح', 'تحميل', 'رقمي', 'digital', 'sku',
        'مقارنة', 'فرق', 'مواصفات', 'مميزات', 'توافق', 'إصدار', 'version',
        'روفيا', 'rovialink', 'متجر', 'store', 'سلة', 'cart',
    ];

    /**
     * @var array<int, string>
     */
    protected array $offTopicPatterns = [
        'طقس', 'الجو', 'رياضة', 'كرة', 'سياسة', 'انتخابات', 'دين', 'فلسفة',
        'اكتب قصة', 'اكتب قصيدة', 'وصفة طبخ', 'طبخ', 'علاج طبي', 'تشخيص',
        'برمجة عامة', 'اكتب كود', 'python', 'javascript tutorial',
        'من هو', 'تاريخ العالم', 'اخبار اليوم', 'نكتة', 'لغز',
        'حبيبي', 'موعد', 'غزل',
        'كيفك', 'كيف حالك', 'شلونك', 'اخبارك', 'أخبارك', 'مرحبا', 'مرحباً',
        'هلا', 'السلام عليكم', 'صباح الخير', 'مساء الخير', 'وش اخبارك',
    ];

    public function isClearlyOffTopic(string $message): bool
    {
        $normalized = mb_strtolower(trim($message));

        if ($normalized === '') {
            return true;
        }

        foreach ($this->offTopicPatterns as $pattern) {
            if (mb_strpos($normalized, mb_strtolower($pattern)) !== false) {
                return true;
            }
        }

        if ($this->hasProductKeyword($normalized)) {
            return false;
        }

        if (mb_strlen($normalized) < 12) {
            return false;
        }

        return ! $this->looksLikeProductQuestion($normalized);
    }

    protected function hasProductKeyword(string $normalized): bool
    {
        foreach ($this->productKeywords as $keyword) {
            if (mb_strpos($normalized, mb_strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function looksLikeProductQuestion(string $normalized): bool
    {
        if (preg_match('/\?|؟|كم|هل|ما هو|ما هي|أي|ايش|وش|عندكم/u', $normalized)) {
            foreach ($this->productKeywords as $keyword) {
                if (mb_strpos($normalized, mb_strtolower($keyword)) !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}
