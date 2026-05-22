<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Coupon::withCount('usages')->with(['products', 'categories'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($expiresFrom = $request->input('expires_from')) {
            $query->whereDate('expires_at', '>=', $expiresFrom);
        }

        if ($expiresTo = $request->input('expires_to')) {
            $query->whereDate('expires_at', '<=', $expiresTo);
        }

        $coupons = $query->paginate(15)->withQueryString();

        return view('admin.pages.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::visible()->orderBy('name')->get(['id', 'name']);
        $categories = Category::active()->ordered()->get(['id', 'name', 'parent_id']);
        return view('admin.pages.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
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
            'applicable_to' => 'required|in:entire_store,specific_products,specific_categories',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ];

        if ($request->input('applicable_to') === 'specific_products') {
            $rules['product_ids'] = 'required|array|min:1';
        }
        if ($request->input('applicable_to') === 'specific_categories') {
            $rules['category_ids'] = 'required|array|min:1';
        }

        $request->validate($rules);

        if (empty($request->input('code'))) {
            $request->merge(['code' => strtoupper(Str::random(6))]);
        }

        $coupon = Coupon::create($request->only([
            'code', 'name', 'description', 'type', 'value', 'minimum_order_amount',
            'usage_limit', 'status', 'starts_at', 'expires_at', 'applicable_to',
        ]));

        $coupon->products()->sync($request->input('product_ids', []));
        $coupon->categories()->sync($request->input('category_ids', []));

        return redirect()->route('admin.coupons.index')->with('success', 'تم إنشاء الكوبون بنجاح.');
    }

    public function edit(Coupon $coupon)
    {
        $coupon->load('products', 'categories');
        $products = Product::visible()->orderBy('name')->get(['id', 'name']);
        $categories = Category::active()->ordered()->get(['id', 'name', 'parent_id']);
        return view('admin.pages.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $rules = [
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
            'applicable_to' => 'required|in:entire_store,specific_products,specific_categories',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ];

        if ($request->input('applicable_to') === 'specific_products') {
            $rules['product_ids'] = 'required|array|min:1';
        }
        if ($request->input('applicable_to') === 'specific_categories') {
            $rules['category_ids'] = 'required|array|min:1';
        }

        $request->validate($rules);

        $coupon->update($request->only([
            'code', 'name', 'description', 'type', 'value', 'minimum_order_amount',
            'usage_limit', 'status', 'starts_at', 'expires_at', 'applicable_to',
        ]));

        $coupon->products()->sync($request->input('product_ids', []));
        $coupon->categories()->sync($request->input('category_ids', []));

        return redirect()->route('admin.coupons.index')->with('success', 'تم تحديث الكوبون بنجاح.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'تم حذف الكوبون.');
    }

    public function markAsExpired()
    {
        $coupons = Coupon::where('expires_at', '<', now())->get();
        foreach ($coupons as $coupon) {
            $coupon->update(['status' => 'expired']);
        }
        return redirect()->route('admin.coupons.index')->with('success', 'تم تعليم ' . $coupons->count() . ' كوبون منتهي الصلاحية.');
    }

    public function getUsageReport()
    {
        $coupons = Coupon::with(['usages.user'])->get();
        $report = [];
        
        foreach ($coupons as $coupon) {
            $latestUsage = $coupon->usages()->latest('used_at')->first();

            $report[] = [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'total_usage' => $coupon->usages()->count(),
                'remaining_usage' => $coupon->usage_limit !== null
                    ? max(0, $coupon->usage_limit - $coupon->usages()->count())
                    : null,
                'status' => $coupon->status,
                'is_expired' => $coupon->expires_at && $coupon->expires_at->isPast(),
                'expires_at' => $coupon->expires_at?->format('Y-m-d'),
                'latest_usage' => $latestUsage?->used_at?->format('Y-m-d H:i'),
                'created_at' => $coupon->created_at->format('Y-m-d'),
                'usage_details' => $coupon->usages->map(function ($usage) {
                        return [
                            'user' => $usage->user ? $usage->user->name : 'زائر',
                            'order_number' => $usage->order_number ?? 'N/A',
                            'used_at' => $usage->used_at?->format('Y-m-d H:i:s'),
                            'discount_amount' => $usage->discount_amount,
                        ];
                    }),
            ];
        }

        return response()->json($report);
    }
}
