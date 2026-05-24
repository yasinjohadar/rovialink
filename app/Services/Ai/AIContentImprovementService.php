<?php

namespace App\Services\Ai;

use App\Models\AIModel;

class AIContentImprovementService
{
    public function __construct(
        protected AIModelService $modelService,
        protected AiContentOrchestrator $orchestrator,
    ) {}

    public function improveContent(string $content, array $options = []): array
    {
        $type = $options['type'] ?? 'general';
        $model = $options['model'] ?? $this->modelService->getBestModelFor('content_improvement');

        if (! $model) {
            throw new \RuntimeException('لا يوجد نموذج AI متاح للتحسين');
        }

        return $this->orchestrator->improveContent($content, $type, $model);
    }

    public function checkGrammar(string $text, ?AIModel $model = null): array
    {
        $model ??= $this->modelService->getBestModelFor('content_improvement');

        if (! $model) {
            throw new \RuntimeException('لا يوجد نموذج AI متاح لفحص القواعد');
        }

        return $this->orchestrator->checkGrammar($text, $model);
    }

    public function enhanceClarity(string $text, ?AIModel $model = null): string
    {
        $result = $this->improveContent($text, ['type' => 'clarity', 'model' => $model]);

        return $result['content'] ?? $text;
    }

    public function suggestImprovements(string $content, ?AIModel $model = null): array
    {
        $result = $this->improveContent($content, ['type' => 'general', 'model' => $model]);

        return $result['suggestions'] ?? [];
    }
}
