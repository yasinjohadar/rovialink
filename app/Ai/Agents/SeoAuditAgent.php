<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class SeoAuditAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        return 'أنت خبير SEO تقني. حلّل نتائج الفحص الآلي وقدّم ملخصاً وتوصيات عملية مرتبة بالأولوية بالعربية. لا تكرر نفس نص الفحوصات حرفياً — أضف قيمة تحليلية.';
    }

    public function messages(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'overall_summary' => $schema->string()->required(),
            'recommendation_titles' => $schema->array()->items($schema->string())->required(),
            'recommendation_details' => $schema->array()->items($schema->string())->required(),
            'recommendation_priorities' => $schema->array()->items($schema->string())->required(),
            'recommendation_fields' => $schema->array()->items($schema->string())->required(),
            'quick_wins' => $schema->array()->items($schema->string())->required(),
        ];
    }
}
