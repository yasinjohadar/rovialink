<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\ReviewReply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReviewRequest;
use App\Http\Requests\Admin\UpdateReviewRequest;
use App\Http\Requests\Admin\ReplyReviewRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:review-list')->only('index');
        $this->middleware('permission:review-create')->only(['create', 'store']);
        $this->middleware('permission:review-edit')->only(['edit', 'update']);
        $this->middleware('permission:review-delete')->only('destroy');
        $this->middleware('permission:review-show')->only('show');
        $this->middleware('permission:review-approve')->only('approve');
        $this->middleware('permission:review-reject')->only('reject');
        $this->middleware('permission:review-reply')->only('reply');
        $this->middleware('permission:review-statistics')->only('statistics');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reviewsQuery = Review::with(['user', 'product'])->latest();

        if ($request->filled('product_id')) {
            $reviewsQuery->where('product_id', $request->input('product_id'));
        }

        // البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $reviewsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('comment', 'like', "%$search%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $reviewsQuery->where('status', $request->input('status'));
        }

        // فلترة حسب التقييم
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->input('rating'));
        }

        // فلترة حسب الشراء الموثق
        if ($request->filled('is_verified_purchase')) {
            $reviewsQuery->where('is_verified_purchase', $request->input('is_verified_purchase'));
        }

        $reviews = $reviewsQuery->paginate(15);

        $products = Product::where('allow_reviews', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.pages.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $products = Product::where('allow_reviews', true)->orderBy('name')->get(['id', 'name', 'sku']);
        return view('admin.pages.reviews.create', compact('users', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // معالجة الصور
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('reviews/images', $imageName, 'public');
                    $images[] = $imagePath;
                }
            }
            $data['images'] = !empty($images) ? $images : null;

            Review::create($data);

            DB::commit();

            return redirect()->route('admin.reviews.index')
                ->with('success', 'تم إضافة الرأي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الرأي: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = Review::with(['user', 'product', 'adminResponder', 'interactions.user', 'replies.user'])
            ->findOrFail($id);

        return view('admin.pages.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $review = Review::findOrFail($id);
        $users = User::orderBy('name')->get();
        $products = Product::where('allow_reviews', true)->orWhere('id', $review->product_id)->orderBy('name')->get(['id', 'name', 'sku']);

        return view('admin.pages.reviews.edit', compact('review', 'users', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $review = Review::findOrFail($id);
            $data = $request->validated();

            // معالجة الصور الجديدة
            if ($request->hasFile('images')) {
                // حذف الصور القديمة
                if ($review->images && is_array($review->images)) {
                    foreach ($review->images as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }

                $images = [];
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('reviews/images', $imageName, 'public');
                    $images[] = $imagePath;
                }
                $data['images'] = $images;
            }

            $review->update($data);

            DB::commit();

            return redirect()->route('admin.reviews.index')
                ->with('success', 'تم تحديث الرأي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الرأي: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $review = Review::findOrFail($id);

            // حذف الصور
            if ($review->images && is_array($review->images)) {
                foreach ($review->images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $review->delete();

            DB::commit();

            return redirect()->route('admin.reviews.index')
                ->with('success', 'تم حذف الرأي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.reviews.index')
                ->with('error', 'حدث خطأ أثناء حذف الرأي: ' . $e->getMessage());
        }
    }

    /**
     * Approve a review.
     */
    public function approve(string $id)
    {
        $review = Review::findOrFail($id);
        $review->approve();

        return redirect()->back()
            ->with('success', 'تم اعتماد الرأي بنجاح');
    }

    /**
     * Reject a review.
     */
    public function reject(string $id)
    {
        $review = Review::findOrFail($id);
        $review->reject();

        return redirect()->back()
            ->with('success', 'تم رفض الرأي بنجاح');
    }

    /**
     * Mark review as spam.
     */
    public function markAsSpam(string $id)
    {
        $review = Review::findOrFail($id);
        $review->markAsSpam();

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الرأي كمحتوى غير مرغوب');
    }

    /**
     * Reply to a review.
     */
    public function reply(ReplyReviewRequest $request, string $id)
    {
        $review = Review::findOrFail($id);

        $review->update([
            'admin_response' => $request->reply_text,
            'admin_response_at' => now(),
            'admin_response_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'تم إضافة الرد بنجاح');
    }

    /**
     * Delete review image.
     */
    public function deleteImage(Request $request, $reviewId, $imageIndex)
    {
        $review = Review::findOrFail($reviewId);
        
        if ($review->images && is_array($review->images) && isset($review->images[$imageIndex])) {
            $imagePath = $review->images[$imageIndex];
            
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            $images = $review->images;
            unset($images[$imageIndex]);
            $images = array_values($images); // Re-index array
            
            $review->update(['images' => !empty($images) ? $images : null]);
        }

        return response()->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح']);
    }

    /**
     * Show statistics page.
     */
    public function statistics()
    {
        $totalReviews = Review::count();
        $approvedReviews = Review::approved()->count();
        $pendingReviews = Review::pending()->count();
        $rejectedReviews = Review::rejected()->count();
        $spamReviews = Review::where('status', 'spam')->count();
        
        $averageRating = Review::approved()->avg('rating');
        $verifiedReviews = Review::verified()->count();
        $featuredReviews = Review::featured()->count();

        // توزيع التقييمات
        $ratingDistribution = Review::approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        // أحدث الآراء
        $latestReviews = Review::with(['user'])
            ->approved()
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.pages.reviews.statistics', compact(
            'totalReviews',
            'approvedReviews',
            'pendingReviews',
            'rejectedReviews',
            'spamReviews',
            'averageRating',
            'verifiedReviews',
            'featuredReviews',
            'ratingDistribution',
            'latestReviews'
        ));
    }
}
