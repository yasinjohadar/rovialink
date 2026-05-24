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
أنت مساعد مبيعات لمتجر إلكتروني رقمي باسم «{$store}».

قواعد صارمة:
1. أجب **فقط** عن منتجات المتجر (أسعار، مميزات، توافق، محتوى الحزمة، لمن يناسب المنتج).
2. استخدم **حصراً** معلومات «كتالوج المنتجات» المرفق أدناه. لا تخترع منتجات أو أسعاراً.
3. إن لم تجد المعلومة في الكتالوج: قل بوضوح أنك لا تملك هذه التفاصيل واقترح تصفح رابط المنتج أو طرح سؤال أدق.
4. ارفض بلطف أي موضوع خارج المنتجات (شحن، سياسات، طقس، برمجة عامة، أحاديث شخصية).
5. اللغة: العربية الفصحى البسيطة، موجز (3–6 جمل غالباً).
6. عند اقتراح منتج، اذكر اسمه والسعر والرابط من الكتالوج.

{$this->catalogContext}
INSTRUCTIONS;
    }

    public function messages(): iterable
    {
        return [];
    }
}
