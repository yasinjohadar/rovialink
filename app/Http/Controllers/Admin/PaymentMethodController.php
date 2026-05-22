<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentMethodRequest;
use App\Http\Requests\Admin\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function drivers(): array
    {
        return [
            'cod' => ['label' => 'الدفع عند الاستلام', 'config_keys' => ['instructions']],
            'bank_transfer' => ['label' => 'التحويل البنكي / الآيبان', 'config_keys' => ['bank_name', 'iban', 'account_name', 'instructions']],
            'paypal' => ['label' => 'باي بال', 'config_keys' => ['client_id', 'client_secret', 'sandbox']],
            'card' => ['label' => 'فيزا / ماستركارد (بوابة دفع)', 'config_keys' => ['gateway', 'public_key', 'secret_key', 'sandbox']],
        ];
    }

    public function index()
    {
        $methods = PaymentMethod::withCount('payments')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.pages.payment-methods.index', compact('methods'));
    }

    public function create()
    {
        $drivers = static::drivers();
        return view('admin.pages.payment-methods.create', compact('drivers'));
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);
        $slug = PaymentMethod::where('slug', $slug)->exists()
            ? $slug . '-' . uniqid()
            : $slug;

        $config = $this->buildConfigFromRequest($request->input('driver'), $request);
        $method = PaymentMethod::create([
            'name' => $data['name'],
            'slug' => $slug,
            'driver' => $data['driver'],
            'config' => $config,
            'is_active' => $request->boolean('is_active', true),
            'order' => (int) ($data['order'] ?? 0),
        ]);

        return redirect()->route('admin.payment-methods.index')->with('success', 'تم إضافة وسيلة الدفع بنجاح.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $drivers = static::drivers();
        return view('admin.pages.payment-methods.edit', compact('paymentMethod', 'drivers'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validated();
        $config = $this->buildConfigFromRequest($paymentMethod->driver, $request);

        // عدم الكتابة فوق الأسرار إذا تُركت فارغة في التعديل
        if ($paymentMethod->driver === 'paypal') {
            $existing = $paymentMethod->config ?? [];
            if (empty($config['client_secret']) && !empty($existing['client_secret'])) {
                $config['client_secret'] = $existing['client_secret'];
            }
        }
        if ($paymentMethod->driver === 'card') {
            $existing = $paymentMethod->config ?? [];
            if (empty($config['secret_key']) && !empty($existing['secret_key'])) {
                $config['secret_key'] = $existing['secret_key'];
            }
        }

        $paymentMethod->update([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $paymentMethod->slug,
            'driver' => $data['driver'],
            'config' => $config,
            'is_active' => $request->boolean('is_active', true),
            'order' => (int) ($data['order'] ?? $paymentMethod->order),
        ]);

        return redirect()->route('admin.payment-methods.index')->with('success', 'تم تحديث وسيلة الدفع بنجاح.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->payments()->exists()) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'لا يمكن حذف وسيلة الدفع لوجود مدفوعات مرتبطة بها.');
        }
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'تم حذف وسيلة الدفع.');
    }

    private function buildConfigFromRequest(string $driver, Request $request): array
    {
        $drivers = static::drivers();
        $keys = $drivers[$driver]['config_keys'] ?? [];
        $config = [];
        foreach ($keys as $key) {
            $value = $request->input("config.{$key}");
            if ($key === 'sandbox') {
                $config[$key] = filter_var($value, FILTER_VALIDATE_BOOL);
            } else {
                $config[$key] = $value !== null ? (string) $value : null;
            }
        }
        return $config;
    }
}
