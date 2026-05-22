<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderStatus extends Model
{
    use HasFactory;

    public const ROLE_CHECKOUT = 'checkout';

    public const ROLE_RETURN_REFUND = 'return_refund';

    protected $fillable = ['name', 'slug', 'color', 'order', 'is_final', 'system_role'];

    public static function idForRole(string $role): ?int
    {
        return static::where('system_role', $role)->value('id');
    }

    public static function clearRoleExcept(string $role, int $keepId): void
    {
        static::where('system_role', $role)->where('id', '!=', $keepId)->update(['system_role' => null]);
    }

    protected $casts = [
        'order' => 'integer',
        'is_final' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }

    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public static function uniqueSlugFromName(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        if ($slug === '') {
            $slug = 'status';
        }

        $base = $slug;
        $counter = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

