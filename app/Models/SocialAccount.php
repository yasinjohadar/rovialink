<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    public const PROVIDER_GOOGLE = 'google';

    public const PROVIDER_FACEBOOK = 'facebook';

    /** @var list<string> */
    public const PROVIDERS = [
        self::PROVIDER_GOOGLE,
        self::PROVIDER_FACEBOOK,
    ];

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'avatar',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
