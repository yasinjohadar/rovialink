<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()->root()->ordered()->withCount(['products' => function ($q) {
            $q->where('status', 'active')->where('is_visible', true);
        }])->get();

        $featuredProducts = Product::active()->featured()->with(['category', 'images', 'reviews'])->inRandomOrder()->limit(8)->get();

        $newArrivals = Product::active()->latest()->with(['category', 'images', 'reviews'])->limit(8)->get();

        $bestSellers = Product::active()->with(['category', 'images', 'reviews'])->orderByDesc('order')->orderByDesc('created_at')->limit(8)->get();

        $topRated = Product::active()->with(['category', 'images', 'reviews'])->has('reviews')->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating')->limit(8)->get();

        $onSale = Product::active()->whereNotNull('compare_at_price')->whereColumn('compare_at_price', '>', 'price')->with(['category', 'images', 'reviews'])->inRandomOrder()->limit(8)->get();

        // Collections for different homepage sections
        $fashionProducts = Product::active()->with(['category', 'images', 'reviews'])->inRandomOrder()->limit(8)->get();
        $electronicsProducts = Product::active()->with(['category', 'images', 'reviews'])->inRandomOrder()->limit(8)->get();
        $homeProducts = Product::active()->with(['category', 'images', 'reviews'])->inRandomOrder()->limit(8)->get();
        $gadgetsProducts = Product::active()->with(['category', 'images', 'reviews'])->inRandomOrder()->limit(8)->get();

        $blogPosts = BlogPost::published()->with(['author', 'category'])->latest('published_at')->limit(4)->get();

        $homepageBrands = Brand::query()
            ->where('show_on_homepage', true)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('frontend.pages.index', compact(
            'categories',
            'homepageBrands',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'topRated',
            'onSale',
            'fashionProducts',
            'electronicsProducts',
            'homeProducts',
            'gadgetsProducts',
            'blogPosts'
        ));
    }
}
