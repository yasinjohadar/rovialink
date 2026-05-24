<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreChatMessage extends Model
{
    protected $fillable = [
        'store_chat_session_id',
        'role',
        'content',
        'tokens_used',
        'metadata',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(StoreChatSession::class, 'store_chat_session_id');
    }
}
