<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class BlogWriterAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        $lang = $this->language === 'en'
            ? 'Write in fluent English.'
            : 'اكتب باللغة العربية الفصحى السليمة.';

        return <<<INSTRUCTIONS
أنت كاتب محتوى محترف للمدونات والتجارة الإلكترونية.
{$lang}
استخدم HTML مناسباً (h2, h3, p, ul, ol, strong, em) دون h1.
أعد مخرجات منظمة فقط حسب المخطط المطلوب.
INSTRUCTIONS;
    }

    public function messages(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'content' => $schema->string()->required(),
            'excerpt' => $schema->string()->required(),
        ];
    }
}
