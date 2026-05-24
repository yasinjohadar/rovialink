<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardStatsService $dashboardStats
    ) {}

    public function index(): View
    {
        $stats = $this->dashboardStats->getStats();

        return view('admin.dashboard', [
            'widgets' => $this->dashboardStats->getWidgets($stats),
            'recentOrders' => $this->dashboardStats->recentOrders(),
            'recentReviews' => $this->dashboardStats->recentReviews(),
        ]);
    }
}
