<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ProductShortDescriptionAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        $lang = $this->language === 'en'
            ? 'Write in persuasive, professional English.'
            : 'اكتب بالعربية الفصحى، أسلوب تسويقي احترافي.';

        return <<<INSTRUCTIONS
أنت خبير كتابة صفحات منتجات رقمية.
{$lang}
أعد **فقط** وصفاً مختصراً (2–4 جمل) بدون HTML وبدون أسماء حقول.
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
        ];
    }
}
