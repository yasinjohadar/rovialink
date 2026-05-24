<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'order_status_id',
        'shipping_method_id',
        'subtotal',
        'shipping_amount',
        'tax_amount',
        'discount_amount',
        'total',
        'coupon_code',
        'points_redeemed',
        'points_discount_amount',
        'currency',
        'customer_note',
        'admin_note',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'points_redeemed' => 'integer',
        'points_discount_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber()
    {
        $prefix = 'ORD-';
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('created_at');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class)->orderByDesc('created_at');
    }

    public function loyaltyPointTransactions()
    {
        return $this->hasMany(LoyaltyPointTransaction::class)->orderByDesc('created_at');
    }

    public function getBillingAddressAttribute()
    {
        return $this->addresses->where('type', 'billing')->first();
    }

    /** بيانات التواصل / الفوترة (منتجات رقمية — يشمل عناوين قديمة من نوع shipping). */
    public function getContactAddressAttribute()
    {
        return $this->billing_address
            ?? $this->addresses->where('type', 'shipping')->first();
    }

    public function billingEmail(): ?string
    {
        $email = $this->contact_address?->address_line_2;

        if (! is_string($email) || $email === '') {
            return null;
        }

        $email = trim($email);

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    public function isOwnedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->user_id !== null && (int) $this->user_id === (int) $user->id) {
            return true;
        }

        $billingEmail = $this->billingEmail();

        return $billingEmail !== null
            && strcasecmp($billingEmail, trim($user->email)) === 0;
    }

    public function assignToUserIfUnclaimed(User $user): bool
    {
        if ($this->user_id !== null) {
            return false;
        }

        if (! $this->isOwnedBy($user)) {
            return false;
        }

        $this->forceFill(['user_id' => $user->id])->save();

        return true;
    }

    public function scopeForCustomer($query, User $user)
    {
        $email = strtolower(trim($user->email));

        return $query->where(function ($q) use ($user, $email) {
            $q->where('user_id', $user->id)
                ->orWhere(function ($q2) use ($email) {
                    $q2->whereNull('user_id')
                        ->whereHas('addresses', function ($addressQuery) use ($email) {
                            $addressQuery->where('type', 'billing')
                                ->whereRaw('LOWER(TRIM(address_line_2)) = ?', [$email]);
                        });
                });
        });
    }
}
