<?php

namespace App\Services\Admin;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardStatsService
{
    public function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $weekStart = $today->copy()->subDays(6)->startOfDay();
        $prevWeekStart = $weekStart->copy()->subDays(7);
        $prevWeekEnd = $weekStart->copy()->subSecond();

        $ordersTotal = Order::count();
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersPending = Order::whereHas('status', fn ($q) => $q->where('slug', 'pending'))->count();

        $monthOrdersQuery = Order::query()
            ->where('created_at', '>=', $monthStart)
            ->whereHas('status', fn ($q) => $q->whereIn('slug', ['processing', 'completed', 'shipped']));

        $salesMonth = (float) (clone $monthOrdersQuery)->sum('total');
        $ordersMonth = (clone $monthOrdersQuery)->count();
        $avgOrderValue = $ordersMonth > 0 ? $salesMonth / $ordersMonth : 0.0;

        $prevMonthStart = $monthStart->copy()->subMonth();
        $prevMonthEnd = $monthStart->copy()->subSecond();
        $salesPrevMonth = (float) Order::query()
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->whereHas('status', fn ($q) => $q->whereIn('slug', ['processing', 'completed', 'shipped']))
            ->sum('total');

        $productsTotal = Product::count();
        $productsActive = Product::active()->count();

        $customersTotal = User::whereHas('orders')->count();
        $reviewsPending = Review::pending()->count();
        $reviewsTotal = Review::count();

        $ordersThisWeek = Order::where('created_at', '>=', $weekStart)->count();
        $ordersPrevWeek = Order::whereBetween('created_at', [$prevWeekStart, $prevWeekEnd])->count();

