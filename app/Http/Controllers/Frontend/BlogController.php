<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogComment;
use App\Services\Seo\SeoBuilder;

class BlogController extends Controller
{
    public function index(Request $request, ?BlogCategory $activeCategory = null, ?BlogTag $activeTag = null)
    {
        if ($request->filled('category') && !$activeCategory) {
            $activeCategory = BlogCategory::where('slug', $request->category)->where('is_active', true)->first();
        }

        if ($request->filled('tag') && !$activeTag) {
            $activeTag = BlogTag::where('slug', $request->tag)->first();
        }

        $query = BlogPost::with(['author', 'category'])->published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        if ($activeCategory) {
            $query->where('blog_category_id', $activeCategory->id);
        } elseif ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($activeTag) {
            $query->whereHas('tags', function ($q) use ($activeTag) {
                $q->where('blog_tags.id', $activeTag->id);
            });
        } elseif ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $posts = $query->latest('published_at')->paginate(6)->withQueryString();
        $categories = BlogCategory::withCount(['posts' => function ($q) {
            $q->published();
        }])->where('is_active', true)->orderBy('order')->get();
        $tags = BlogTag::popular()->limit(20)->get();
        $recentPosts = BlogPost::published()->latest('published_at')->limit(5)->get();

        $seo = SeoBuilder::forBlogIndex($request, $posts, $activeCategory, $activeTag);

        $heroTitle = 'المدونة التعليمية';
        $heroSubtitle = config('seo.blog.index_description');
        $breadcrumbCurrent = 'المدونة';

        if ($activeCategory) {
            $heroTitle = 'مدونة ' . $activeCategory->name;
            $heroSubtitle = $activeCategory->description ?? $heroSubtitle;
            $breadcrumbCurrent = $activeCategory->name;
        } elseif ($activeTag) {
            $heroTitle = 'وسم: ' . $activeTag->name;
            $breadcrumbCurrent = $activeTag->name;
        } elseif ($request->filled('search')) {
            $heroTitle = 'نتائج البحث';
            $breadcrumbCurrent = 'بحث: ' . $request->search;
        }

        return view('frontend.pages.blog.index', compact(
            'posts',
            'categories',
            'tags',
            'recentPosts',
            'activeCategory',
            'activeTag',
            'seo',
            'heroTitle',
            'heroSubtitle',
            'breadcrumbCurrent'
        ));
    }

    public function show($slug)
    {
        $post = BlogPost::with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->incrementViews();

        $comments = BlogComment::where('blog_post_id', $post->id)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->get();

        $categories = BlogCategory::withCount(['posts' => function ($q) {
            $q->published();
        }])->where('is_active', true)->orderBy('order')->get();
        $tags = BlogTag::popular()->limit(20)->get();
        $recentPosts = BlogPost::published()->where('id', '!=', $post->id)->latest('published_at')->limit(5)->get();
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $seo = SeoBuilder::forPost($post);

        return view('frontend.pages.blog.show', compact(
            'post',
            'comments',
            'categories',
            'tags',
            'recentPosts',
            'relatedPosts',
            'seo'
        ));
    }

    public function storeComment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $post = BlogPost::where('slug', $slug)->published()->firstOrFail();

        BlogComment::create([
            'blog_post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_approved' => false,
        ]);

        return back()->with('success', 'تم إرسال تعليقك وسيتم مراجعته قبل النشر.');
    }

    public function category($slug)
    {
        $activeCategory = BlogCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $request = request()->merge(['category' => $slug]);

        return $this->index($request, $activeCategory);
    }

    public function tag($slug)
    {
        $activeTag = BlogTag::where('slug', $slug)->firstOrFail();

        $request = request()->merge(['tag' => $slug]);

        return $this->index($request, null, $activeTag);
    }
}
