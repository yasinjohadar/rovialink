<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Services\Ai\AIModelService;
use App\Models\ProductAttribute;
use App\Models\ProductFile;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Services\CurrencyService;
use App\Services\Storage\StorageHelperService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        protected StorageHelperService $storageHelper
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $products = $this->filteredProductsQuery($request)
            ->paginate($this->resolveProductsPerPage($request))
            ->withQueryString();
        $categories = Category::orderBy('name')->get();
        $currencyService = app(CurrencyService::class);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.pages.products.partials.table', compact('products', 'currencyService'))->render(),
            ]);
        }

        return view('admin.pages.products.index', compact('products', 'categories', 'currencyService'));
    }

    private function filteredProductsQuery(Request $request)
    {
        $query = Product::with(['category', 'images'])->orderBy('order')->orderByDesc('created_at');

        if ($request->filled('query')) {
            $q = $request->input('query');
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        return $query;
    }

    private function resolveProductsPerPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', 15);

        return in_array($perPage, [10, 15, 25, 50, 100], true) ? $perPage : 15;
    }

    public function duplicate(Product $product)
    {
        $product->loadMissing(['category', 'attributes', 'images', 'files']);

        $new = $product->replicate([
            'slug',
            'sku',
            'status',
            'is_featured',
            'created_at',
            'updated_at',
        ]);
        $new->allow_reviews = $product->allow_reviews;
        $new->reviews_require_approval = $product->reviews_require_approval;

        $new->name = $product->name . ' (نسخة)';
        $new->status = 'draft';
        $new->slug = Str::slug($new->name) . '-' . uniqid();
        $new->sku = null;
        $new->is_featured = false;
        $new->save();

        $new->attributes()->sync($product->attributes->pluck('id')->all());

        foreach ($product->images as $image) {
            $new->images()->create([
                'path' => $image->path,
                'is_primary' => $image->is_primary,
                'order' => $image->order,
            ]);
        }

        foreach ($product->files as $file) {
            $new->files()->create([
                'title' => $file->title,
                'path' => $file->path,
                'downloadable' => $file->downloadable,
                'order' => $file->order,
            ]);
        }

        return redirect()
            ->route('admin.products.edit', $new)
            ->with('success', 'تم استنساخ المنتج، يمكنك الآن تعديل النسخة الجديدة.');
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
            'action' => ['required', 'in:activate,draft,hide,delete'],
        ]);

        if ($data['action'] === 'delete') {
            $products = Product::with(['images', 'files'])->whereIn('id', $data['ids'])->get();
            $count = $products->count();

            foreach ($products as $product) {
                $this->deleteProduct($product);
            }

            return redirect()->route('admin.products.index')->with(
                'success',
                'تم حذف '.$count.' منتج(ات) بنجاح.'
            );
        }

        $query = Product::whereIn('id', $data['ids']);

        switch ($data['action']) {
            case 'activate':
                $query->update(['status' => 'active', 'is_visible' => true]);
                $message = 'تم تفعيل المنتجات المحددة.';
                break;
            case 'draft':
                $query->update(['status' => 'draft']);
                $message = 'تم تحويل المنتجات المحددة إلى مسودة.';
                break;
            case 'hide':
                $query->update(['is_visible' => false]);
                $message = 'تم إخفاء المنتجات المحددة من المتجر.';
                break;
            default:
                $message = 'تم تنفيذ العملية.';
        }

        return redirect()->route('admin.products.index')->with('success', $message);
    }

    public function create(Request $request, AIModelService $modelService)
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('order')->orderBy('name')->get();
        $attributes = ProductAttribute::with('values')->orderBy('order')->get();
        $selectedCategoryId = $request->query('category_id');
        $aiModels = $modelService->getAvailableModels('all');

        return view('admin.pages.products.create', compact('categories', 'brands', 'attributes', 'selectedCategoryId', 'aiModels'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        unset($data['attribute_ids'], $data['digital_files']);
        $product = Product::create($data);

        $product->attributes()->sync($request->input('attribute_ids', []));
        $this->syncDigitalFiles($product, $request);

        $galleryOrder = 0;

        if ($request->hasFile('primary_image')) {
            if (! $this->setPrimaryImage($product, $request->file('primary_image'))) {
                return redirect()->back()->withInput()->with('error', 'فشل رفع الصورة الرئيسية.');
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $this->storageHelper->storeUploadedFileWithFailover(
                    $this->storageHelper->mediaDisk(),
                    'products/' . $product->id,
                    $file,
                    'image'
                );
                if (! $path) {
                    continue;
                }
                $product->images()->create([
                    'path' => $path,
                    'order' => $galleryOrder + $index,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'تم إنشاء المنتج. يمكنك الآن إضافة المتغيرات أدناه.');
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'images',
            'variants.attributeValues.attribute',
            'reviews',
        ]);
        return view('admin.pages.products.show', compact('product'));
    }

    public function edit(Product $product, AIModelService $modelService)
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('order')->orderBy('name')->get();
        $attributes = ProductAttribute::with('values')->orderBy('order')->get();
        $product->load(['images', 'primaryImage', 'galleryImages', 'attributes', 'variants.attributeValues', 'files']);
        $aiModels = $modelService->getAvailableModels('all');

        return view('admin.pages.products.edit', compact('product', 'categories', 'brands', 'attributes', 'aiModels'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        unset($data['attribute_ids'], $data['variants'], $data['digital_files']);

        foreach (['meta_title', 'meta_description', 'meta_keywords'] as $seoField) {
            if ($request->has($seoField)) {
                $data[$seoField] = $request->input($seoField);
            }
        }

        $product->update($data);

        $product->attributes()->sync($request->input('attribute_ids', []));
        $this->syncDigitalFiles($product, $request);

        $submittedVariants = $request->input('variants', []);
        $submittedIds = collect($submittedVariants)->pluck('id')->filter()->values()->toArray();
        $product->variants()->whereNotIn('id', $submittedIds)->delete();

        foreach ($submittedVariants as $row) {
            $attributeValueIds = $row['attribute_value_ids'] ?? [];
            $attributeValueIds = is_array($attributeValueIds) ? array_filter(array_map('intval', $attributeValueIds)) : [];

            $payload = [
                'price' => isset($row['price']) ? (float) $row['price'] : $product->price,
                'sku' => $row['sku'] ?? null,
                'is_default' => false,
            ];
            if (isset($row['id']) && $row['id']) {
                $variant = $product->variants()->find($row['id']);
                if ($variant) {
                    $variant->update($payload);
                    $variant->attributeValues()->sync($attributeValueIds);
                    continue;
                }
            }
            $variant = $product->variants()->create($payload);
            $variant->attributeValues()->sync($attributeValueIds);
        }

        $defaultVariant = $product->variants()->first();
        if ($defaultVariant) {
            $defaultVariant->update(['is_default' => true]);
        }

        if ($request->hasFile('primary_image')) {
            if (! $this->setPrimaryImage($product, $request->file('primary_image'))) {
                return redirect()->back()->withInput()->with('error', 'فشل رفع الصورة الرئيسية.');
            }
        }

        if ($request->hasFile('images')) {
            $maxOrder = (int) $product->galleryImages()->max('order');
            foreach ($request->file('images') as $file) {
                $path = $this->storageHelper->storeUploadedFileWithFailover(
                    $this->storageHelper->mediaDisk(),
                    'products/' . $product->id,
                    $file,
                    'image'
                );
                if (! $path) {
                    continue;
                }
                $product->images()->create([
                    'path' => $path,
                    'order' => ++$maxOrder,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function destroy(Product $product)
    {
        $product->load(['images', 'files']);
        $this->deleteProduct($product);

        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج.');
    }

    protected function deleteProduct(Product $product): void
    {
        foreach ($product->images as $img) {
            $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $img->path);
        }

        foreach ($product->files as $file) {
            if ($file->path && Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
        }

        $product->delete();
    }

    public function deleteImage(Request $request, Product $product, ProductImage $productImage)
    {
        if ((int) $productImage->product_id !== (int) $product->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الصورة لا تتبع هذا المنتج.',
                ], 404);
            }

            abort(404);
        }

        $wasPrimary = (bool) $productImage->is_primary;
        $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $productImage->path);
        $productImage->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'was_primary' => $wasPrimary,
                'message' => $wasPrimary ? 'تم حذف الصورة الرئيسية.' : 'تم حذف صورة المعرض.',
            ]);
        }

        return back()->with('success', 'تم حذف الصورة.');
    }

    protected function setPrimaryImage(Product $product, UploadedFile $file): bool
    {
        $path = $this->storageHelper->storeUploadedFileWithFailover(
            $this->storageHelper->mediaDisk(),
            'products/'.$product->id,
            $file,
            'image'
        );

        if (! $path) {
            return false;
        }

        foreach ($product->images()->where('is_primary', true)->get() as $oldPrimary) {
            $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $oldPrimary->path);
            $oldPrimary->delete();
        }

        $product->images()->create([
            'path' => $path,
            'order' => 0,
            'is_primary' => true,
        ]);

        return true;
    }

    private function syncDigitalFiles(Product $product, Request $request): void
    {
        $rows = $request->input('digital_files', []);
        if (!is_array($rows)) {
            return;
        }

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = $row['id'] ?? null;
            $delete = filter_var($row['delete'] ?? false, FILTER_VALIDATE_BOOL);

            /** @var ProductFile|null $fileModel */
            $fileModel = null;
            if ($id) {
                $fileModel = $product->files()->whereKey($id)->first();
            }

            if ($delete) {
                if ($fileModel) {
                    if ($fileModel->path && Storage::disk('public')->exists($fileModel->path)) {
                        Storage::disk('public')->delete($fileModel->path);
                    }
                    $fileModel->delete();
                }
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));
            $order = isset($row['order']) ? (int) $row['order'] : 0;

            $payload = [
                'title' => $title !== '' ? $title : 'ملف قابل للتحميل',
                'order' => $order,
                'downloadable' => true,
            ];

            $uploaded = $request->file("digital_files.$index.file");
            if ($uploaded) {
                if ($fileModel && $fileModel->path && Storage::disk('public')->exists($fileModel->path)) {
                    Storage::disk('public')->delete($fileModel->path);
                }
                $payload['path'] = $uploaded->store("products/digital/{$product->id}", 'public');
            }

            if ($fileModel) {
                if (!$uploaded) {
                    unset($payload['path']);
                }
                $fileModel->update($payload);
                continue;
            }

            if (!$uploaded) {
                continue;
            }

            $product->files()->create($payload);
        }
    }
}
