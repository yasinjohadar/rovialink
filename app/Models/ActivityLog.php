<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'activity_log';

    protected $fillable = [
        'user_id',
        'log_type',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'subject_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('log_type', $type);
    }

    public function scopeForSubject(Builder $query, string $type, ?int $id = null): Builder
    {
        $query->where('subject_type', $type);
        if ($id !== null) {
            $query->where('subject_id', $id);
        }
        return $query;
    }

    public function scopeByUser(Builder $query, ?int $userId): Builder
    {
        if ($userId === null) {
            return $query->whereNull('user_id');
        }
        return $query->where('user_id', $userId);
    }

    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Arabic labels for known log types (for display in admin).
     *
     * @return array<string, string>
     */
    public static function logTypeLabels(): array
    {
        return [
            'order.status_changed' => 'تغيير حالة الطلب',
            'site_settings.updated' => 'تحديث إعدادات الموقع',
            'user.created' => 'إنشاء مستخدم',
            'user.updated' => 'تعديل مستخدم',
            'user.deleted' => 'حذف مستخدم',
            'user.status_toggled' => 'تفعيل/إلغاء مستخدم',
            'user.password_changed' => 'تغيير كلمة مرور',
            'role.created' => 'إنشاء صلاحية',
            'role.updated' => 'تعديل صلاحية',
            'role.deleted' => 'حذف صلاحية',
        ];
    }

    public function getLogTypeLabelAttribute(): string
    {
        return self::logTypeLabels()[$this->log_type] ?? $this->log_type;
    }
}
