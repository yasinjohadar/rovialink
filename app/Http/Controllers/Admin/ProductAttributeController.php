<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $attributes = ProductAttribute::withCount('values')->orderBy('order')->orderBy('name')->paginate(15);
        return view('admin.pages.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.pages.attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,color,image',
            'order' => 'nullable|integer|min:0',
            'is_visible' => 'boolean',
        ]);
        $data = $request->only('name', 'type', 'order');
        $data['is_visible'] = $request->boolean('is_visible');
        ProductAttribute::create($data);
        return redirect()->route('admin.attributes.index')->with('success', 'تم إنشاء السمة بنجاح.');
    }

    public function edit(ProductAttribute $attribute)
    {
        return view('admin.pages.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,color,image',
            'order' => 'nullable|integer|min:0',
            'is_visible' => 'boolean',
        ]);
        $attribute->update([
            'name' => $request->name,
            'type' => $request->type,
            'order' => $request->input('order', 0),
            'is_visible' => $request->boolean('is_visible'),
        ]);
        return redirect()->route('admin.attributes.index')->with('success', 'تم تحديث السمة.');
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'تم حذف السمة.');
    }

    public function valuesIndex(ProductAttribute $attribute)
    {
        $attribute->load('values');
        return view('admin.pages.attributes.values.index', compact('attribute'));
    }

    public function valuesStore(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'color_hex' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);
        $attribute->values()->create([
            'value' => $request->value,
            'color_hex' => $request->color_hex,
            'order' => $request->input('order', 0),
        ]);
        return redirect()->route('admin.attributes.values.index', $attribute)->with('success', 'تم إضافة القيمة.');
    }

    public function valuesUpdate(Request $request, ProductAttribute $attribute, ProductAttributeValue $value)
    {
        if ($value->product_attribute_id !== $attribute->id) {
            abort(404);
        }
        $request->validate([
            'value' => 'required|string|max:255',
            'color_hex' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);
        $value->update([
            'value' => $request->value,
            'color_hex' => $request->color_hex,
            'order' => $request->input('order', 0),
        ]);
        return redirect()->route('admin.attributes.values.index', $attribute)->with('success', 'تم تحديث القيمة.');
    }

    public function valuesDestroy(ProductAttribute $attribute, ProductAttributeValue $value)
    {
        if ($value->product_attribute_id !== $attribute->id) {
            abort(404);
        }
        $value->delete();
        return redirect()->route('admin.attributes.values.index', $attribute)->with('success', 'تم حذف القيمة.');
    }
}
