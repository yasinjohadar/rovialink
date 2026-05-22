<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_class_id',
        'name',
        'rate',
        'country_code',
        'state',
        'city',
        'postal_code_pattern',
        'is_compound',
        'is_inclusive',
        'is_active',
        'order',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_compound' => 'boolean',
        'is_inclusive' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class, 'tax_class_id');
    }
}

