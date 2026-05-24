<?php

use App\Models\AIModel;
use App\Models\User;
use App\Services\Ai\DynamicAiBridge;

function createSeoTestModel(): AIModel
{
    return AIModel::create([
        'name' => 'SEO Test',
        'provider' => 'openai',
        'model_key' => 'gpt-4o-mini',
        'api_key' => 'sk-test',
        'max_tokens' => 8000,
        'temperature' => 0.7,
        'is_active' => true,
        'is_default' => true,
        'capabilities' => ['seo_generation'],
    ]);
}

test('seo audit returns score and checks', function () {
    $user = User::factory()->create(['is_active' => true]);
    $model = createSeoTestModel();

    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptStructured')
            ->once()
            ->andReturn([
                'overall_summary' => 'ملخص تحليلي',
                'recommendation_titles' => ['تحسين العنوان'],
                'recommendation_details' => ['وسّع meta_title'],
                'recommendation_priorities' => ['high'],
                'recommendation_fields' => ['meta_title'],
                'quick_wins' => ['أضف كلمات مفتاحية'],
            ]);
    });

    $response = $this->actingAs($user)->postJson(route('admin.ai.seo.audit'), [
        'type' => 'product',
        'name' => 'منتج',
        'meta_title' => '',
        'meta_description' => str_repeat('و', 130),
        'ai_model_id' => $model->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['score', 'checks', 'recommendations']]);
});

test('seo apply returns meta fields', function () {
    $user = User::factory()->create(['is_active' => true]);
    $model = createSeoTestModel();

    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptStructured')
            ->once()
            ->andReturn([
                'meta_title' => 'عنوان محسّن للمنتج التجريبي',
                'meta_description' => str_repeat('وصف ', 30),
                'meta_keywords' => 'منتج, تجريبي',
            ]);
    });

    $response = $this->actingAs($user)->postJson(route('admin.ai.seo.apply'), [
        'type' => 'product',
        'name' => 'منتج تجريبي',
        'recommendations' => [
            ['title' => 'عنوان', 'detail' => 'حسّن العنوان', 'priority' => 'high', 'affected_fields' => 'meta_title'],
        ],
        'ai_model_id' => $model->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.meta_title', 'عنوان محسّن للمنتج التجريبي');
});
