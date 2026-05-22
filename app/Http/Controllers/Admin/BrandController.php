<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Brand::query()->orderBy('order')->orderBy('name');

        if ($request->filled('query')) {
            $q = $request->input('query');
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%$q%")
                    ->orWhere('slug', 'like', "%$q%");
            });
        }

        $brands = $query->paginate(15);
        return view('admin.pages.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.pages.brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        Brand::create($data);
        return redirect()->route('admin.brands.index')->with('success', 'تم إضافة الماركة بنجاح.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.pages.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand->update($data);
        return redirect()->route('admin.brands.index')->with('success', 'تم تحديث الماركة بنجاح.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'تم حذف الماركة بنجاح.');
    }
}
