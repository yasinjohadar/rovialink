<?php

use App\Models\AIModel;
use App\Models\User;
use App\Services\Ai\AIBlogPostService;
use App\Services\Ai\DynamicAiBridge;
function createActiveAiModel(array $overrides = []): AIModel
{
    return AIModel::create(array_merge([
        'name' => 'Test AI',
        'provider' => 'openai',
        'model_key' => 'gpt-4o-mini',
        'api_key' => 'sk-test-key',
        'max_tokens' => 2000,
        'temperature' => 0.7,
        'is_active' => true,
        'is_default' => true,
        'priority' => 10,
        'capabilities' => ['blog_generation', 'product_copy', 'seo_generation'],
    ], $overrides));
}

test('blog ai generate returns structured content', function () {
    $user = User::factory()->create(['is_active' => true]);
    $model = createActiveAiModel();

    $this->mock(AIBlogPostService::class, function ($mock) {
        $mock->shouldReceive('generateBlogPost')
            ->once()
            ->andReturn([
                'title' => 'مقال تجريبي',
                'slug' => 'mqal-tjryby',
                'content' => '<p>محتوى المقال</p>',
                'excerpt' => 'مقتطف',
                'meta_title' => 'عنوان SEO',
                'meta_description' => 'وصف SEO',
                'meta_keywords' => 'كلمة1, كلمة2',
                'focus_keyword' => 'كلمة',
            ]);
    });

    $response = $this->actingAs($user)->postJson(route('admin.blog.ai-posts.generate'), [
        'topic' => 'Test blog topic',
        'ai_model_id' => $model->id,
        'content_length' => 'short',
        'generate_seo' => 1,
        'generate_og' => 1,
        'generate_twitter' => 1,
        'generate_schema' => 1,
        'generate_keyword_synonyms' => 1,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'مقال تجريبي');
});

test('product ai generate copy short step returns short description', function () {
    $user = User::factory()->create(['is_active' => true]);
    $model = createActiveAiModel();

    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptStructured')
            ->once()
            ->andReturn(['short_description' => 'وصف قصير']);
    });

    $this->actingAs($user)->postJson(route('admin.ai.products.generate-copy'), [
        'name' => 'منتج تجريبي',
        'ai_model_id' => $model->id,
        'step' => 'short',
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.short_description', 'وصف قصير');
});

test('product ai generate copy description step returns html', function () {
    $user = User::factory()->create(['is_active' => true]);
    $model = createActiveAiModel();

    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptStructured')
            ->once()
            ->andReturn([
                'description' => '<p>'.str_repeat('وصف كامل للمنتج. ', 40).'</p>',
            ]);
    });

    $this->actingAs($user)->postJson(route('admin.ai.products.generate-copy'), [
        'name' => 'منتج تجريبي',
        'ai_model_id' => $model->id,
        'step' => 'description',
        'short_description' => 'وصف قصير',
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('step', 'description');
});

test('product ai generate seo returns meta fields', function () {
    $user = User::factory()->create(['is_active' => true]);
    $model = createActiveAiModel();

    $this->mock(DynamicAiBridge::class, function ($mock) {
        $mock->shouldReceive('promptStructured')
            ->once()
            ->andReturn([
                'meta_title' => 'عنوان المنتج',
                'meta_description' => 'وصف المنتج',
                'meta_keywords' => 'منتج, تجريبي',
            ]);
    });

    $response = $this->actingAs($user)->postJson(route('admin.ai.products.generate-seo'), [
        'name' => 'منتج تجريبي',
        'description' => 'وصف للمنتج',
        'ai_model_id' => $model->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.meta_title', 'عنوان المنتج');
});

test('blog ai generate fails when no active model exists', function () {
    $user = User::factory()->create(['is_active' => true]);

    AIModel::query()->update(['is_active' => false, 'is_default' => false]);

    $response = $this->actingAs($user)->postJson(route('admin.blog.ai-posts.generate'), [
        'topic' => 'Test topic',
        'content_length' => 'short',
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('success', false);
});
