<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? now()->diffInYears($this->date_of_birth)->y : null;
    }
}
