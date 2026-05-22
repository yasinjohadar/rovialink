<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Models\SystemSetting;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltySettingsController extends Controller
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $settings = $this->loyaltyService->getSettings();
        $orderStatuses = OrderStatus::orderBy('order')->get();

        return view('admin.pages.loyalty.settings', compact('settings', 'orderStatuses'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'loyalty_points_per_currency' => 'required|integer|min:0',
            'loyalty_redemption_rate' => 'required|integer|min:1',
            'loyalty_min_order_to_redeem' => 'required|numeric|min:0',
            'loyalty_max_points_per_order' => 'required|integer|min:0',
            'loyalty_award_on_status' => 'required|string|max:50',
        ]);

        foreach ($data as $key => $value) {
            $type = in_array($key, ['loyalty_points_per_currency', 'loyalty_redemption_rate', 'loyalty_max_points_per_order'], true)
                ? 'integer'
                : ($key === 'loyalty_min_order_to_redeem' ? 'float' : 'string');
            SystemSetting::set($key, (string) $value, $type, 'loyalty');
        }

        return redirect()
            ->route('admin.loyalty.settings.index')
            ->with('success', 'تم حفظ إعدادات نقاط الولاء بنجاح.');
    }
}
