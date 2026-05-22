<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Wishlist::with(['user', 'product'])->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $items = $query->paginate(25);

        // Top wishlisted products (for report section)
        $topWishlisted = Product::withCount('wishlists')
            ->having('wishlists_count', '>', 0)
            ->orderByDesc('wishlists_count')
            ->limit(10)
            ->get();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.pages.wishlists.index', compact('items', 'topWishlisted', 'users'));
    }

    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();
        return redirect()->route('admin.wishlists.index')->with('success', 'تم حذف العنصر من المفضلة.');
    }
}
