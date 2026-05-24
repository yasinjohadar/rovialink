<?php

namespace App\Services\Ai;

use App\Models\AIModel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Ai\Agents\ConnectionTestAgent;

class AIModelService
{
    public function __construct(
        protected DynamicAiBridge $bridge,
    ) {}
    /**
     * إنشاء موديل جديد
     */
    public function createModel(array $data, ?User $user = null): AIModel
    {
        if ($user) {
            $data['created_by'] = $user->id;
        }

        // إذا كان هذا الموديل هو الافتراضي، إلغاء الافتراضي من الموديلات الأخرى
        if (isset($data['is_default']) && $data['is_default']) {
            AIModel::where('is_default', true)->update(['is_default' => false]);
        }

        return AIModel::create($data);
    }

    /**
     * تحديث موديل
     */
    public function updateModel(AIModel $model, array $data): AIModel
    {
        // إذا كان هذا الموديل هو الافتراضي، إلغاء الافتراضي من الموديلات الأخرى
        if (isset($data['is_default']) && $data['is_default'] && !$model->is_default) {
            AIModel::where('is_default', true)->where('id', '!=', $model->id)->update(['is_default' => false]);
        }

        // إذا كان هناك api_key جديد، استخدم mutator لتشفيره
        $hasApiKey = isset($data['api_key']) && !empty(trim($data['api_key']));
        if ($hasApiKey) {
            $apiKeyValue = trim($data['api_key']);
            $model->api_key = $apiKeyValue; // Mutator سيقوم بتشفيره تلقائياً
            Log::info('API Key updated for model', [
                'model_id' => $model->id,
                'key_length' => strlen($apiKeyValue)
            ]);
        }
        
        // إزالة api_key من البيانات قبل update
        unset($data['api_key']);

        // تحديث البيانات الأخرى
        if (!empty($data)) {
            $model->update($data);
        }
        
        // إذا تم تحديث api_key، احفظه بشكل منفصل (لأن update قد لا يستدعي mutator)
        if ($hasApiKey) {
            $model->save(); // تأكد من الحفظ
            Log::info('Model saved with API Key', ['model_id' => $model->id]);
        }
        
        // تحديث الـ model من قاعدة البيانات
        $model->refresh();
        
        // التحقق من أن API Key تم حفظه
        if ($hasApiKey) {
            $decrypted = $model->getDecryptedApiKey();
            if (empty($decrypted)) {
                Log::error('API Key was not saved correctly', ['model_id' => $model->id]);
            } else {
                Log::info('API Key verified after save', ['model_id' => $model->id]);
            }
        }
        
        return $model;
    }

    /**
     * حذف موديل
     */
    public function deleteModel(AIModel $model): bool
    {
        // إذا كان الموديل الافتراضي، تعيين موديل آخر كافتراضي
        if ($model->is_default) {
            $newDefault = AIModel::where('id', '!=', $model->id)
                                ->where('is_active', true)
                                ->orderBy('priority', 'desc')
                                ->first();
            
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return $model->delete();
    }

    /**
     * اختبار الموديل
     */
    public function testModel(AIModel $model): array
    {
        try {
            // تحديث الـ model من قاعدة البيانات للتأكد من أحدث البيانات
            $model->refresh();
            
            // التحقق من وجود API Key
            $apiKey = $model->getDecryptedApiKey();
            
            if (!$apiKey || trim($apiKey) === '') {
                return [
                    'success' => false,
                    'message' => 'API Key غير موجود. يرجى إدخال API Key أولاً ثم حفظ النموذج.',
                ];
            }

            // التحقق من وجود Model Key
            if (empty($model->model_key)) {
                return [
                    'success' => false,
                    'message' => 'Model Key غير موجود.',
                ];
            }

            return $this->runConnectionTest($model);
        } catch (\Exception $e) {
            Log::error('Error testing AI model: ' . $e->getMessage(), [
                'model_id' => $model->id,
                'provider' => $model->provider,
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'خطأ في الاختبار: ' . $e->getMessage(),
                'provider' => $model->provider,
            ];
        }
    }

    /**
     * اختبار الموديل مع API Key مباشر (للاستخدام في الاختبار المؤقت)
     */
    public function testModelWithRawApiKey(array $data, string $rawApiKey): array
    {
        try {
            // التحقق من وجود API Key
            if (!$rawApiKey || trim($rawApiKey) === '') {
                return [
                    'success' => false,
                    'message' => 'API Key غير موجود. يرجى إدخال API Key أولاً.',
                ];
            }

            // التحقق من وجود Model Key
            if (empty($data['model_key'])) {
                return [
                    'success' => false,
                    'message' => 'Model Key غير موجود.',
                ];
            }
            
            // إنشاء موديل مؤقت بدون حفظ
            $tempModel = new AIModel();
            $tempModel->fill($data);
            // تعيين API Key مباشرة للاختبار (بدون تشفير)
            $tempModel->setRawApiKeyForTesting($rawApiKey);

            return $this->runConnectionTest($tempModel);
        } catch (\Exception $e) {
            Log::error('Error testing model with raw API key: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'خطأ في الاختبار: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * الحصول على الموديل الافتراضي
     */
    public function getDefaultModel(): ?AIModel
    {
        return AIModel::default()->active()->first() 
            ?? AIModel::active()->orderBy('priority', 'desc')->first();
    }

    /**
     * الحصول على أفضل موديل لقدرة معينة
     */
    public function getBestModelFor(string $capability): ?AIModel
    {
        // أولاً: الموديل الافتراضي إذا كان يدعم القدرة
        $default = $this->getDefaultModel();
        if ($default && $default->canHandle($capability)) {
            return $default;
        }

        // ثانياً: البحث عن موديل نشط يدعم القدرة حسب الأولوية
        return AIModel::active()
                     ->byCapability($capability)
                     ->orderBy('priority', 'desc')
                     ->first();
    }

    /**
     * التبديل بين الموديلات
     */
    public function switchModel(AIModel $model): bool
    {
        if (!$model->is_active) {
            return false;
        }

        // تعيين هذا الموديل كافتراضي
        AIModel::where('is_default', true)->update(['is_default' => false]);
        return $model->update(['is_default' => true]);
    }

    /**
     * الحصول على الموديلات المتاحة
     */
    public function getAvailableModels(string $capability = 'all'): Collection
    {
        $query = AIModel::active();

        if ($capability !== 'all') {
            $query->byCapability($capability);
        }

        return $query->orderBy('priority', 'desc')->orderBy('name')->get();
    }

    protected function runConnectionTest(AIModel $model): array
    {
        $startTime = microtime(true);

        try {
            $text = $this->bridge->promptText(
                new ConnectionTestAgent(),
                'Say OK only.',
                $model,
                60
            );
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $success = stripos($text, 'ok') !== false;

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'الاتصال ناجح! API Key يعمل بشكل صحيح (Laravel AI).',
                    'response_time_ms' => $responseTime,
                    'provider' => $model->provider,
                    'model_key' => $model->model_key,
                ];
            }

            return [
                'success' => false,
                'message' => 'فشل الاتصال. الرد: '.Str::limit($text, 200),
                'response_time_ms' => $responseTime,
                'provider' => $model->provider,
                'model_key' => $model->model_key,
            ];
        } catch (\Throwable $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال: '.$e->getMessage(),
                'response_time_ms' => $responseTime,
                'provider' => $model->provider,
                'model_key' => $model->model_key,
            ];
        }
    }
}

