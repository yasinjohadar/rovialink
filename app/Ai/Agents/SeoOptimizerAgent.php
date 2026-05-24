<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class SeoOptimizerAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $language = 'ar',
        protected bool $includeBlogFields = true,
    ) {}

    public function instructions(): Stringable|string
    {
        return 'أنت خبير SEO. أنشئ حقول meta محسّنة بدقة. استخدم كلمات عربية/إنجليزية صحيحة فقط بدون رموز غريبة.';
    }

    public function messages(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        $fields = [
            'meta_title' => $schema->string()->required(),
            'meta_description' => $schema->string()->required(),
            'meta_keywords' => $schema->string()->required(),
        ];

        if ($this->includeBlogFields) {
            $fields['focus_keyword'] = $schema->string()->required();
        }

        return $fields;
    }
}
