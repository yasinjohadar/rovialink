<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $customers = Customer::orderBy('created_at')->paginate(15);
        return view('store.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('store.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(13)],
            'gender' => ['required', 'in:male,female'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $customer = Customer::create($data);
        Auth::login($customer);

        return redirect()->route('store.customers.index')->with('success', 'تم إنشاء حساب العميل بنجاح.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders', 'shippingAddresses']);
        return view('store.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('store.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(13)],
            'gender' => ['required', 'in:male,female'],
        ]);

        $customer->update($data);

        return redirect()->route('store.customers.index')->with('success', 'تم تحديث بيانات العميل بنجاح.');
    }
}
