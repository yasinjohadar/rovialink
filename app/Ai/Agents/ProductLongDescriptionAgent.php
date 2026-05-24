<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ProductLongDescriptionAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        $lang = $this->language === 'en'
            ? 'Write in professional English.'
            : 'اكتب بالعربية الفصحى.';

        return <<<INSTRUCTIONS
أنت كاتب صفحات منتجات. أعد **فقط** حقل description: HTML طويل ومنظّم (h2, h3, p, ul, ol) بدون h1.
لا تضع أسماء حقول مثل short_description أو description كنص.
لا تستخدم الرمز | أو قوالب — محتوى حقيقي فقط.
{$lang}
INSTRUCTIONS;
    }

    public function messages(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'description' => $schema->string()->required(),
        ];
    }
}
