<?php

namespace App\Services\Store;

use App\Ai\Agents\StoreProductChatAgent;
use App\Models\StoreChatMessage;
use App\Models\StoreChatSession;
use App\Services\Ai\AIModelService;
use App\Services\Ai\DynamicAiBridge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreProductChatService
{
    public const COOKIE_NAME = 'store_chat_session';

    public function __construct(
        protected DynamicAiBridge $bridge,
        protected AIModelService $modelService,
        protected ProductCatalogContextBuilder $catalogBuilder,
        protected StoreChatTopicGuard $topicGuard,
        protected StoreChatSettings $settings,
    ) {}

    public function resolveSession(Request $request, ?string $token = null): StoreChatSession
    {
        $token = $token ?: $request->cookie(self::COOKIE_NAME);

        if ($token) {
            $session = StoreChatSession::query()->where('token', $token)->first();
            if ($session) {
                if ($request->user() && ! $session->user_id) {
                    $session->update(['user_id' => $request->user()->id]);
                }

                return $session;
            }
        }

        return StoreChatSession::createGuest(
            userId: $request->user()?->id,
            ipHash: $this->hashIp($request->ip()),
            userAgent: $request->userAgent(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function sendMessage(StoreChatSession $session, string $message, ?string $productSlug = null): array
    {
        if (! $this->settings->isEnabled()) {
            throw new \RuntimeException('ودجت المحادثة غير مفعّل حالياً.');
        }

        if ($session->dailyMessageCount() >= $this->settings->maxMessagesPerDay()) {
            throw new \RuntimeException('وصلت للحد الأقصى من الرسائل اليوم. حاول غداً.');
        }

        $message = trim($message);
        if ($message === '') {
            throw new \InvalidArgumentException('الرسالة فارغة.');
        }

        $session->messages()->create([
            'role' => 'user',
            'content' => $message,
        ]);
        $session->incrementDailyMessageCount();

        if ($this->topicGuard->isClearlyOffTopic($message)) {
            $refusal = $this->settings->refusalMessage();
            $session->messages()->create([
                'role' => 'assistant',
                'content' => $refusal,
                'metadata' => ['refused' => true],
            ]);

            return [
                'reply' => $refusal,
                'refused' => true,
                'suggested_products' => [],
            ];
        }

        $catalog = $this->catalogBuilder->build($message, $productSlug);
        $model = $this->modelService->getBestModelFor('chat');

        if (! $model) {
            throw new \RuntimeException('لا يوجد نموذج ذكاء اصطناعي للمحادثة. يرجى ضبط نموذج بقدرة chat من لوحة الإدارة.');
        }

        $history = $this->formatHistory($session);
        $agent = new StoreProductChatAgent(
            catalogContext: $catalog['context_text'],
            storeName: config('app.name', 'المتجر'),
        );

        $prompt = $history !== ''
            ? "سجل المحادثة:\n{$history}\n\nرسالة العميل الأخيرة: {$message}"
            : $message;

        try {
            $reply = trim($this->bridge->promptText($agent, $prompt, $model, 90));
        } catch (\Throwable $e) {
            Log::error('Store chat AI failed: '.$e->getMessage(), ['session_id' => $session->id]);
            throw new \RuntimeException('تعذر الحصول على رد الآن. حاول بعد قليل.');
        }

        if ($reply === '') {
            $reply = 'عذراً، لم أتمكن من صياغة رد. جرّب إعادة صياغة سؤالك عن منتج معيّن.';
        }

        $session->messages()->create([
            'role' => 'assistant',
            'content' => $reply,
            'metadata' => ['suggested_products' => $catalog['products']],
        ]);

        return [
            'reply' => $reply,
            'refused' => false,
            'suggested_products' => $catalog['products'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getHistory(StoreChatSession $session, int $limit = 20): array
    {
        return $session->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->sortBy('created_at')
            ->values()
            ->map(fn (StoreChatMessage $m) => [
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => $m->created_at?->toIso8601String(),
            ])
            ->all();
    }

    protected function formatHistory(StoreChatSession $session): string
    {
        $messages = $session->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->sortBy('created_at');

        return $messages->map(function (StoreChatMessage $m) {
            $label = $m->role === 'user' ? 'عميل' : 'مساعد';

            return "{$label}: {$m->content}";
        })->implode("\n");
    }

    protected function hashIp(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        return hash('sha256', $ip.config('app.key'));
    }
}
