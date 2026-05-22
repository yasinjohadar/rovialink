<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $coupons = Coupon::withCount('usages')->latest()->paginate(15);
        return view('admin.store.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.store.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,expired',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        Coupon::create($request->validated());
        return redirect()->route('admin.store.coupons.index')->with('success', 'تم إنشاء الكوبون بنجاح.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.store.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,expired',
            'starts_at' => 'nullable|date|after_or_equal:starts_at',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $coupon->update($request->validated());
        return redirect()->route('admin.store.coupons.index')->with('success', 'تم تحديث الكوبون بنجاح.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.store.coupons.index')->with('success', 'تم حذف الكوبون.');
    }
}
