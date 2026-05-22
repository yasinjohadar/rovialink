<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'rate_to_default',
        'is_default',
        'order',
        'is_active',
    ];

    protected $casts = [
        'rate_to_default' => 'decimal:6',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Currency $currency) {
            if ($currency->is_default) {
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('code');
    }
}
