<?php

namespace App\Services\Ai;

use App\Models\AIModel;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;

class DynamicAiBridge
{
    public function __construct(
        protected GeminiModelKeyNormalizer $geminiNormalizer,
    ) {}

    /**
     * @return array{provider_key: string, model: string, driver: string}
     */
    public function registerProvider(AIModel $model): array
    {
        $providerKey = 'ai_model_'.$model->id;
        $driver = $this->mapDriver($model->provider);
        $apiKey = $model->getDecryptedApiKey();

        if (empty($apiKey)) {
            throw new \RuntimeException('مفتاح API غير موجود لهذا النموذج. يرجى ضبطه من لوحة الإدارة.');
        }

        $config = [
            'driver' => $driver,
            'key' => $apiKey,
        ];

        $baseUrl = trim((string) ($model->base_url ?: ''));
        if ($baseUrl !== '' && $this->shouldUseCustomUrl($driver, $baseUrl)) {
            $config['url'] = rtrim($baseUrl, '/');
        }

        config(["ai.providers.{$providerKey}" => $config]);

        return [
            'provider_key' => $providerKey,
            'model' => $this->resolveModelKey($model, $driver),
            'driver' => $driver,
        ];
    }

    protected function resolveModelKey(AIModel $model, string $driver): string
    {
        if ($driver === 'gemini') {
            return $this->geminiNormalizer->normalize($model->model_key);
        }

        return trim($model->model_key);
    }

    protected function shouldUseCustomUrl(string $driver, string $baseUrl): bool
    {
        if ($driver === 'gemini') {
            return $this->geminiNormalizer->isGeminiApiUrl($baseUrl);
        }

        return true;
    }

    public function prompt(Agent $agent, string $prompt, AIModel $model, ?int $timeout = 300): AgentResponse
    {
        $resolved = $this->registerProvider($model);

        return $agent->prompt(
            $prompt,
            provider: $resolved['provider_key'],
            model: $resolved['model'],
            timeout: $timeout,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function promptStructured(Agent $agent, string $prompt, AIModel $model, ?int $timeout = 300): array
    {
        $response = $this->prompt($agent, $prompt, $model, $timeout);

        if ($response instanceof StructuredAgentResponse) {
            return $response->toArray();
        }

        return AiJsonParser::parse($response->text);
    }

    public function promptText(Agent $agent, string $prompt, AIModel $model, ?int $timeout = 300): string
    {
        $response = $this->prompt($agent, $prompt, $model, $timeout);

        return trim($response->text);
    }

    protected function mapDriver(string $provider): string
    {
        return match ($provider) {
            'openai' => 'openai',
            'anthropic' => 'anthropic',
            'google' => 'gemini',
            'groq' => 'groq',
            'openrouter' => 'openrouter',
            'custom', 'zai', 'manus', 'local' => 'openrouter',
            default => 'openai',
        };
    }
}
