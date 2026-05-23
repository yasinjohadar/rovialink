<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Services\Storage\StorageHelperService;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(
        protected StorageHelperService $storageHelper
    ) {
        $this->middleware('auth');
        $this->middleware('permission:category-list')->only('index');
        $this->middleware('permission:category-create')->only(['create', 'store']);
        $this->middleware('permission:category-edit')->only(['edit', 'update']);
        $this->middleware('permission:category-delete')->only('destroy');
        $this->middleware('permission:category-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoriesQuery = Category::with('parent')->orderBy('order')->orderBy('name');

        // البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $categoriesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('slug', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $categoriesQuery->where('status', $request->input('status'));
        }

        // فلترة حسب التصنيف الأب
        if ($request->filled('parent_id')) {
            if ($request->input('parent_id') == 'null') {
                $categoriesQuery->whereNull('parent_id');
            } else {
                $categoriesQuery->where('parent_id', $request->input('parent_id'));
            }
        }

        $categories = $categoriesQuery->paginate(15);
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.pages.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.pages.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        // معالجة الصورة
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($data['name']) . '.' . $image->getClientOriginalExtension();
            $data['image'] = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'categories/images',
                $image,
                'image',
                $imageName
            ) ?: null;
        }

        // معالجة صورة الغلاف
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '_cover_' . Str::slug($data['name']) . '.' . $coverImage->getClientOriginalExtension();
            $data['cover_image'] = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'categories/covers',
                $coverImage,
                'image',
                $coverImageName
            ) ?: null;
        }

        // إنشاء slug إذا لم يتم توفيره
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with([
            'parent',
            'children',
            'products' => fn ($q) => $q->with(['brand', 'images'])->orderBy('order')->orderByDesc('id'),
        ])->findOrFail($id);

        return view('admin.pages.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
        
        return view('admin.pages.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();

        // معالجة صورة التصنيف
        if ($request->hasFile('image')) {
            if ($category->image) {
                $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $category->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($data['name']) . '.' . $image->getClientOriginalExtension();
            $data['image'] = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'categories/images',
                $image,
                'image',
                $imageName
            ) ?: $category->image;
        }

        // معالجة صورة الغلاف
        if ($request->hasFile('cover_image')) {
            if ($category->cover_image) {
                $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $category->cover_image);
            }

            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '_cover_' . Str::slug($data['name']) . '.' . $coverImage->getClientOriginalExtension();
            $data['cover_image'] = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'categories/covers',
                $coverImage,
                'image',
                $coverImageName
            ) ?: $category->cover_image;
        }

        // إنشاء slug إذا لم يتم توفيره
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // التحقق من وجود تصنيفات فرعية
        if ($category->hasChildren()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على تصنيفات فرعية');
        }

        // حذف الصور
        if ($category->image) {
            $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $category->image);
        }

        if ($category->cover_image) {
            $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $category->cover_image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}
