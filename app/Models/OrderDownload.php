<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_file_id',
        'download_token',
        'remaining_downloads',
        'expires_at',
        'downloaded_at',
    ];

    protected $casts = [
        'remaining_downloads' => 'integer',
        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function file()
    {
        return $this->belongsTo(ProductFile::class, 'product_file_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->greaterThan($this->expires_at);
    }

    public function canDownload(): bool
    {
        $statusSlug = $this->order?->status?->slug;
        // Project does not mark orders as paid yet; allow pending/processing/completed.
        if (!in_array($statusSlug, ['pending', 'processing', 'completed'], true)) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->remaining_downloads !== null && $this->remaining_downloads <= 0) {
            return false;
        }

        return true;
    }
}

