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
        ->assertSee('المنتجات')
        ->assertSee('تقارير المتجر');
});

test('dashboard stats service returns widget collection', function () {
    $service = app(DashboardStatsService::class);
    $stats = $service->getStats();
    $widgets = $service->getWidgets($stats);

    expect($stats)->toHaveKeys(['orders_total', 'sales_month', 'orders_month', 'avg_order_value'])
        ->and($widgets)->toHaveCount(8)
        ->and(collect($widgets)->firstWhere('key', 'reports')['url'])->toBe(route('admin.reports.dashboard'));
});
