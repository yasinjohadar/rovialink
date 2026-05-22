<?php

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('activity_log')) {
    /**
     * Log an activity for audit trail.
     *
     * @param string $logType e.g. order.status_changed, user.updated
     * @param string $description Human-readable description
     * @param Model|null $subject Optional subject model
     * @param array<string, mixed> $properties Optional extra data
     * @return ActivityLog
     */
    function activity_log(string $logType, string $description, ?Model $subject = null, array $properties = []): ActivityLog
    {
        return app(ActivityLogger::class)->log($logType, $description, $subject, $properties);
    }
}
