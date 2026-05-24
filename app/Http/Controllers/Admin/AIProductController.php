<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Ai\AiContentOrchestrator;
use App\Services\Ai\AIModelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIProductController extends Controller
{
    public function __construct(
        protected AiContentOrchestrator $orchestrator,
        protected AIModelService $modelService,
    ) {
        $this->middleware('auth');
    }

    public function generateCopy(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'features' => 'nullable|string|max:2000',
            'short_description' => 'nullable|string|max:5000',
            'language' => 'nullable|in:ar,en',
            'ai_model_id' => 'nullable|exists:ai_models,id',
            'step' => 'nullable|in:short,description',
        ]);

        try {
            $model = $this->orchestrator->resolveModel(
                $validated['ai_model_id'] ?? null,
                'product_copy'
            );

            $categoryName = '';
            if (! empty($validated['category_id'])) {
                $categoryName = Category::find($validated['category_id'])?->name ?? '';
            }

            $context = [
                'language' => $validated['language'] ?? 'ar',
                'category' => $categoryName,
                'price' => $validated['price'] ?? '',
                'features' => $validated['features'] ?? '',
                'short_description' => $validated['short_description'] ?? '',
            ];

            $step = $validated['step'] ?? 'short';

            $data = match ($step) {
                'description' => $this->orchestrator->generateProductDescription(
                    $validated['name'],
                    $model,
                    $context,
                ),
                default => $this->orchestrator->generateProductShortDescription(
                    $validated['name'],
                    $model,
                    $context,
                ),
            };

            return response()->json([
                'success' => true,
                'data' => $data,
                'step' => $step,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('AI product copy failed: '.$e->getMessage(), [
                'step' => $validated['step'] ?? 'short',
            ]);

            return response()->json([
                'success' => false,
                'message' => $this->friendlyAiErrorMessage($e),
            ], 500);
        }
    }

    protected function friendlyAiErrorMessage(\Throwable $e): string
    {
        $errorMessage = $e->getMessage();

        if (str_contains($errorMessage, 'timeout') || str_contains($errorMessage, 'Timeout')) {
            return 'انتهت مهلة الخادم أو مزود AI. جرّب نموذجاً أسرع أو أعد المحاولة بعد قليل.';
        }

        if (stripos($errorMessage, 'API Key') !== false || stripos($errorMessage, 'api key') !== false) {
            return 'مشكلة في مفتاح API. تحقق من إعدادات النموذج في لوحة AI.';
        }

        if (str_contains($errorMessage, 'quota') || str_contains($errorMessage, 'rate limit') || str_contains($errorMessage, '429')) {
            return 'تم تجاوز حد الطلبات أو نفاد الرصيد. انتظر دقيقة أو غيّر النموذج.';
        }

        if (str_contains($errorMessage, '<html') || str_contains($errorMessage, 'is not valid JSON')) {
            return 'رد غير متوقع من مزود AI (صفحة HTML). تحقق من base URL والنموذج في OpenRouter.';
        }

        return 'حدث خطأ أثناء التوليد: '.$errorMessage;
    }

    public function generateSeo(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'nullable|in:ar,en',
            'ai_model_id' => 'nullable|exists:ai_models,id',
        ]);

        try {
            $model = $this->orchestrator->resolveModel(
                $validated['ai_model_id'] ?? null,
                'seo_generation'
            );

            $description = $validated['description'] ?? $validated['name'];

            $data = $this->orchestrator->generateProductSeo(
                $validated['name'],
                $description,
                $model,
                $validated['language'] ?? 'ar'
            );

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('AI product SEO failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
