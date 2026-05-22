<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $ordersQuery = Order::with('status')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('status', function ($q) {
                $q->whereIn('slug', ['processing', 'completed']);
            });

        $orders = $ordersQuery->get();

        $totalSales = (float) $orders->sum('total');
        $ordersCount = $orders->count();
        $averageOrderValue = $ordersCount > 0 ? $totalSales / $ordersCount : 0.0;
        $totalDiscounts = (float) $orders->sum('discount_amount');

        $kpis = [
            'total_sales' => $totalSales,
            'orders_count' => $ordersCount,
            'average_order_value' => $averageOrderValue,
            'total_discounts' => $totalDiscounts,
        ];

        $salesSeries = $this->buildSalesSeries($orders, $startDate, $endDate);

        $topProducts = $this->buildTopProducts($startDate, $endDate);
        $couponSummary = $this->buildCouponSummary($startDate, $endDate);

        $filters = [
            'start' => $startDate->toDateString(),
            'end' => $endDate->toDateString(),
            'preset' => $request->input('preset', 'last_30_days'),
        ];

        return view('admin.pages.reports.dashboard', [
            'filters' => $filters,
            'kpis' => $kpis,
            'salesSeries' => $salesSeries,
            'topProducts' => $topProducts,
            'couponSummary' => $couponSummary,
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $preset = $request->input('preset', 'last_30_days');
        $today = Carbon::today();

        switch ($preset) {
            case 'today':
                $start = $today;
                $end = $today;
                break;
            case 'last_7_days':
                $start = $today->copy()->subDays(6);
                $end = $today;
                break;
            case 'this_month':
                $start = $today->copy()->startOfMonth();
                $end = $today;
                break;
            case 'custom':
                $start = $request->filled('start') ? Carbon::parse($request->input('start')) : $today->copy()->subDays(29);
                $end = $request->filled('end') ? Carbon::parse($request->input('end')) : $today;
                break;
            case 'last_30_days':
            default:
                $start = $today->copy()->subDays(29);
                $end = $today;
                break;
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy(), $start->copy()];
        }

        return [$start->startOfDay(), $end->endOfDay()];
    }

    private function buildSalesSeries($orders, Carbon $start, Carbon $end): array
    {
        $period = [];
        $cursor = $start->copy()->startOfDay();
        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $period[$key] = [
                'date' => $key,
                'total' => 0.0,
                'count' => 0,
            ];
            $cursor->addDay();
        }

        foreach ($orders as $order) {
            $day = $order->created_at->toDateString();
            if (!isset($period[$day])) {
                continue;
            }
            $period[$day]['total'] += (float) $order->total;
            $period[$day]['count']++;
        }

        return array_values($period);
    }

    private function buildTopProducts(Carbon $start, Carbon $end, int $limit = 10): array
    {
        return \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereIn('order_statuses.slug', ['processing', 'completed'])
            ->selectRaw('
                order_items.product_id,
                order_items.product_name,
                order_items.sku,
                categories.name as category_name,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.total) as total_revenue
            ')
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name'
            )
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function buildCouponSummary(Carbon $start, Carbon $end): array
    {
        $query = CouponUsage::query()
            ->join('coupons', 'coupon_usages.coupon_id', '=', 'coupons.id')
            ->whereBetween('coupon_usages.used_at', [$start, $end])
            ->selectRaw('
                coupons.code,
                coupons.name,
                coupons.type,
                coupons.value,
                COUNT(coupon_usages.id) as usage_count,
                SUM(coupon_usages.discount_amount) as total_discount
            ')
            ->groupBy('coupons.code', 'coupons.name', 'coupons.type', 'coupons.value')
            ->orderByDesc('total_discount');

        $rows = $query->get();

        $totalDiscount = (float) $rows->sum('total_discount');

        return [
            'rows' => $rows->toArray(),
            'total_discount' => $totalDiscount,
        ];
    }
}

