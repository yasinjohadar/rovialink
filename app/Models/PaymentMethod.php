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
}
