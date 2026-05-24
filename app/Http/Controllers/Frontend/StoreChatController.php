<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Store\StoreChatSettings;
use App\Services\Store\StoreProductChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreChatController extends Controller
{
    public function __construct(
        protected StoreProductChatService $chatService,
        protected StoreChatSettings $settings,
    ) {}

    public function config(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->settings->publicConfig(),
        ]);
    }

    public function session(Request $request): JsonResponse
    {
        if (! $this->settings->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'ودجت المحادثة غير مفعّل.',
            ], 403);
        }

        $session = $this->chatService->resolveSession($request);

        return $this->withSessionCookie(response()->json([
            'success' => true,
            'data' => [
                'session_token' => $session->token,
                'history' => $this->chatService->getHistory($session),
            ],
        ]), $session->token);
    }

    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'product_slug' => 'nullable|string|max:255',
            'session_token' => 'nullable|uuid',
        ]);

        try {
            $cookieToken = $request->cookie(StoreProductChatService::COOKIE_NAME);
            $bodyToken = $validated['session_token'] ?? null;

            if ($cookieToken && $bodyToken && $cookieToken !== $bodyToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'جلسة غير صالحة. أعد فتح المحادثة.',
                ], 403);
            }

            $session = $this->chatService->resolveSession(
                $request,
                $cookieToken ?? $bodyToken
            );

            $result = $this->chatService->sendMessage(
                $session,
                $validated['message'],
                $validated['product_slug'] ?? null,
            );

            return $this->withSessionCookie(response()->json([
                'success' => true,
                'data' => $result,
            ]), $session->token);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::warning('Store chat message error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $session = $this->chatService->resolveSession($request);

        if ($request->cookie(StoreProductChatService::COOKIE_NAME) !== $session->token) {
            return response()->json(['success' => true, 'data' => ['history' => []]]);
        }

        return $this->withSessionCookie(response()->json([
            'success' => true,
            'data' => ['history' => $this->chatService->getHistory($session)],
        ]), $session->token);
    }

    protected function withSessionCookie(JsonResponse $response, string $token): JsonResponse
    {
        return $response->cookie(
            StoreProductChatService::COOKIE_NAME,
            $token,
            60 * 24 * 30,
            '/',
            null,
            (bool) config('session.secure', false),
            true,
            false,
            'lax'
        );
    }
}
