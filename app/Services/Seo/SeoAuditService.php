<?php

namespace App\Services\Seo;

class SeoAuditService
{
    protected const SCORE_CRITICAL = 15;

    protected const SCORE_WARNING = 8;

    protected const SCORE_INFO = 3;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $checks = [];

    public function audit(SeoAuditContext $ctx): SeoAuditResult
    {
        $this->checks = [];

        $this->checkMetaTitle($ctx);
        $this->checkMetaDescription($ctx);
        $this->checkMetaKeywords($ctx);
        $this->checkSlug($ctx);
        $this->checkContentLength($ctx);
        $this->checkHeadings($ctx);

        if ($ctx->isProduct()) {
            $this->checkProductSpecific($ctx);
        }

        if ($ctx->isBlogPost()) {
            $this->checkBlogSpecific($ctx);
        }

        $score = $this->calculateScore();
        $summary = $this->buildSummary($score);

        return new SeoAuditResult($score, $this->checks, $summary);
    }

    protected function checkMetaTitle(SeoAuditContext $ctx): void
    {
        $title = trim($ctx->metaTitle);
        $len = mb_strlen($title);

        if ($title === '') {
            $this->addCheck('meta_title_empty', 'critical', 'meta_title', 'عنوان SEO فارغ', 'أضف عنواناً يتضمن الكلمة المفتاحية (30–60 حرفاً).');
        } elseif ($len < 30) {
            $this->addCheck('meta_title_short', 'warning', 'meta_title', "عنوان SEO قصير ({$len} حرفاً)", 'وسّع العنوان إلى 30–60 حرفاً مع الكلمة المفتاحية.');
        } elseif ($len > 60) {
            $this->addCheck('meta_title_long', 'warning', 'meta_title', "عنوان SEO طويل ({$len} حرفاً)", 'اختصر العنوان إلى 60 حرفاً كحد أقصى لتجنب القص في نتائج البحث.');
        } else {
            $this->addCheck('meta_title_ok', 'info', 'meta_title', 'طول عنوان SEO مناسب', 'جيد — راجع تضمين الكلمة المفتاحية في البداية.');
        }

        if ($title !== '' && $ctx->title !== '' && ! $this->titleAppearsInMeta($title, $ctx->title)) {
            $this->addCheck('meta_title_mismatch', 'info', 'meta_title', 'عنوان SEO لا يعكس اسم المحتوى بوضوح', 'ضمّن اسم المنتج/المقال أو كلمته المفتاحية في meta_title.');
        }
    }

    protected function checkMetaDescription(SeoAuditContext $ctx): void
    {
        $desc = trim($ctx->metaDescription);
        $len = mb_strlen($desc);

        if ($desc === '') {
            $this->addCheck('meta_description_empty', 'critical', 'meta_description', 'وصف SEO فارغ', 'أضف وصفاً جذاباً 120–160 حرفاً يشجع على النقر.');
        } elseif ($len < 120) {
            $this->addCheck('meta_description_short', 'warning', 'meta_description', "وصف SEO قصير ({$len} حرفاً)", 'وسّع الوصف إلى 120–160 حرفاً.');
        } elseif ($len > 160) {
            $this->addCheck('meta_description_long', 'warning', 'meta_description', "وصف SEO طويل ({$len} حرفاً)", 'اختصر الوصف إلى 160 حرفاً كحد أقصى.');
        } else {
            $this->addCheck('meta_description_ok', 'info', 'meta_description', 'طول وصف SEO مناسب', 'جيد.');
        }
    }

    protected function checkMetaKeywords(SeoAuditContext $ctx): void
    {
        if (trim($ctx->metaKeywords) === '') {
            $this->addCheck('meta_keywords_empty', 'warning', 'meta_keywords', 'الكلمات المفتاحية فارغة', 'أضف 3–8 كلمات مفتاحية مفصولة بفواصل.');
        } else {
            $count = count(array_filter(array_map('trim', preg_split('/[,،]/u', $ctx->metaKeywords) ?: [])));
            if ($count < 3) {
                $this->addCheck('meta_keywords_few', 'info', 'meta_keywords', 'عدد قليل من الكلمات المفتاحية', 'أضف المزيد من الكلمات ذات الصلة.');
            }
        }
    }

