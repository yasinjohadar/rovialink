<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payments\CheckoutService;
use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\OrderPaymentSyncService;
use App\Services\Payments\PaymentOrchestrator;
use App\Services\Payments\PaymentSettingsService;
use App\Services\Seo\SeoBuilder;
use App\Services\Storage\StorageHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected PaymentOrchestrator $paymentOrchestrator,
        protected OrderPaymentSyncService $syncService,
        protected PayPalGateway $payPalGateway,
        protected PaymentSettingsService $paymentSettings,
        protected StorageHelperService $storageHelper,
    ) {}

    public function index()
    {
        $cart = $this->checkoutService->getCheckoutCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('frontend.cart.index')->withErrors(['cart' => 'السلة فارغة']);
        }

        $cartItems = app(\App\Services\CartService::class)->toViewItems($cart);
        $totals = $this->checkoutService->calculateTotals($cart);
        $cartTotal = $totals['subtotal'];
        $discount = $totals['discount'];
        $shippingCost = $totals['shipping'];
        $taxAmount = $totals['tax'];
        $paymentMethods = PaymentMethod::active()->orderBy('order')->get();

        $seo = SeoBuilder::forPage(
            'إتمام الدفع - ' . site_brand_name(),
            'أكمل طلبك بأمان واحصل على منتجاتك الرقمية فوراً.',
            route('frontend.checkout.index')
        );

        return view('frontend.pages.checkout.index', compact(
            'cartItems',
            'cartTotal',
            'discount',
            'shippingCost',
            'taxAmount',
            'paymentMethods',
            'seo'
        ));
    }

    public function store(Request $request)
    {
        $cart = $this->checkoutService->getCheckoutCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('frontend.cart.index')->withErrors(['cart' => 'السلة فارغة']);
        }

        $paymentMethod = PaymentMethod::active()->findOrFail($request->input('payment_method_id'));

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_reference' => 'nullable|string|max:100',
            'country' => 'nullable|string|size:2',
            'payment_receipt' => [
                Rule::requiredIf($paymentMethod->driver === 'bank_transfer'),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],
        ], [
            'payment_receipt.required' => 'يرجى إرفاق إيصال التحويل البنكي.',
            'payment_receipt.mimes' => 'إيصال التحويل يجب أن يكون صورة أو PDF.',
            'payment_receipt.max' => 'حجم إيصال التحويل يجب ألا يتجاوز 5 م.ب.',
        ]);

        if ($request->hasFile('payment_receipt')) {
            $file = $request->file('payment_receipt');
            $fileType = str_starts_with((string) $file->getMimeType(), 'image/') ? 'image' : 'document';
            $receiptPath = $this->storageHelper->storeUploadedFileWithFailover(
                $this->storageHelper->mediaDisk(),
                'payment-receipts',
                $file,
                $fileType
            );

            if (! $receiptPath) {
                return back()->withInput()->withErrors([
                    'payment_receipt' => 'فشل رفع إيصال التحويل. يرجى المحاولة مرة أخرى.',
                ]);
            }

            $validated['payment_receipt_path'] = $receiptPath;
            $validated['payment_receipt_original_name'] = $file->getClientOriginalName();
        }

        try {
            $order = $this->checkoutService->createOrderFromCart($validated, $paymentMethod);
            $result = $this->paymentOrchestrator->initiate($order, [
                'email' => $validated['email'],
                'bank_reference' => $validated['bank_reference'] ?? null,
                'payment_receipt_path' => $validated['payment_receipt_path'] ?? null,
                'payment_receipt_original_name' => $validated['payment_receipt_original_name'] ?? null,
            ]);

            if ($result->requiresRedirect()) {
                return redirect()->away($result->redirectUrl);
            }

            if ($result->requiresView()) {
                return view($result->view, $result->viewData);
            }

            return redirect()->route('frontend.checkout.success', $order);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.']);
        }
    }

    public function success(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $payment = $order->payments()->latest()->first();
        if ($payment && $payment->status === 'pending' && $request->filled('session_id')) {
            $this->verifyStripeSession($payment, $request->string('session_id')->toString());
        }

        $order->load(['items.product', 'items.downloads.file', 'status', 'payments.paymentMethod']);

        return view('frontend.pages.checkout.success', compact('order', 'payment'));
    }

    public function pending(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['items.product.primaryImage']);
        $payment = $order->payments()->latest()->with('paymentMethod')->first();

        return view('frontend.pages.checkout.pending', compact('order', 'payment'));
    }

    public function cancel(Order $order)
    {
        $this->authorizeOrder($order);
        $payment = $order->payments()->latest()->first();
        if ($payment && $payment->status === 'pending') {
            $this->syncService->markCancelled($payment, 'ألغى العميل عملية الدفع');
        }

        return view('frontend.pages.checkout.cancel', compact('order', 'payment'));
    }

    public function paypalReturn(Request $request, Order $order)
    {
        $this->authorizeOrder($order);
        $payment = $order->payments()->latest()->with('paymentMethod')->first();

        if (! $payment || ! $request->filled('token')) {
            return redirect()->route('frontend.checkout.cancel', $order);
        }

        try {
            $capture = $this->payPalGateway->captureOrder($payment, $request->string('token')->toString());
            $captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $this->syncService->markCompleted($payment, $captureId, [
                'paypal_capture_id' => $captureId,
                'paypal_order_id' => $request->string('token')->toString(),
            ]);

            return redirect()->route('frontend.checkout.success', $order);
        } catch (\Throwable $e) {
            report($e);
            $this->syncService->markFailed($payment, $e->getMessage());

            return redirect()->route('frontend.checkout.cancel', $order);
        }
    }

    public function retry(Order $order)
    {
        $this->authorizeOrder($order);
        $payment = $order->payments()->latest()->with('paymentMethod')->first();

        if (! $payment || $payment->status !== 'pending') {
            return redirect()->route('frontend.account.orders.show', $order);
        }

        try {
            $result = $this->paymentOrchestrator->initiate($order->fresh(['items', 'addresses']), [
                'email' => $order->billing_address?->address_line_2,
            ]);

            if ($result->requiresRedirect()) {
                return redirect()->away($result->redirectUrl);
            }

            if ($result->requiresView()) {
                return view($result->view, $result->viewData);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->withErrors(['error' => 'تعذر إعادة محاولة الدفع.']);
    }

    protected function authorizeOrder(Order $order): void
    {
        $user = Auth::user();
        abort_unless($user && $order->isOwnedBy($user), 403);
        $order->assignToUserIfUnclaimed($user);
    }

    protected function verifyStripeSession(Payment $payment, string $sessionId): void
    {
        $method = $payment->paymentMethod;
        $secret = $method?->config['secret_key'] ?? $this->paymentSettings->stripeSecret();
        if (! $secret) {
            return;
        }

        Stripe::setApiKey($secret);
        $session = Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $this->syncService->markCompleted($payment, $session->payment_intent, [
                'stripe_session_id' => $session->id,
                'stripe_payment_intent' => $session->payment_intent,
            ]);
        }
    }
}
