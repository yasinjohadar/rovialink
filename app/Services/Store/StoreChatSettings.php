<?php

namespace App\Services\Store;

use App\Models\AISetting;

class StoreChatSettings
{
    public function isEnabled(): bool
    {
        return (bool) AISetting::getValue('store_chat_enabled', false);
    }

    public function welcomeMessage(): string
    {
        return (string) AISetting::getValue(
            'store_chat_welcome_message',
            'مرحباً! اسألني عن منتجات متجرنا.'
        );
    }

    public function refusalMessage(): string
    {
        return (string) AISetting::getValue(
            'store_chat_refusal_message',
            'عذراً، أستطيع الإجابة فقط عن منتجات متجرنا.'
        );
    }

    public function maxMessagesPerDay(): int
    {
        return max(1, (int) AISetting::getValue('store_chat_max_messages_per_day', 50));
    }

    /**
     * @return array<string, mixed>
     */
    public function publicConfig(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'welcome_message' => $this->welcomeMessage(),
            'store_name' => config('app.name', 'المتجر'),
            'csrf_token' => csrf_token(),
        ];
    }
}
