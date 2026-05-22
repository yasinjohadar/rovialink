<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'type',
    ];

    /**
     * Get the review that owns the interaction.
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the user who made the interaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
