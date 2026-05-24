<?php

namespace App\Services\Ai;

use App\Ai\Agents\BlogWriterAgent;
use App\Ai\Agents\ContentToolsAgent;
use App\Ai\Agents\ProductLongDescriptionAgent;
use App\Ai\Agents\ProductShortDescriptionAgent;
use App\Ai\Agents\SeoApplyAgent;
use App\Ai\Agents\SeoAuditAgent;
use App\Ai\Agents\SeoOptimizerAgent;
use App\Services\Seo\SeoAuditContext;
use App\Services\Seo\SeoAuditService;
use App\Models\AIModel;
use App\Models\BlogCategory;
use App\Models\ContentSummary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiContentOrchestrator
{
    use AiContentTextHelpers;

    public function __construct(
        protected DynamicAiBridge $bridge,
        protected AIModelService $modelService,
        protected SeoAuditService $seoAuditService,
    ) {}

    public function resolveModel(?int $modelId, string $capability = 'blog_generation'): AIModel
    {
        if ($modelId) {
            $model = AIModel::active()->find($modelId);
            if ($model) {
                return $model;
            }
        }

        $model = $this->modelService->getBestModelFor($capability)
            ?? $this->modelService->getDefaultModel();

        if (! $model) {
            throw new \RuntimeException('لا يوجد نموذج ذكاء اصطناعي نشط. يرجى إضافة نموذج من لوحة الإدارة.');
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function generateBlogPost(string $topic, AIModel $model, array $options = []): array
    {
        set_time_limit(500);

        $contentLength = $options['content_length'] ?? 'medium';
        $tone = $options['tone'] ?? 'professional';
        $language = $options['language'] ?? 'ar';
        $category = $options['category'] ?? null;

        $lengthMap = [
            'short' => '500-800 كلمة',
            'medium' => '1000-1500 كلمة',
            'long' => '2000-3000 كلمة',
        ];
        $toneMap = [
            'professional' => 'احترافي ومهني',
            'friendly' => 'ودود وسهل',
            'technical' => 'تقني ومفصل',
            'casual' => 'عادي ومرن',
            'formal' => 'رسمي ومهذب',
        ];

        $categoryContext = $category instanceof BlogCategory
            ? "التصنيف: {$category->name}. "
            : '';

        $lengthLabel = $lengthMap[$contentLength] ?? $lengthMap['medium'];
        $toneLabel = $toneMap[$tone] ?? $toneMap['professional'];

        $prompt = <<<PROMPT
اكتب مقالاً شاملاً حول الموضوع: {$topic}
{$categoryContext}
الطول: {$lengthLabel}
الأسلوب: {$toneLabel}

تضمين مقدمة، عناوين فرعية، أمثلة عند الحاجة، وخاتمة.
PROMPT;

        $agent = new BlogWriterAgent(language: $language);
        $contentData = $this->bridge->promptStructured($agent, $prompt, $model, 500);

        $title = $contentData['title'] ?? $topic;
        $content = $contentData['content'] ?? '';
        $excerpt = $contentData['excerpt'] ?? $this->generateExcerpt($content);
        $slug = $this->generateSlug($title);

        $result = compact('title', 'slug', 'excerpt', 'content');

        if ($options['generate_seo'] ?? true) {
            $result = array_merge($result, $this->generateBlogSeo($title, $content, $topic, $model, $language));
        }

        if ($options['generate_og'] ?? true) {
            $result = array_merge($result, [
                'og_title' => Str::limit($title, 60),
                'og_description' => Str::limit($excerpt ?: strip_tags($content), 200),
                'og_type' => 'article',
                'og_locale' => $language === 'ar' ? 'ar_SA' : 'en_US',
            ]);
        }

        if ($options['generate_twitter'] ?? true) {
            $result = array_merge($result, [
                'twitter_card' => 'summary_large_image',
                'twitter_title' => Str::limit($title, 70),
                'twitter_description' => Str::limit($excerpt ?: strip_tags($content), 200),
            ]);
        }

        if ($options['generate_schema'] ?? true) {
            $result = array_merge($result, [
                'schema_type' => 'Article',
                'schema_headline' => $title,
                'schema_description' => $excerpt ?: Str::limit(strip_tags($content), 200),
            ]);
        }

        if (($options['generate_keyword_synonyms'] ?? true) && ! empty($result['focus_keyword'])) {
            $result['focus_keyword_synonyms'] = $this->generateKeywordSynonyms($result['focus_keyword'], $model, $language);
        }

        $result['canonical_url'] = url('/blog/'.$slug);
        $result['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($content)) / 200));

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function generateBlogSeo(string $title, string $content, string $topic, AIModel $model, string $language = 'ar'): array
    {
        $prompt = <<<PROMPT
أنشئ حقول SEO للمقال:
العنوان: {$title}
الموضوع: {$topic}
مقتطف من المحتوى: {$this->generateExcerpt($content)}
PROMPT;

        try {
            $agent = new SeoOptimizerAgent(language: $language, includeBlogFields: true);
            $data = $this->bridge->promptStructured($agent, $prompt, $model, 120);

            $metaKeywords = $this->cleanKeywords($data['meta_keywords'] ?? $this->extractKeywords($content));
            $focusKeyword = $this->cleanText($data['focus_keyword'] ?? $this->extractMainKeyword($topic, $content));

            return [
                'meta_title' => $this->cleanText($data['meta_title'] ?? Str::limit($title, 60)),
                'meta_description' => $this->cleanText($data['meta_description'] ?? Str::limit(strip_tags($content), 160)),
                'meta_keywords' => $metaKeywords,
                'focus_keyword' => $focusKeyword,
            ];
        } catch (\Throwable $e) {
            Log::warning('Blog SEO generation fallback: '.$e->getMessage());

            return [
                'meta_title' => $this->cleanText(Str::limit($title, 60)),
                'meta_description' => $this->cleanText(Str::limit(strip_tags($content), 160)),
                'meta_keywords' => $this->cleanKeywords($this->extractKeywords($content)),
                'focus_keyword' => $this->extractMainKeyword($topic, $content),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array{short_description: string}
     */
    public function generateProductShortDescription(string $productName, AIModel $model, array $context = []): array
    {
        set_time_limit(120);

        $language = $context['language'] ?? 'ar';
        $category = $context['category'] ?? '';
        $features = $context['features'] ?? '';
        $price = $context['price'] ?? '';

        $featuresBlock = $features !== ''
            ? "معلومات إضافية:\n{$features}\n"
            : '';

        $prompt = <<<PROMPT
اكتب وصفاً مختصراً (2–4 جمل) لمنتج رقمي:

المنتج: {$productName}
التصنيف: {$category}
السعر: {$price}
{$featuresBlock}
PROMPT;

        $agent = new ProductShortDescriptionAgent(language: $language);
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 90);

        return [
            'short_description' => $this->sanitizeProductCopyField($data['short_description'] ?? '', 'short_description'),
        ];
    }

    /**
     * @return array{description: string}
     */
    public function generateProductDescription(string $productName, AIModel $model, array $context = []): array
    {
        set_time_limit(180);

        $language = $context['language'] ?? 'ar';
        $short = $context['short_description'] ?? '';

        $description = $this->generateProductLongDescription(
            $productName,
            $short,
            $model,
            $language,
            $context['category'] ?? '',
            $context['features'] ?? '',
            $context['price'] ?? '',
        );

        return ['description' => $description];
    }

    /**
     * @return array<string, mixed>
     */
    public function generateProductCopy(string $productName, AIModel $model, array $context = []): array
    {
        $short = $this->generateProductShortDescription($productName, $model, $context);
        $long = $this->generateProductDescription($productName, $model, array_merge($context, $short));

        return array_merge($short, $long);
    }

    protected function generateProductLongDescription(
        string $productName,
        string $shortDescription,
        AIModel $model,
        string $language,
        string $category,
        string $features,
        string $price,
    ): string {
        $prompt = <<<PROMPT
اكتب وصفاً تسويقياً **طويلاً جداً** (HTML) لصفحة منتج:

المنتج: {$productName}
التصنيف: {$category}
السعر: {$price}
الوصف المختصر الحالي: {$shortDescription}
معلومات إضافية: {$features}

المطلوب: HTML كامل بأقسام (مقدمة، مميزات، مواصفات، لمن يناسب، أسئلة شائعة، خاتمة).
لا تكرر الوصف المختصر فقط — وسّعه إلى 800+ كلمة.
PROMPT;

        $agent = new ProductLongDescriptionAgent(language: $language);
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 120);
        $description = $this->sanitizeProductCopyField($data['description'] ?? '', 'description');

        if (! $this->isValidProductDescription($description)) {
            return $this->fallbackProductDescriptionHtml($productName, $shortDescription);
        }

        return $description;
    }

    protected function sanitizeProductCopyField(string $value, string $fieldName): string
    {
        $value = trim($value);
        $value = preg_replace('/^\|+\s*/u', '', $value) ?? $value;
        $value = trim($value);

        $placeholders = [
            'short_description',
            'description',
            '|short_description',
            '|description',
            '{{short_description}}',
            '{{description}}',
        ];

        if (in_array(strtolower($value), array_map('strtolower', $placeholders), true)) {
            return '';
        }

        if (preg_match('/^\{?\s*"?'.preg_quote($fieldName, '/').'"?\s*\}?\s*$/i', $value)) {
            return '';
        }

        return $value;
    }

    protected function isValidProductDescription(string $description): bool
    {
        $plain = trim(strip_tags($description));
        $len = mb_strlen($plain);

        if ($len < 200) {
            return false;
        }

        if (preg_match('/^\|?\s*(short_description|description)\s*$/iu', $plain)) {
            return false;
        }

        return true;
    }

    protected function fallbackProductDescriptionHtml(string $productName, string $shortDescription): string
    {
        $short = e($shortDescription);
        $name = e($productName);

        return <<<HTML
<h2>نظرة عامة</h2>
<p>{$short}</p>
<h2>لماذا {$name}؟</h2>
<p>يقدّم هذا المنتج قيمة عملية للمستخدمين الذين يبحثون عن حل موثوق وسهل الاستخدام. راجع التفاصيل أدناه ثم أكمل الطلب.</p>
<h2>المميزات الرئيسية</h2>
<ul>
<li>تجربة استخدام واضحة ومناسبة للمتاجر الرقمية</li>
<li>محتوى قابل للتوسعة لاحقاً من لوحة الإدارة</li>
<li>دعم كامل للغة العربية واتجاه RTL</li>
</ul>
<h2>ملاحظة</h2>
<p>تم إنشاء هذا الهيكل تلقائياً لأن النموذج لم يُرجع وصفاً طويلاً. يُفضّل إعادة التوليد بنموذج أقوى أو زيادة max tokens ثم تعديل النص يدوياً.</p>
HTML;
    }

    /**
     * @return array<string, mixed>
     */
    public function generateProductSeo(string $productName, string $description, AIModel $model, string $language = 'ar'): array
    {
        $prompt = <<<PROMPT
أنشئ حقول SEO لصفحة منتج:
المنتج: {$productName}
الوصف: {$this->generateExcerpt($description)}
PROMPT;

        $agent = new SeoOptimizerAgent(language: $language, includeBlogFields: false);
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 120);

        return [
            'meta_title' => $this->cleanText($data['meta_title'] ?? Str::limit($productName, 60)),
            'meta_description' => $this->cleanText($data['meta_description'] ?? Str::limit(strip_tags($description), 160)),
            'meta_keywords' => $this->cleanKeywords($data['meta_keywords'] ?? $this->extractKeywords($description)),
        ];
    }

    public function summarize(string $content, string $type, AIModel $model): ContentSummary
    {
        set_time_limit(180);

        $typeLabel = match ($type) {
            'long' => 'ملخصاً مفصلاً',
            'bullet_points' => 'نقاطاً رئيسية',
            default => 'ملخصاً قصيراً',
        };

        $prompt = "لخّص النص التالي كـ {$typeLabel}:\n\n{$content}";
        $agent = new ContentToolsAgent(mode: 'summarize');
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 180);

        return ContentSummary::create([
            'summarizable_type' => 'manual',
            'summarizable_id' => 0,
            'summary_text' => $data['summary'] ?? '',
            'summary_type' => $type,
            'ai_model_id' => $model->id,
            'tokens_used' => 0,
            'cost' => 0,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function improveContent(string $content, string $improvementType, AIModel $model): array
    {
        set_time_limit(180);

        $prompt = "حسّن المحتوى التالي (نوع التحسين: {$improvementType}):\n\n{$content}";
        $agent = new ContentToolsAgent(mode: 'improve');
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 180);

        return [
            'content' => $data['content'] ?? $content,
            'suggestions' => $data['suggestions'] ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function checkGrammar(string $text, AIModel $model): array
    {
        set_time_limit(180);

        $prompt = "صحّح الأخطاء اللغوية في النص:\n\n{$text}";
        $agent = new ContentToolsAgent(mode: 'grammar');
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 120);

        return [
            'corrected' => $data['corrected_text'] ?? $text,
            'errors' => $data['errors'] ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function auditSeo(SeoAuditContext $ctx, AIModel $model): array
    {
        set_time_limit(120);

        $ruleResult = $this->seoAuditService->audit($ctx);
        $checksJson = json_encode($ruleResult->checks, JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
نوع المحتوى: {$ctx->type}
العنوان: {$ctx->title}
Slug: {$ctx->slug}
meta_title: {$ctx->metaTitle}
meta_description: {$ctx->metaDescription}
meta_keywords: {$ctx->metaKeywords}
focus_keyword: {$ctx->focusKeyword}

نتائج الفحص الآلي (score={$ruleResult->score}):
{$checksJson}

قدّم ملخصاً وتوصيات عملية مرتبة وquick wins.
PROMPT;

        try {
            $agent = new SeoAuditAgent(language: $ctx->language);
            $aiReport = $this->bridge->promptStructured($agent, $prompt, $model, 120);
            $aiReport['prioritized_recommendations'] = $this->mergeSeoRecommendations($aiReport);
        } catch (\Throwable $e) {
            Log::warning('SEO AI audit fallback: '.$e->getMessage());
            $aiReport = [
                'overall_summary' => $ruleResult->summaryAr,
                'prioritized_recommendations' => $this->recommendationsFromChecks($ruleResult->checks),
                'quick_wins' => [],
            ];
        }

        return [
            'score' => $ruleResult->score,
            'checks' => $ruleResult->checks,
            'summary_ar' => $ruleResult->summaryAr,
            'ai' => $aiReport,
            'recommendations' => $aiReport['prioritized_recommendations'] ?? [],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $recommendations
     * @return array<string, mixed>
     */
    public function applySeoFixes(SeoAuditContext $ctx, array $recommendations, AIModel $model): array
    {
        set_time_limit(120);

        $recsJson = json_encode($recommendations, JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
نوع المحتوى: {$ctx->type}
العنوان/الاسم: {$ctx->title}
المحتوى (مقتطف): {$this->generateExcerpt($ctx->primaryText())}

الحقول الحالية:
meta_title: {$ctx->metaTitle}
meta_description: {$ctx->metaDescription}
meta_keywords: {$ctx->metaKeywords}
focus_keyword: {$ctx->focusKeyword}
excerpt: {$ctx->excerpt}
slug: {$ctx->slug}

التوصيات المطلوب تطبيقها:
{$recsJson}

أعد الحقول المحسّنة فقط.
PROMPT;

        $agent = new SeoApplyAgent(
            entityType: $ctx->type,
            language: $ctx->language,
        );
        $data = $this->bridge->promptStructured($agent, $prompt, $model, 120);

        $result = [
            'meta_title' => $this->cleanText($data['meta_title'] ?? $ctx->metaTitle),
            'meta_description' => $this->cleanText($data['meta_description'] ?? $ctx->metaDescription),
            'meta_keywords' => $this->cleanKeywords($data['meta_keywords'] ?? $ctx->metaKeywords),
        ];

        if (! empty($data['slug'])) {
            $result['slug'] = Str::slug($data['slug']);
        }

        if ($ctx->isBlogPost()) {
            $result['focus_keyword'] = $this->cleanText($data['focus_keyword'] ?? $ctx->focusKeyword);
            $result['excerpt'] = $this->cleanText($data['excerpt'] ?? $ctx->excerpt);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $aiReport
     * @return array<int, array<string, string>>
     */
    protected function mergeSeoRecommendations(array $aiReport): array
    {
        $titles = $aiReport['recommendation_titles'] ?? [];
        $details = $aiReport['recommendation_details'] ?? [];
        $priorities = $aiReport['recommendation_priorities'] ?? [];
        $fields = $aiReport['recommendation_fields'] ?? [];
        $out = [];
        $count = max(count($titles), count($details));

        for ($i = 0; $i < $count; $i++) {
            $out[] = [
                'title' => (string) ($titles[$i] ?? ''),
                'detail' => (string) ($details[$i] ?? ''),
                'priority' => (string) ($priorities[$i] ?? 'medium'),
                'affected_fields' => (string) ($fields[$i] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, array<string, mixed>>  $checks
     * @return array<int, array<string, string>>
     */
    protected function recommendationsFromChecks(array $checks): array
    {
        return array_map(
            fn ($c) => [
                'title' => $c['message'],
                'detail' => $c['recommendation'],
                'priority' => $c['severity'],
                'affected_fields' => $c['field'],
            ],
            array_values(array_filter($checks, fn ($c) => ! str_ends_with($c['id'], '_ok')))
        );
    }

    protected function generateKeywordSynonyms(string $keyword, AIModel $model, string $language): string
    {
        $prompt = "أعطِ 8-12 مرادفاً عربياً للكلمة: {$keyword} — مفصولة بفواصل فقط.";

        try {
            $agent = new BlogWriterAgent(language: $language);

            return $this->cleanKeywords($this->bridge->promptText($agent, $prompt, $model, 60));
        } catch (\Throwable $e) {
            Log::warning('Keyword synonyms failed: '.$e->getMessage());

            return '';
        }
    }
}
