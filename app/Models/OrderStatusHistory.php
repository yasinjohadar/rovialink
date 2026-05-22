<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'old_status_id',
        'new_status_id',
        'changed_by',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'new_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

