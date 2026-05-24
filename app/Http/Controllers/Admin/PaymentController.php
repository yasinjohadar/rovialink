<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\PaymentOrchestrator;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentOrchestrator $orchestrator
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Payment::with(['order.user', 'paymentMethod'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('transaction_id', 'like', "%{$q}%")
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$q}%"));
            });
        }

        $payments = $query->paginate(20)->withQueryString();
        $methods = PaymentMethod::orderBy('order')->get();
        $webhookUrl = route('webhooks.stripe');

        return view('admin.pages.payments.index', compact('payments', 'methods', 'webhookUrl'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.items', 'paymentMethod', 'refunds.processor']);

        return view('admin.pages.payments.show', compact('payment'));
    }

    public function confirm(Payment $payment)
    {
        abort_unless($payment->paymentMethod?->isManual(), 403);
        abort_unless($payment->status === 'pending', 422);

        $this->orchestrator->confirmManual($payment);

        return back()->with('success', 'تم تأكيد الدفع.');
    }

    public function reject(Request $request, Payment $payment)
    {
        abort_unless($payment->paymentMethod?->isManual(), 403);

        $this->orchestrator->rejectManual($payment, $request->input('reason'));

        return back()->with('success', 'تم رفض الدفع.');
    }

    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        abort_unless($payment->status === 'completed', 422);

        try {
            $this->orchestrator->refund(
                $payment,
                $request->filled('amount') ? (float) $request->amount : null,
                $request->input('reason')
            );
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم تنفيذ الاسترداد.');
    }

    public function webhooks()
    {
        $events = PaymentWebhookEvent::latest()->paginate(30);

        return view('admin.pages.payments.webhooks', compact('events'));
    }
}
