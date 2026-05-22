<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'path',
        'downloadable',
        'order',
    ];

    protected $casts = [
        'downloadable' => 'boolean',
        'order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute()
    {
        return $this->path ? Storage::url($this->path) : null;
    }
}

