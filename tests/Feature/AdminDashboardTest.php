<?php

use App\Models\User;
use App\Services\Admin\DashboardStatsService;

test('dashboard loads with stat widgets for authenticated user', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('الطلبات')
        ->assertSee('مبيعات الشهر')
        ->assertSee('المنتجات');
});

test('dashboard stats service returns widget collection', function () {
    $service = app(DashboardStatsService::class);
    $stats = $service->getStats();
    $widgets = $service->getWidgets($stats);

    expect($stats)->toHaveKeys(['orders_total', 'sales_month', 'products_total'])
        ->and($widgets)->toHaveCount(8)
        ->and($widgets[0]['key'])->toBe('orders');
});
