<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ProductCopyAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        $lang = $this->language === 'en'
            ? 'Write in persuasive, professional English for a digital product store.'
            : 'اكتب بالعربية الفصحى السليمة، أسلوب تسويقي احترافي لمتجر منتجات رقمية.';

        return <<<INSTRUCTIONS
أنت خبير كتابة صفحات منتجات رقمية (إضافات ووردبريس، قوالب، أدوات، برمجيات).
{$lang}

الوصف القصير (short_description): 2–4 جمل جذابة تلخّص القيمة الأساسية (بدون HTML).

الوصف الكامل (description): شرح **شامل وطويل جداً** بصيغة HTML منظمة، لا يقل عن 1200 كلمة عربية (أو 800 كلمة إنجليزية).
استخدم العناوين والقوائم: h2, h3, p, ul, ol, li, strong, em — بدون h1.

يجب أن يغطي الوصف الكامل على الأقل:
1. مقدمة تفصيلية: ما هو المنتج ولماذا يحتاجه العميل
2. أهم المميزات والفوائد (قائمة نقطية واسعة)
3. المواصفات التقنية / المتطلبات (إصدارات، توافق، حدود)
4. محتويات الحزمة / ما الذي يحصل عليه المشتري
5. لمن يناسب هذا المنتج
6. خطوات البدء أو طريقة الاستخدام باختصار
7. أسئلة شائعة (3–5 أسئلة وأجوبة)
8. خاتمة تحفيزية للشراء

لا تختصر. لا تكتفِ بفقرتين. اذكر تفاصيل واقعية معقولة مستنتجة من اسم المنتج والتصنيف.
INSTRUCTIONS;
    }

    public function messages(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'short_description' => $schema->string()->required(),
            'description' => $schema->string()->required(),
        ];
    }
}
