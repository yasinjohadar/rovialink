<?php

namespace App\Services\Ai;

use App\Models\AIModel;
use App\Models\BlogCategory;

/**
 * @deprecated Use AiContentOrchestrator instead.
 */
class AIBlogPostService
{
    public function __construct(
        protected AiContentOrchestrator $orchestrator,
    ) {}

    public function generateBlogPost(string $topic, AIModel $model, array $options = []): array
    {
        return $this->orchestrator->generateBlogPost($topic, $model, $options);
    }
}
