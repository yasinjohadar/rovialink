<?php

namespace App\Services\Ai;

use App\Models\AIModel;
use App\Models\ContentSummary;

class AIContentSummaryService
{
    public function __construct(
        protected AIModelService $modelService,
        protected AiContentOrchestrator $orchestrator,
    ) {}

    public function summarize(string $content, string $type = 'short', ?AIModel $model = null): ContentSummary
    {
        $model ??= $this->modelService->getBestModelFor('content_summary')
            ?? $this->modelService->getBestModelFor('content_improvement');

        if (! $model) {
            throw new \RuntimeException('لا يوجد نموذج AI متاح للتلخيص');
        }

        return $this->orchestrator->summarize($content, $type, $model);
    }
}
