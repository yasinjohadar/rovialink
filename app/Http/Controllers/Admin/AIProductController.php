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
            'language' => 'nullable|in:ar,en',
            'ai_model_id' => 'nullable|exists:ai_models,id',
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

            $data = $this->orchestrator->generateProductCopy(
                $validated['name'],
                $model,
                [
                    'language' => $validated['language'] ?? 'ar',
                    'category' => $categoryName,
                    'price' => $validated['price'] ?? '',
                    'features' => $validated['features'] ?? '',
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('AI product copy failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
