<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentSettingsService;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public function __construct(
        protected PaymentSettingsService $paymentSettings
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->paymentSettings->initializeDefaults();
        $settings = $this->paymentSettings->getSettingsForForm();

        $webhookUrls = [
            'stripe' => route('webhooks.stripe'),
            'paypal' => route('webhooks.paypal'),
        ];

        return view('admin.pages.payments.settings', compact('settings', 'webhookUrls'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'stripe_publishable_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_client_secret' => 'nullable|string|max:255',
            'paypal_webhook_id' => 'nullable|string|max:255',
            'paypal_mode' => 'required|in:sandbox,live',
        ], [
            'paypal_mode.in' => 'وضع PayPal غير صالح.',
        ]);

        $this->paymentSettings->updateSettings($validated);

        return redirect()
            ->route('admin.payments.settings.index')
            ->with('success', 'تم حفظ إعدادات الدفع بنجاح.');
    }
}
