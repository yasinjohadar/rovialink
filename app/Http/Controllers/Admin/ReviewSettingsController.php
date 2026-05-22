<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class ReviewSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $value = SystemSetting::getValue('reviews_require_approval', '1');
        $reviewsRequireApproval = $value === '1';

        return view('admin.pages.reviews.settings', compact('reviewsRequireApproval'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'reviews_require_approval' => ['required', 'in:0,1'],
        ]);

        $value = $request->input('reviews_require_approval');
        SystemSetting::set('reviews_require_approval', $value, 'string', 'reviews');

        return redirect()->route('admin.review-settings.index')
            ->with('success', 'تم حفظ إعدادات التقييمات بنجاح.');
    }
}
