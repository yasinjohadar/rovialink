<?php

namespace App\Services\Ai;

use App\Models\AIConversation;
use App\Models\AIMessage;
use App\Models\AIModel;
use App\Ai\Agents\ChatReplyAgent;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AIChatbotService
{
    public function __construct(
        private AIModelService $modelService,
        private AIPromptService $promptService,
        private DynamicAiBridge $bridge,
    ) {}

    /**
     * إنشاء محادثة جديدة
     */
    public function createConversation(
        User $user,
        ?int $courseId = null,
        ?int $lessonId = null,
        ?AIModel $model = null,
        ?string $title = null
    ): AIConversation {
        // تحديد نوع المحادثة
        $conversationType = 'general';
        if ($lessonId) {
            $conversationType = 'lesson';
        } elseif ($courseId) {
            $conversationType = 'subject';
        }

        // الحصول على الموديل
        if (!$model) {
            $model = $this->modelService->getBestModelFor('chat');
        }

        $conversation = AIConversation::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'conversation_type' => $conversationType,
            'title' => $title,
            'ai_model_id' => $model?->id,
        ]);

        // إضافة رسالة نظام
        $systemPrompt = $this->promptService->getChatbotPrompt($conversation);
        $conversation->addMessage('system', $systemPrompt);

        return $conversation;
    }

    /**
     * إرسال رسالة
     */
    public function sendMessage(
        AIConversation $conversation,
        string $message,
        ?AIModel $model = null
    ): AIMessage {
        $startTime = microtime(true);

        // الحصول على الموديل
        if (!$model) {
            $model = $conversation->model ?? $this->modelService->getBestModelFor('chat');
        }

        if (!$model) {
            throw new \Exception('لا يوجد موديل AI متاح');
        }

        // إضافة رسالة المستخدم
        $userMessage = $conversation->addMessage('user', $message);

        // الحصول على تاريخ المحادثة
        $history = $this->getConversationHistory($conversation, 20);
        $messages = $history->map(function($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        })->toArray();

        try {
            $prompt = collect($messages)
                ->map(fn (array $msg) => strtoupper($msg['role']).': '.$msg['content'])
                ->implode("\n");

            $system = $this->promptService->getChatbotPrompt($conversation);
            $agent = new ChatReplyAgent(systemInstructions: $system);
            $content = $this->bridge->promptText($agent, $prompt, $model, 120);

            $tokensUsed = (int) ceil(mb_strlen($prompt.$content) / 4);

            $assistantMessage = $conversation->addMessage('assistant', $content, [
                'tokens_used' => $tokensUsed,
            ]);

            $responseTime = (microtime(true) - $startTime) * 1000;
            $cost = $model->getCost($tokensUsed);

            $assistantMessage->update([
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'response_time' => (int) $responseTime,
            ]);

            return $assistantMessage;
        } catch (\Exception $e) {
            Log::error('Error sending AI message: ' . $e->getMessage(), [
                'conversation_id' => $conversation->id,
                'model_id' => $model->id,
            ]);

            throw $e;
        }
    }

    /**
     * الحصول على تاريخ المحادثة
     */
    public function getConversationHistory(AIConversation $conversation, int $limit = 50): Collection
    {
        return $conversation->messages()
                           ->where('role', '!=', 'system')
                           ->orderBy('created_at', 'desc')
                           ->limit($limit)
                           ->get()
                           ->reverse();
    }

    /**
     * الحصول على السياق للمحادثة
     */
    public function getContextForConversation(AIConversation $conversation): string
    {
        return $conversation->getContext();
    }

    /**
     * تقدير التكلفة
     */
    public function estimateCost(AIConversation $conversation, string $message): float
    {
        $model = $conversation->model ?? $this->modelService->getBestModelFor('chat');
        if (!$model) {
            return 0;
        }

        $estimatedTokens = (int) ceil(mb_strlen($message) / 4);

        return $model->getCost($estimatedTokens);
    }
}

