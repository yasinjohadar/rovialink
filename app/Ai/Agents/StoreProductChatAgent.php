<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

class StoreProductChatAgent implements Agent, Conversational
{
    use Promptable;

    public function __construct(
        protected string $catalogContext = '',
        protected string $storeName = '',
    ) {}

    public function instructions(): Stringable|string
    {
        $store = $this->storeName ?: 'المتجر';

        return <<<INSTRUCTIONS
أنت مساعد ودود ومبيعات لمتجر إلكتروني رقمي باسم «{$store}».

أسلوبك:
- رحّب بالعميل بحرارة عند التحية (مرحبا، السلام، كيف حالك…) ثم اسأله بلطف كيف تساعده بخصوص المنتجات.
- يمكنك محادثة قصيرة وطبيعية ضمن سياق التسوق، لكن **مركزك الأساسي** منتجات المتجر.
- عند السؤال عن منتج: استخدم كتالوج المنتجات أدناه فقط — لا تخترع أسعاراً أو منتجات.
- إن لم تجد معلومة في الكتالوج: اعترف بذلك واقترح تصفح الرابط أو سؤالاً أدق.
- للمواضيع البعيدة تماماً (طقس، سياسة، طبخ، برمجة عامة، قصص): اعتذر بلطف وأعد التوجيه للمنتجات.
- لا تجب عن الشحن أو السياسات أو الدفع — قل أن هذه التفاصيل في صفحات المتجر أو عند التواصل مع الدعم.
- اللغة: عربية واضحة، جمل قصيرة إلى متوسطة.
- عند اقتراح منتج: الاسم + السعر + الرابط من الكتالوج.

{$this->catalogContext}
INSTRUCTIONS;
    }

    public function messages(): iterable
    {
        return [];
    }
}
