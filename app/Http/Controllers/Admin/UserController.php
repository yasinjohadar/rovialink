<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderItem;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     // يمكنه فقط رؤية قائمة المستخدمين (index)
    //     $this->middleware(['permission:user-list'])->only('index');

    //     // يمكنه فقط إنشاء مستخدم جديد (create + store)
    //     $this->middleware(['permission:user-create'])->only(['create', 'store']);

    //     // يمكنه فقط تعديل المستخدم (edit + update)
    //     $this->middleware(['permission:user-edit'])->only(['edit', 'update']);

    //     // يمكنه فقط حذف المستخدم (destroy)
    //     $this->middleware(['permission:user-delete'])->only('destroy');

    //     // يمكنه فقط رؤية ملف المستخدم (show)
    //     $this->middleware(['permission:user-show'])->only('show');
    // }

    public function __construct()
{
    // تأكد أن المستخدم مصادق أولًا ثم تحقق من الصلاحيات
    $this->middleware('auth');

    $this->middleware('permission:user-list')->only('index');
    $this->middleware('permission:user-create')->only(['create', 'store']);
    $this->middleware('permission:user-edit')->only(['edit', 'update']);
    $this->middleware('permission:user-delete')->only('destroy');
    $this->middleware('permission:user-show')->only('show');
}

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $sessions = $this->userSessionsGrouped();
        $users = $this->filteredUsersQuery($request)->paginate($this->resolvePerPage($request))->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.pages.users.partials.table', compact('users', 'sessions'))->render(),
            ]);
        }

        return view('admin.pages.users.index', compact('users', 'sessions'));
    }

    private function userSessionsGrouped()
    {
        return DB::table('sessions')
            ->orderByDesc('last_activity')
            ->get()
            ->groupBy('user_id');
    }

    private function filteredUsersQuery(Request $request)
    {
        $usersQuery = User::query()->with('roles');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $usersQuery->where('status', $request->input('status'));
        }

        if ($request->filled('is_active')) {
            $usersQuery->where('is_active', (bool) $request->input('is_active'));
        }

        return $usersQuery->latest('id');
    }

    private function resolvePerPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', 10);

        return in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view("admin.pages.users.create" ,compact("roles"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,banned',
            'is_active' => 'boolean',
            'roles' => 'array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'status.required' => 'حالة المستخدم مطلوبة',
            'photo.image' => 'يجب أن يكون الملف صورة',
            'photo.mimes' => 'نوع الصورة غير مدعوم',
            'photo.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ]);

        // معالجة الصورة
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('users/photos', $photoName, 'public');
        }

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
            'photo' => $photoPath,
            'created_by' => auth()->id(), // المستخدم الذي أنشأ هذا الحساب
        ]);

        // تعيين الأدوار
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        app(ActivityLogger::class)->userAction('created', $user);

        return redirect()->route("admin.users.index")->with("success" , "تم إضافة مستخدم جديد بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        $user->load([
            'orders' => function ($q) {
                $q->whereNull('deleted_at')
                    ->with(['status', 'items'])
                    ->latest();
            },
            'addresses',
            'loyaltyPointTransactions' => function ($q) {
                $q->with('order')->latest()->limit(30);
            },
        ]);

        $ordersQuery = $user->orders()->whereNull('deleted_at');

        $ordersCount = (clone $ordersQuery)->count();
        $totalSpent = (float) (clone $ordersQuery)->sum('total');
        $averageOrderValue = $ordersCount > 0 ? $totalSpent / $ordersCount : 0;
        $lastOrder = (clone $ordersQuery)->latest()->first();

        $orderIds = $ordersQuery->pluck('id');
        $topProduct = null;
        $topCategory = null;

        if ($orderIds->isNotEmpty()) {
            $topProduct = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->with('product')
                ->first();

            $topCategory = OrderItem::select('products.category_id', DB::raw('SUM(order_items.quantity) as total_qty'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereIn('order_items.order_id', $orderIds)
                ->groupBy('products.category_id')
                ->orderByDesc('total_qty')
                ->with(['product.category'])
                ->first();
        }

        $currencyService = app(CurrencyService::class);

        return view('admin.pages.users.profile', [
            'user' => $user,
            'orders' => $user->orders,
            'ordersCount' => $ordersCount,
            'totalSpent' => $totalSpent,
            'averageOrderValue' => $averageOrderValue,
            'lastOrder' => $lastOrder,
            'topProduct' => $topProduct,
            'topCategory' => $topCategory,
            'currencyService' => $currencyService,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view("admin.pages.users.edit" ,compact("roles" , "user"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,banned',
            'is_active' => 'boolean',
            'roles' => 'array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'status.required' => 'حالة المستخدم مطلوبة',
            'photo.image' => 'يجب أن يكون الملف صورة',
            'photo.mimes' => 'نوع الصورة غير مدعوم',
            'photo.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ]);

        // تجهيز البيانات للتحديث
        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ];

        // تحديث كلمة المرور فقط إذا تم إدخالها
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // معالجة الصورة
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->photo) {
                \Storage::disk('public')->delete($user->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('users/photos', $photoName, 'public');
            $updateData['photo'] = $photoPath;
        }

        // تحديث المستخدم
        $user->update($updateData);

        // تحديث الأدوار
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        $logger = app(ActivityLogger::class);
        $logger->userAction('updated', $user);
        if ($request->filled('password')) {
            $logger->userAction('password_changed', $user);
        }

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id);

        app(ActivityLogger::class)->userAction('deleted', $user);

        $user->delete();

        return redirect()->route("admin.users.index")->with("success" , "تم حذف مستخدم جديد بنجاح");

    }



    public function updatePassword(Request $request, User $user)
{
    $request->validate([
        'password' => 'required|string|min:8|confirmed',
    ], [
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
    ]);

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    app(ActivityLogger::class)->userAction('password_changed', $user);

    return redirect()->route('admin.users.index')->with('success', 'تم تحديث كلمة المرور بنجاح');
}

/**
 * تبديل حالة المستخدم (تفعيل/إلغاء تفعيل)
 */
public function toggleStatus(Request $request, $id)
{
    try {
        \Log::info('Toggle status request received', [
            'user_id' => $id,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_headers' => $request->headers->all(),
            'auth_user' => auth()->id()
        ]);

        $user = User::findOrFail($id);

        \Log::info('User found', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'current_is_active' => $user->is_active
        ]);

        // التحقق من أن المستخدم لا يحاول إلغاء تفعيل نفسه
        if ($user->id === auth()->id()) {
            \Log::warning('User tried to deactivate themselves', [
                'user_id' => $user->id
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك إلغاء تفعيل حسابك'
                ], 400);
            }
            return redirect()->back()->with('error', 'لا يمكنك إلغاء تفعيل حسابك');
        }

        // حفظ الحالة القديمة
        $oldStatus = $user->is_active;

        // تبديل الحالة
        $newStatus = !$user->is_active;

        // تحديث الحالة باستخدام update للتأكد من التحديث
        $user->update(['is_active' => $newStatus]);

        app(ActivityLogger::class)->userAction('status_toggled', $user->refresh(), [
            'old_is_active' => $oldStatus,
            'new_is_active' => $newStatus,
        ]);

        // إعادة تحميل المستخدم للتأكد من الحصول على القيمة المحدثة
        $user->refresh();

        \Log::info('User status updated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'old_status' => $oldStatus,
            'new_status' => $user->is_active,
            'toggled_by' => auth()->id()
        ]);

        $status = $user->is_active ? 'مفعل' : 'غير مفعل';

        $response = [
            'success' => true,
            'message' => "تم تحديث حالة المستخدم إلى: {$status}",
            'is_active' => (bool) $user->is_active
        ];

        \Log::info('Toggle status response', [
            'user_id' => $user->id,
            'response' => $response
        ]);

        if ($request->wantsJson()) {
            return response()->json($response);
        }
        return redirect()->back()->with('success', $response['message']);

    } catch (\Exception $e) {
        \Log::error('Error toggling user status', [
            'user_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'toggled_by' => auth()->id()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة المستخدم: ' . $e->getMessage()
            ], 500);
        }
        return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة المستخدم: ' . $e->getMessage());
    }
}


}
