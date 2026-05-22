<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaxClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($class) {
            if (empty($class->slug)) {
                $class->slug = Str::slug($class->name);
            }
        });
    }

    public function rates()
    {
        return $this->hasMany(TaxRate::class);
    }
}

