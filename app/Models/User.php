<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
      use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'status',
        'is_active',
        'photo',
        'created_by',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'loyalty_points_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'loyalty_points_balance' => 'integer',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

     public function sessions()
    {
        return $this->hasMany(\App\Models\Session::class, 'user_id');
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the review interactions made by the user.
     */
    public function reviewInteractions()
    {
        return $this->hasMany(ReviewInteraction::class);
    }

    /**
     * Get the review replies written by the user.
     */
    public function reviewReplies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function notes()
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    public function loyaltyPointTransactions()
    {
        return $this->hasMany(LoyaltyPointTransaction::class)->orderByDesc('created_at');
    }
}