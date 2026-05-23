<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\Storage\StorageHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct(
        protected StorageHelperService $storageHelper
    ) {
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

        $data['show_on_homepage'] = $request->boolean('show_on_homepage');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.Str::slug($data['name']).'.'.$image->getClientOriginalExtension();
            $data['image'] = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'brands',
                $image,
                'image',
                $imageName
            ) ?: null;
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

        $data['show_on_homepage'] = $request->boolean('show_on_homepage');

        if ($request->hasFile('image')) {
            if ($brand->image) {
                $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $brand->image);
            }
            $image = $request->file('image');
            $imageName = time().'_'.Str::slug($data['name']).'.'.$image->getClientOriginalExtension();
            $data['image'] = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'brands',
                $image,
                'image',
                $imageName
            ) ?: $brand->image;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'تم تحديث الماركة بنجاح.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->image) {
            $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $brand->image);
        }
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'تم حذف الماركة بنجاح.');
    }
}
