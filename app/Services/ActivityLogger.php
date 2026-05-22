<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $logType Category/type (e.g. order.status_changed, user.updated)
     * @param string $description Human-readable description (Arabic or English)
     * @param Model|null $subject Optional model (Order, User, etc.)
     * @param array<string, mixed> $properties Optional extra data (old/new values, etc.)
     */
    public function log(
        string $logType,
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): ActivityLog {
        $userId = Auth::id();
        $subjectType = null;
        $subjectId = null;
        if ($subject !== null) {
            $subjectType = $subject->getMorphClass();
            $subjectId = $subject->getKey();
        }

        return ActivityLog::create([
            'user_id' => $userId,
            'log_type' => $logType,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Convenience: log order status change.
     */
    public function orderStatusChanged($order, string $oldStatus, string $newStatus): ActivityLog
    {
        return $this->log(
            'order.status_changed',
            "تغيير حالة الطلب #{$order->order_number} من «{$oldStatus}» إلى «{$newStatus}»",
            $order,
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );
    }

    /**
     * Convenience: log site settings update.
     */
    public function siteSettingsUpdated(array $changedKeys = []): ActivityLog
    {
        return $this->log(
            'site_settings.updated',
            'تحديث إعدادات الموقع العامة',
            null,
            ['keys' => $changedKeys]
        );
    }

    /**
     * Convenience: log user created/updated/deleted or status toggled.
     */
    public function userAction(string $action, $user, array $properties = []): ActivityLog
    {
        $descriptions = [
            'created' => "إنشاء مستخدم جديد: {$user->name} (#{$user->id})",
            'updated' => "تعديل بيانات المستخدم: {$user->name} (#{$user->id})",
            'deleted' => "حذف المستخدم: {$user->name} (#{$user->id})",
            'status_toggled' => "تفعيل/إلغاء تفعيل المستخدم: {$user->name} (#{$user->id})",
            'password_changed' => "تغيير كلمة مرور المستخدم: {$user->name} (#{$user->id})",
        ];
        $description = $descriptions[$action] ?? "إجراء على المستخدم: {$user->name} (#{$user->id})";
        return $this->log("user.{$action}", $description, $user, $properties);
    }

    /**
     * Convenience: log role created/updated/deleted.
     */
    public function roleAction(string $action, $role, array $properties = []): ActivityLog
    {
        $name = $role->name ?? '#' . $role->id;
        $descriptions = [
            'created' => "إنشاء صلاحية جديدة: {$name}",
            'updated' => "تعديل الصلاحية: {$name}",
            'deleted' => "حذف الصلاحية: {$name}",
        ];
        $description = $descriptions[$action] ?? "إجراء على الصلاحية: {$name}";
        return $this->log("role.{$action}", $description, $role, $properties);
    }
}