        return [
            'orders_total' => $ordersTotal,
            'orders_today' => $ordersToday,
            'orders_pending' => $ordersPending,
            'sales_month' => $salesMonth,
            'orders_month' => $ordersMonth,
            'avg_order_value' => $avgOrderValue,
            'sales_month_trend' => $this->percentTrend($salesMonth, $salesPrevMonth),
            'products_total' => $productsTotal,
            'products_active' => $productsActive,
            'customers_total' => $customersTotal,
            'reviews_pending' => $reviewsPending,
            'reviews_total' => $reviewsTotal,
            'categories_total' => Category::count(),
            'blog_posts_total' => BlogPost::count(),
            'orders_sparkline' => $this->buildDailySparkline(Order::query(), $weekStart, 7),
            'sales_sparkline' => $this->buildDailySparkline(
                Order::query()->whereHas('status', fn ($q) => $q->whereIn('slug', ['processing', 'completed', 'shipped'])),
                $weekStart,
                7,
                'total'
            ),
            'orders_week_trend' => $this->percentTrend($ordersThisWeek, $ordersPrevWeek),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getWidgets(array $stats): array
    {
        return [
            [
                'key' => 'orders',
                'title' => 'الطلبات',
                'value' => number_format($stats['orders_total']),
                'subtitle' => $stats['orders_today'].' طلب اليوم',
                'badge' => $stats['orders_pending'] > 0 ? $stats['orders_pending'].' بانتظار المعالجة' : null,
                'badge_type' => 'warning',
                'url' => route('admin.orders.index'),
                'icon' => 'shopping-bag',
                'variant' => 'violet',
                'sparkline' => $stats['orders_sparkline'],
                'trend' => $stats['orders_week_trend'],
            ],
            [
                'key' => 'sales',
                'title' => 'مبيعات الشهر',
                'value' => format_money($stats['sales_month']),
                'subtitle' => now()->translatedFormat('F Y'),
                'badge' => null,
                'badge_type' => 'success',
                'url' => route('admin.orders.index'),
                'icon' => 'trending-up',
                'variant' => 'emerald',
                'sparkline' => $stats['sales_sparkline'],
                'trend' => null,
            ],
            [
                'key' => 'reports',
                'title' => 'تقارير المتجر',
                'value' => number_format($stats['orders_month']),
                'subtitle' => format_money($stats['sales_month']).' مبيعات '.now()->translatedFormat('F'),
                'badge' => $stats['avg_order_value'] > 0
                    ? 'متوسط الطلب: '.format_money($stats['avg_order_value'])
                    : null,
                'badge_type' => 'success',
                'url' => route('admin.reports.dashboard'),
                'icon' => 'bar-chart-2',
                'variant' => 'purple',
                'sparkline' => $stats['sales_sparkline'],
                'trend' => $stats['sales_month_trend'],
            ],
            [
                'key' => 'products',
                'title' => 'المنتجات',
                'value' => number_format($stats['products_total']),
                'subtitle' => $stats['products_active'].' نشط في المتجر',
                'badge' => null,
                'badge_type' => 'info',
                'url' => route('admin.products.index'),
                'icon' => 'package',
                'variant' => 'sky',
                'sparkline' => null,
                'trend' => null,
            ],
            [
                'key' => 'customers',
                'title' => 'العملاء',
                'value' => number_format($stats['customers_total']),
                'subtitle' => 'إجمالي حسابات العملاء',
                'badge' => null,
                'badge_type' => 'info',
                'url' => route('admin.customers.index'),
                'icon' => 'users',
                'variant' => 'amber',
                'sparkline' => null,
                'trend' => null,
            ],
            [
                'key' => 'reviews',
                'title' => 'آراء العملاء',
                'value' => number_format($stats['reviews_total']),
                'subtitle' => 'كل التقييمات',
                'badge' => $stats['reviews_pending'] > 0 ? $stats['reviews_pending'].' تحتاج مراجعة' : null,
                'badge_type' => 'danger',
                'url' => route('admin.reviews.index'),
                'icon' => 'message-circle',
                'variant' => 'rose',
                'sparkline' => null,
                'trend' => null,
            ],
            [
                'key' => 'categories',
                'title' => 'التصنيفات',
                'value' => number_format($stats['categories_total']),
                'subtitle' => 'تصنيفات المنتجات',
                'badge' => null,
                'badge_type' => 'info',
                'url' => route('admin.categories.index'),
                'icon' => 'grid',
                'variant' => 'indigo',
                'sparkline' => null,
                'trend' => null,
            ],
            [
                'key' => 'blog',
                'title' => 'المدونة',
                'value' => number_format($stats['blog_posts_total']),
                'subtitle' => 'مقالات منشورة',
                'badge' => null,
                'badge_type' => 'info',
                'url' => route('admin.blog.posts.index'),
                'icon' => 'file-text',
                'variant' => 'slate',
                'sparkline' => null,
                'trend' => null,
            ],
        ];
    }

    public function recentOrders(int $limit = 5): Collection
    {
        return Order::with(['status', 'user'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function recentReviews(int $limit = 5): Collection
    {
        return Review::with('user')
            ->latest()
            ->take($limit)
            ->get();
    }

    protected function buildDailySparkline($query, Carbon $start, int $days, ?string $column = null): array
    {
        $end = $start->copy()->addDays($days - 1);
        $raw = (clone $query)
            ->whereBetween('created_at', [$start, $end->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as day, '.($column ? "SUM({$column})" : 'COUNT(*)').' as aggregate')
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $points = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $points[] = [
                'label' => $date,
                'value' => (float) ($raw[$date] ?? 0),
            ];
        }

        $max = max(1, collect($points)->max('value') ?: 1);

        return array_map(function ($point) use ($max) {
            $point['height'] = max(8, (int) round(($point['value'] / $max) * 100));

            return $point;
        }, $points);
    }

      protected function percentTrend(int|float $current, int|float $previous): ?array
    {
        if ($previous <= 0) {
            return $current > 0 ? ['direction' => 'up', 'label' => 'جديد'] : null;
        }

        $change = (($current - $previous) / $previous) * 100;
        $direction = $change >= 0 ? 'up' : 'down';

        return [
            'direction' => $direction,
            'label' => ($change >= 0 ? '+' : '').number_format($change, 0).'%',
        ];
    }
}
