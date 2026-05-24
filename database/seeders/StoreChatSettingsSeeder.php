<?php

namespace Database\Seeders;

use App\Models\AISetting;
use Illuminate\Database\Seeder;

class StoreChatSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'store_chat_enabled',
                'value' => false,
                'type' => 'boolean',
                'description' => 'تفعيل ودجت محادثة المنتجات في المتجر',
                'is_public' => true,
                'category' => 'store_chat',
            ],
            [
                'key' => 'store_chat_welcome_message',
                'value' => 'مرحباً! أنا مساعد المتجر. اسألني عن منتجاتنا (الأسعار، المميزات، التوافق) وسأساعدك.',
                'type' => 'string',
                'description' => 'رسالة الترحيب في الودجت',
                'is_public' => true,
                'category' => 'store_chat',
            ],
            [
                'key' => 'store_chat_refusal_message',
                'value' => 'عذراً، أستطيع الإجابة فقط عن منتجات متجرنا. اسألني عن منتج معيّن أو تصفّح المتجر.',
                'type' => 'string',
                'description' => 'رسالة رفض المواضيع خارج المنتجات',
                'is_public' => true,
                'category' => 'store_chat',
            ],
            [
                'key' => 'store_chat_max_messages_per_day',
                'value' => 50,
                'type' => 'integer',
                'description' => 'الحد الأقصى لرسائل المستخدم في اليوم لكل جلسة',
                'is_public' => false,
                'category' => 'store_chat',
            ],
        ];

        foreach ($defaults as $row) {
            AISetting::updateOrCreate(
                ['key' => $row['key']],
                $row
            );
        }
    }
}
