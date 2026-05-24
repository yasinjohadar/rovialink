<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StoreChatSession extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'ip_hash',
        'user_agent',
        'message_count_today',
        'message_count_date',
        'last_activity_at',
    ];

    protected $casts = [
        'message_count_today' => 'integer',
        'message_count_date' => 'date',
        'last_activity_at' => 'datetime',
    ];

    public static function createGuest(?int $userId = null, ?string $ipHash = null, ?string $userAgent = null): self
    {
        return static::create([
            'token' => (string) Str::uuid(),
            'user_id' => $userId,
            'ip_hash' => $ipHash,
            'user_agent' => $userAgent ? Str::limit($userAgent, 500) : null,
            'message_count_today' => 0,
            'message_count_date' => now()->toDateString(),
            'last_activity_at' => now(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(StoreChatMessage::class)->orderBy('created_at');
    }

    public function touchActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function incrementDailyMessageCount(): void
    {
        $today = now()->toDateString();

        if ($this->message_count_date?->toDateString() !== $today) {
            $this->message_count_today = 0;
            $this->message_count_date = $today;
        }

        $this->message_count_today++;
        $this->last_activity_at = now();
        $this->save();
    }

    public function dailyMessageCount(): int
    {
        $today = now()->toDateString();
        if ($this->message_count_date?->toDateString() !== $today) {
            return 0;
        }

        return (int) $this->message_count_today;
    }
}
