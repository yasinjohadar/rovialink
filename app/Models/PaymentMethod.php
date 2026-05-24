<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'driver', 'config', 'is_active', 'order'];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_method_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getDriverSlugs(): array
    {
        return ['cod', 'bank_transfer', 'paypal', 'card'];
    }

    public function resolvedGateway(): string
    {
        if ($this->driver === 'card') {
            return strtolower((string) ($this->config['gateway'] ?? 'stripe'));
        }

        return $this->driver;
    }

    public function isManual(): bool
    {
        return in_array($this->driver, ['cod', 'bank_transfer'], true);
    }

    public function displayLabel(): string
    {
        return match ($this->driver) {
            'cod' => 'الدفع عند الاستلام',
            'bank_transfer' => 'تحويل بنكي',
            'paypal' => 'PayPal',
            'card' => 'بطاقة ائتمان',
            default => $this->name,
        };
    }
}
