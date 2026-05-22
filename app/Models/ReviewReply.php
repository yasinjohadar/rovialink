<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'reply_text',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the review that owns the reply.
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the user who wrote the reply.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include approved replies.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending replies.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
