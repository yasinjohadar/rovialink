<?php

use App\Models\AISetting;
use App\Models\AIModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreChatSession;
use App\Services\Ai\DynamicAiBridge;
use App\Services\Store\StoreProductChatService;

beforeEach(function () {
    AISetting::setValue('store_chat_enabled', true, 'boolean', null, true, 'store_chat');
    AISetting::setValue('store_chat_refusal_message', 'خارج النطاق', 'string', null, true, 'store_chat');
    AISetting::setValue('store_chat_max_messages_per_day', 50, 'integer', null, false, 'store_chat');

    AIModel::create([
        'name' => 'Chat Test Model',
        'provider' => 'openai',
        'model_key' => 'gpt-4o-mini',
        'api_key' => 'sk-test',
        'max_tokens' => 4000,
        'temperature' => 0.7,
        'is_active' => true,
        'is_default' => true,
        'capabilities' => ['chat'],
    ]);
});

test('chat config returns public settings', function () {
    $this->getJson(route('frontend.chat.config'))
        ->assertOk()
        ->assertJsonPath('data.enabled', true)
        ->assertJsonStructure(['data' => ['csrf_token']]);
});

test('casual greeting gets ai reply', function () {
    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptText')
            ->once()
            ->andReturn('أهلاً وسهلاً! كيف يمكنني مساعدتك في اختيار منتج من متجرنا؟');
    });

    $session = StoreChatSession::createGuest();

    $this->withCookie(StoreProductChatService::COOKIE_NAME, $session->token)
        ->postJson(route('frontend.chat.message'), [
            'message' => 'مرحبا',
            'session_token' => $session->token,
        ])
        ->assertOk()
        ->assertJsonPath('data.refused', false)
        ->assertJsonFragment(['reply' => 'أهلاً وسهلاً! كيف يمكنني مساعدتك في اختيار منتج من متجرنا؟']);
});

test('off topic message is refused without calling ai bridge', function () {
    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldNotReceive('promptText');
    });

    $session = StoreChatSession::createGuest();

    $response = $this->withCookie(StoreProductChatService::COOKIE_NAME, $session->token)
        ->postJson(route('frontend.chat.message'), [
            'message' => 'ما هو الطقس في باريس؟',
            'session_token' => $session->token,
        ]);

    $response->assertOk()
        ->assertJsonPath('data.refused', true)
        ->assertJsonPath('data.reply', 'خارج النطاق');
});

test('product question returns ai reply', function () {
    $category = Category::create(['name' => 'Soft', 'slug' => 'soft-feature']);

    Product::create([
        'name' => 'Windows 11 Pro',
        'slug' => 'win11-feature',
        'category_id' => $category->id,
        'price' => 25,
        'status' => 'active',
        'is_visible' => true,
        'short_description' => 'مفتاح تفعيل',
    ]);

    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptText')
            ->once()
            ->andReturn('سعر Windows 11 Pro هو 25 دولار.');
    });

    $session = StoreChatSession::createGuest();

    $this->withCookie(StoreProductChatService::COOKIE_NAME, $session->token)
        ->postJson(route('frontend.chat.message'), [
            'message' => 'كم سعر Windows 11 Pro؟',
            'session_token' => $session->token,
        ])
        ->assertOk()
        ->assertJsonPath('data.refused', false)
        ->assertJsonFragment(['reply' => 'سعر Windows 11 Pro هو 25 دولار.']);
});
