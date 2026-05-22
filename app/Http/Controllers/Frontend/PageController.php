<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function contact(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
            ]);

            try {
                Mail::raw($request->message, function ($message) use ($request) {
                    $message->to(setting('email', 'info@example.com'))
                        ->subject($request->subject)
                        ->from($request->email, $request->name);
                });

                return back()->with('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.');
            } catch (\Exception $e) {
                return back()->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
            }
        }

        return view('frontend.pages.contact');
    }

    public function faq()
    {
        return view('frontend.pages.faq');
    }

    public function privacy()
    {
        return view('frontend.pages.privacy');
    }

    public function terms()
    {
        return view('frontend.pages.terms');
    }
}
