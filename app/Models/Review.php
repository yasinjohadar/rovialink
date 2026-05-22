<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'status',
        'is_verified_purchase',
        'helpful_count',
        'not_helpful_count',
        'images',
        'admin_response',
        'admin_response_at',
        'admin_response_by',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'images' => 'array',
        'is_featured' => 'boolean',
        'admin_response_at' => 'datetime',
    ];

    /**
     * Get the product being reviewed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who responded.
     */
    public function adminResponder()
    {
        return $this->belongsTo(User::class, 'admin_response_by');
    }

    /**
     * Get the interactions for this review.
     */
    public function interactions()
    {
        return $this->hasMany(ReviewInteraction::class);
    }

    /**
     * Get the replies for this review.
     */
    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected reviews.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include featured reviews.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include verified purchase reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Get formatted rating (stars).
     */
    public function getFormattedRatingAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'approved' => 'bg-success',
            'pending' => 'bg-warning',
            'rejected' => 'bg-danger',
            'spam' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status text in Arabic.
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'approved' => 'معتمد',
            'pending' => 'في الانتظار',
            'rejected' => 'مرفوض',
            'spam' => 'محتوى غير مرغوب',
            default => 'غير معروف',
        };
    }

    /**
     * Get helpful percentage.
     */
    public function getHelpfulPercentageAttribute()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total == 0) {
            return 0;
        }
        return round(($this->helpful_count / $total) * 100);
    }

    /**
     * Get image URLs.
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }
        
        return array_map(function($image) {
            return Storage::url($image);
        }, $this->images);
    }

    /**
     * Approve the review.
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject the review.
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as spam.
     */
    public function markAsSpam()
    {
        $this->update(['status' => 'spam']);
    }

    /**
     * Mark as helpful.
     */
    public function markAsHelpful($userId = null)
    {
        if ($userId) {
            // Check if user already interacted
            $existing = $this->interactions()
                ->where('user_id', $userId)
                ->where('type', 'helpful')
                ->first();
            
            if (!$existing) {
                $this->interactions()->create([
                    'user_id' => $userId,
                    'type' => 'helpful',
                ]);
                $this->increment('helpful_count');
            }
        } else {
            $this->increment('helpful_count');
        }
    }

    /**
     * Mark as not helpful.
     */
    public function markAsNotHelpful($userId = null)
    {
        if ($userId) {
            // Check if user already interacted
            $existing = $this->interactions()
                ->where('user_id', $userId)
                ->where('type', 'not_helpful')
                ->first();
            
            if (!$existing) {
                $this->interactions()->create([
                    'user_id' => $userId,
                    'type' => 'not_helpful',
                ]);
                $this->increment('not_helpful_count');
            }
        } else {
            $this->increment('not_helpful_count');
        }
    }
}
