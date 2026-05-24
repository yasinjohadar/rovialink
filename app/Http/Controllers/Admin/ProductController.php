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
            'action' => ['required', 'in:activate,draft,hide'],
        ]);

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

        // حفظ الصورة الرئيسية إن وُجدت
        $orderCounter = 0;
        if ($request->hasFile('primary_image')) {
            $primaryPath = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'products/' . $product->id,
                $request->file('primary_image'),
                'image'
            );
            if (! $primaryPath) {
                return redirect()->back()->withInput()->with('error', 'فشل رفع الصورة الرئيسية.');
            }
            $product->images()->create([
                'path' => $primaryPath,
                'order' => $orderCounter,
                'is_primary' => true,
            ]);
            $orderCounter++;
        }

        // حفظ صور المعرض
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
                    'order' => $orderCounter + $index,
                    // إذا لم تُرفع صورة رئيسية سيتم اعتبار أول صورة من المعرض كصورة رئيسية
                    'is_primary' => !$request->hasFile('primary_image') && $index === 0,
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
        $product->load(['images', 'attributes', 'variants.attributeValues', 'files']);
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

        // تحميل صورة رئيسية جديدة إن وُجدت
        $maxOrder = $product->images()->max('order') ?? -1;
        if ($request->hasFile('primary_image')) {
            $primaryPath = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'products/' . $product->id,
                $request->file('primary_image'),
                'image'
            );
            if (! $primaryPath) {
                return redirect()->back()->withInput()->with('error', 'فشل رفع الصورة الرئيسية.');
            }
            // إلغاء تعيين أي صورة رئيسية سابقة
            $product->images()->update(['is_primary' => false]);
            $product->images()->create([
                'path' => $primaryPath,
                'order' => $maxOrder + 1,
                'is_primary' => true,
            ]);
            $maxOrder++;
        }

        // إضافة صور جديدة للمعرض
        if ($request->hasFile('images')) {
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
        foreach ($product->images as $img) {
            $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $img->path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج.');
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }
        $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $image->path);
        $image->delete();
        return back()->with('success', 'تم حذف الصورة.');
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