    protected function checkSlug(SeoAuditContext $ctx): void
    {
        $slug = trim($ctx->slug);

        if ($slug === '') {
            $this->addCheck('slug_empty', 'warning', 'slug', 'الرابط (Slug) فارغ', 'أنشئ slugاً قصيراً بالإنجليزية أو transliteration.');
        } elseif (preg_match('/\s/u', $slug)) {
            $this->addCheck('slug_spaces', 'critical', 'slug', 'الـ slug يحتوي مسافات', 'استخدم شرطات بدل المسافات.');
        } elseif (mb_strlen($slug) > 80) {
            $this->addCheck('slug_long', 'info', 'slug', 'الـ slug طويل جداً', 'اختصر الرابط لتحسين القراءة.');
        } elseif (! preg_match('/^[a-z0-9\-]+$/i', $slug)) {
            $this->addCheck('slug_chars', 'warning', 'slug', 'أحرف غير مناسبة في الـ slug', 'استخدم حروفاً لاتينية وأرقاماً وشرطات فقط.');
        }
    }

    protected function checkContentLength(SeoAuditContext $ctx): void
    {
        $text = $ctx->primaryText();
        $words = $this->wordCount($text);

        if ($ctx->isProduct()) {
            if ($words < 150) {
                $this->addCheck('product_content_thin', 'warning', 'description', "وصف المنتج قصير ({$words} كلمة تقريباً)", 'أضف وصفاً أطول (300+ كلمة) مع مميزات ومواصفات.');
            } elseif ($words >= 300) {
                $this->addCheck('product_content_ok', 'info', 'description', 'طول وصف المنتج جيد', 'محتوى كافٍ لمحركات البحث.');
            }
        }

        if ($ctx->isBlogPost()) {
            if ($words < 300) {
                $this->addCheck('blog_content_thin', 'warning', 'content', "المقال قصير ({$words} كلمة تقريباً)", 'وسّع المقال إلى 600+ كلمة للمنافسة في SERP.');
            } elseif ($words >= 600) {
                $this->addCheck('blog_content_ok', 'info', 'content', 'طول المقال مناسب', 'جيد للـ SEO.');
            }
        }
    }

    protected function checkHeadings(SeoAuditContext $ctx): void
    {
        $html = $ctx->content;
        if ($html === '' || ! str_contains($html, '<')) {
            return;
        }

        if (! preg_match('/<h2[\s>]/i', $html)) {
            $this->addCheck('no_h2', 'info', 'content', 'لا توجد عناوين H2 في المحتوى', 'قسّم المحتوى بعناوين H2/H3 لتحسين البنية.');
        }
    }

    protected function checkProductSpecific(SeoAuditContext $ctx): void
    {
        if (trim($ctx->shortDescription) === '') {
            $this->addCheck('short_description_empty', 'warning', 'short_description', 'الوصف المختصر فارغ', 'أضف 1–3 جمل تلخص قيمة المنتج.');
        }

        $metaTitle = trim($ctx->metaTitle);
        if ($metaTitle !== '' && $ctx->title !== '' && ! $this->titleAppearsInMeta($metaTitle, $ctx->title)) {
            $this->addCheck('product_name_in_meta', 'warning', 'meta_title', 'اسم المنتج غير واضح في عنوان SEO', 'ضمّن اسم المنتج أو الجزء الرئيسي منه في meta_title.');
        }
    }

    protected function checkBlogSpecific(SeoAuditContext $ctx): void
    {
        if (trim($ctx->excerpt) === '') {
            $this->addCheck('excerpt_empty', 'warning', 'excerpt', 'المقتطف فارغ', 'أضف مقتطفاً يعكس فكرة المقال.');
        }

        $focus = trim($ctx->focusKeyword);
        if ($focus === '') {
            $this->addCheck('focus_keyword_empty', 'critical', 'focus_keyword', 'الكلمة المفتاحية الرئيسية فارغة', 'حدد كلمة مفتاحية واحدة رئيسية للمقال.');
        } else {
            $body = $ctx->primaryText();
            if (! $this->containsKeyword($body, $focus)) {
                $this->addCheck('focus_keyword_missing_content', 'critical', 'focus_keyword', 'الكلمة المفتاحية غير موجودة في المحتوى', 'استخدم الكلمة المفتاحية في العنوان والفقرات الأولى.');
            }

            if ($ctx->title !== '' && ! $this->containsKeyword($ctx->title, $focus)) {
                $this->addCheck('focus_keyword_missing_title', 'warning', 'title', 'الكلمة المفتاحية غير موجودة في عنوان المقال', 'ضمّن focus_keyword في العنوان.');
            }

            $density = $this->keywordDensity($body, $focus);
            if ($density > 0 && $density < 0.5) {
                $this->addCheck('focus_keyword_low_density', 'info', 'focus_keyword', 'كثافة الكلمة المفتاحية منخفضة', 'كرّر الكلمة المفتاحية بشكل طبيعي (حوالي 1–2%).');
            } elseif ($density > 3) {
                $this->addCheck('focus_keyword_stuffing', 'warning', 'focus_keyword', 'حشو محتمل للكلمة المفتاحية', 'قلل تكرار الكلمة المفتاحية لتجنب العقوبات.');
            }
        }

        if (trim($ctx->featuredImageAlt) === '') {
            $this->addCheck('featured_alt_empty', 'info', 'featured_image_alt', 'نص بديل للصورة البارزة فارغ', 'أضف alt يصف الصورة ويتضمن الكلمة المفتاحية.');
        }
    }

