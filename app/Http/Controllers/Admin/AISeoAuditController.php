<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiContentOrchestrator;
use App\Services\Seo\SeoAuditContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AISeoAuditController extends Controller
{
    public function __construct(
        protected AiContentOrchestrator $orchestrator,
    ) {
        $this->middleware('auth');
    }

    public function audit(Request $request)
    {
        $validated = $this->validatePayload($request);

        try {
            $validated['title'] = $validated['title'] ?? $validated['name'] ?? '';
            if (trim($validated['title']) === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'العنوان أو اسم المنتج مطلوب للفحص.',
                ], 422);
            }

            $ctx = SeoAuditContext::fromArray($validated);
            $model = $this->orchestrator->resolveModel(
                $validated['ai_model_id'] ?? null,
                'seo_generation'
            );

            $report = $this->orchestrator->auditSeo($ctx, $model);

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('SEO audit failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function apply(Request $request)
    {
        $validated = $this->validatePayload($request);
        $extra = $request->validate([
            'recommendations' => 'required|array',
            'recommendations.*.title' => 'nullable|string',
            'recommendations.*.detail' => 'nullable|string',
        ]);
        $validated = array_merge($validated, $extra);

        try {
            $validated['title'] = $validated['title'] ?? $validated['name'] ?? '';
            if (trim($validated['title']) === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'العنوان أو اسم المنتج مطلوب للفحص.',
                ], 422);
            }

            $ctx = SeoAuditContext::fromArray($validated);
            $model = $this->orchestrator->resolveModel(
                $validated['ai_model_id'] ?? null,
                'seo_generation'
            );

            $fields = $this->orchestrator->applySeoFixes(
                $ctx,
                $validated['recommendations'],
                $model
            );

            return response()->json([
                'success' => true,
                'data' => $fields,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('SEO apply failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePayload(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:product,blog_post',
            'title' => 'nullable|string|max:500',
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:500',
            'featured_image_alt' => 'nullable|string|max:255',
            'language' => 'nullable|in:ar,en',
            'ai_model_id' => 'nullable|exists:ai_models,id',
        ]);
    }
}
