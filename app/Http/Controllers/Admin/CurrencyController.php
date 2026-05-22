<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCurrencyRequest;
use App\Http\Requests\Admin\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $currencies = Currency::ordered()->get();
        return view('admin.pages.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.pages.currencies.create');
    }

    public function store(StoreCurrencyRequest $request)
    {
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = (int) ($data['order'] ?? 0);

        Currency::create($data);

        return redirect()->route('admin.currencies.index')->with('success', 'تم إضافة العملة بنجاح.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.pages.currencies.edit', compact('currency'));
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = (int) ($data['order'] ?? 0);

        $currency->update($data);

        return redirect()->route('admin.currencies.index')->with('success', 'تم تحديث العملة بنجاح.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'لا يمكن حذف العملة الافتراضية.');
        }

        $currency->delete();
        return redirect()->route('admin.currencies.index')->with('success', 'تم حذف العملة بنجاح.');
    }

    public function setDefault(Request $request, Currency $currency)
    {
        $currency->update(['is_default' => true, 'rate_to_default' => 1]);
        Currency::where('id', '!=', $currency->id)->update(['is_default' => false]);
        return back()->with('success', 'تم تعيين العملة الافتراضية بنجاح.');
    }

    public function setDisplay(Request $request)
    {
        $code = $request->validate(['code' => 'required|string|exists:currencies,code'])['code'];
        session(['admin_display_currency' => $code]);
        return back()->with('success', 'تم تغيير عملة العرض.');
    }
}