    protected function addCheck(
        string $id,
        string $severity,
        string $field,
        string $message,
        string $recommendation
    ): void {
        $this->checks[] = [
            'id' => $id,
            'severity' => $severity,
            'field' => $field,
            'message' => $message,
            'recommendation' => $recommendation,
        ];
    }

    protected function calculateScore(): int
    {
        $score = 100;

        foreach ($this->checks as $check) {
            if (str_ends_with($check['id'], '_ok')) {
                continue;
            }

            $score -= match ($check['severity']) {
                'critical' => self::SCORE_CRITICAL,
                'warning' => self::SCORE_WARNING,
                default => self::SCORE_INFO,
            };
        }

        return max(0, min(100, $score));
    }

    protected function buildSummary(int $score): string
    {
        $critical = count(array_filter($this->checks, fn ($c) => $c['severity'] === 'critical' && ! str_ends_with($c['id'], '_ok')));
        $warning = count(array_filter($this->checks, fn ($c) => $c['severity'] === 'warning'));

        if ($score >= 80) {
            return "درجة SEO: {$score}/100 — جيد مع {$warning} تحذير و{$critical} مشكلة حرجة.";
        }
        if ($score >= 50) {
            return "درجة SEO: {$score}/100 — يحتاج تحسين: {$critical} حرج، {$warning} تحذير.";
        }

        return "درجة SEO: {$score}/100 — ضعيف؛ راجع التوصيات الحرجة أولاً.";
    }

    protected function wordCount(string $text): int
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text) ?: []);
    }

    protected function containsKeyword(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        return mb_stripos($haystack, $needle) !== false;
    }

    /**
     * يتحقق إن كان اسم المنتج/المقال ظاهراً في meta_title (تطابق مرن وليس حرفياً).
     */
    protected function titleAppearsInMeta(string $metaTitle, string $title): bool
    {
        if ($title === '' || $metaTitle === '') {
            return false;
        }

        $meta = $this->normalizeSeoText($metaTitle);
        $full = $this->normalizeSeoText($title);

        if ($full !== '' && str_contains($meta, $full)) {
            return true;
        }

        $core = $this->extractTitleCore($title);
        $coreNorm = $this->normalizeSeoText($core);
        if ($coreNorm !== '' && mb_strlen($coreNorm) >= 4 && str_contains($meta, $coreNorm)) {
            return true;
        }

        return $this->significantWordOverlap($metaTitle, $title, 0.55);
    }

    protected function normalizeSeoText(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\x{2013}\x{2014}\x{2212}\-_|،]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

    protected function extractTitleCore(string $title): string
    {
        $parts = preg_split('/[\x{2013}\x{2014}\x{2212}\-\|:–]+/u', $title, 2);

        return trim($parts[0] ?? $title);
    }

    /**
     * @return bool true إذا نسبة كافية من الكلمات المهمة في العنوان موجودة في meta_title
     */
    protected function significantWordOverlap(string $metaTitle, string $title, float $minRatio): bool
    {
        $words = array_values(array_filter(
            preg_split('/\s+/u', $this->normalizeSeoText($title)) ?: [],
            fn (string $w) => mb_strlen($w) >= 2
        ));

        if ($words === []) {
            return false;
        }

        $normMeta = $this->normalizeSeoText($metaTitle);
        $found = 0;
        foreach ($words as $word) {
            if (str_contains($normMeta, $word)) {
                $found++;
            }
        }

        return ($found / count($words)) >= $minRatio;
    }

    protected function keywordDensity(string $text, string $keyword): float
    {
        $words = $this->wordCount($text);
        if ($words === 0 || $keyword === '') {
            return 0;
        }

        $pattern = '/'.preg_quote($keyword, '/').'/iu';
        $matches = preg_match_all($pattern, $text);

        return ($matches / $words) * 100;
    }
}
