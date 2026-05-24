<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class SeoApplyAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $entityType = 'product',
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        $extra = $this->entityType === 'blog_post'
            ? 'أعد أيضاً focus_keyword و excerpt محسّنين.'
            : 'يمكن اقتراح slug محسّن إن لزم.';

        return <<<INSTRUCTIONS
أنت خبير SEO. طبّق التوصيات على حقول meta فقط دون إعادة كتابة المقال/الوصف الكامل.
{$extra}
احترم حدود الطول: meta_title 30-60 حرفاً، meta_description 120-160 حرفاً.
INSTRUCTIONS;
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
            'slug' => $schema->string(),
        ];

        if ($this->entityType === 'blog_post') {
            $fields['focus_keyword'] = $schema->string()->required();
            $fields['excerpt'] = $schema->string()->required();
        }

        return $fields;
    }
}
