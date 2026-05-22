<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaxClassRequest;
use App\Http\Requests\Admin\UpdateTaxClassRequest;
use App\Http\Requests\Admin\StoreTaxRateRequest;
use App\Http\Requests\Admin\UpdateTaxRateRequest;
use App\Models\TaxClass;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $classes = TaxClass::withCount('rates')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('admin.pages.tax.index', compact('classes'));
    }

    public function createClass()
    {
        return view('admin.pages.tax.class-create');
    }

    public function storeClass(StoreTaxClassRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            TaxClass::where('is_default', true)->update(['is_default' => false]);
        }

        TaxClass::create($data);

        return redirect()->route('admin.tax.index')->with('success', 'تم إنشاء فئة الضريبة بنجاح.');
    }

    public function editClass(TaxClass $tax_class)
    {
        return view('admin.pages.tax.class-edit', ['taxClass' => $tax_class]);
    }

    public function updateClass(UpdateTaxClassRequest $request, TaxClass $tax_class)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            TaxClass::where('id', '!=', $tax_class->id)->update(['is_default' => false]);
        }

        $tax_class->update($data);

        return redirect()->route('admin.tax.index')->with('success', 'تم تحديث فئة الضريبة بنجاح.');
    }

    public function destroyClass(TaxClass $tax_class)
    {
        if ($tax_class->is_default) {
            return back()->with('error', 'لا يمكن حذف فئة الضريبة الافتراضية.');
        }

        $tax_class->delete();

        return redirect()->route('admin.tax.index')->with('success', 'تم حذف فئة الضريبة بنجاح.');
    }

    public function rates(TaxClass $tax_class)
    {
        $tax_class->load('rates');
        return view('admin.pages.tax.rates', ['taxClass' => $tax_class]);
    }

    public function storeRate(StoreTaxRateRequest $request, TaxClass $tax_class)
    {
        $data = $request->validated();
        $data['is_compound'] = $request->boolean('is_compound');
        $data['is_inclusive'] = $request->boolean('is_inclusive');
        $data['is_active'] = $request->boolean('is_active');

        $tax_class->rates()->create($data);

        return back()->with('success', 'تم إضافة معدل الضريبة بنجاح.');
    }

    public function updateRate(UpdateTaxRateRequest $request, TaxClass $tax_class, TaxRate $rate)
    {
        if ($rate->tax_class_id !== $tax_class->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['is_compound'] = $request->boolean('is_compound');
        $data['is_inclusive'] = $request->boolean('is_inclusive');
        $data['is_active'] = $request->boolean('is_active');

        $rate->update($data);

        return back()->with('success', 'تم تحديث معدل الضريبة بنجاح.');
    }

    public function destroyRate(TaxClass $tax_class, TaxRate $rate)
    {
        if ($rate->tax_class_id !== $tax_class->id) {
            abort(404);
        }

        $rate->delete();

        return back()->with('success', 'تم حذف معدل الضريبة بنجاح.');
    }
}

