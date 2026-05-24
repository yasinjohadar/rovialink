<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ContentToolsAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        protected string $mode = 'improve',
        protected string $language = 'ar',
    ) {}

    public function instructions(): Stringable|string
    {
        return match ($this->mode) {
            'summarize' => 'أنت مساعد تلخيص. أعد ملخصاً واضحاً فقط.',
            'grammar' => 'أنت مدقق لغوي. صحّح النص وأعد قائمة أخطاء إن وُجدت.',
            default => 'أنت محرر محتوى. حسّن النص مع الحفاظ على المعنى.',
        };
    }

    public function messages(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return match ($this->mode) {
            'summarize' => [
                'summary' => $schema->string()->required(),
            ],
            'grammar' => [
                'corrected_text' => $schema->string()->required(),
                'errors' => $schema->array()->items($schema->string()),
            ],
            default => [
                'content' => $schema->string()->required(),
                'suggestions' => $schema->array()->items($schema->string()),
            ],
        };
    }
}
