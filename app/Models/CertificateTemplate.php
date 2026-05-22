<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    public const TYPE_HTML = 'html';
    public const TYPE_WITH_BACKGROUND = 'with_background';

    protected $fillable = [
        'name',
        'type',
        'body_html',
        'background_path',
        'course_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function scopeForCourse($query, ?int $courseId)
    {
        if ($courseId === null) {
            return $query->whereNull('course_id');
        }
        return $query->where(function ($q) use ($courseId) {
            $q->whereNull('course_id')->orWhere('course_id', $courseId);
        });
    }

    public function scopeDefault($query, ?int $courseId = null)
    {
        $query->where('is_default', true);
        if ($courseId !== null) {
            $query->where(function ($q) use ($courseId) {
                $q->where('course_id', $courseId)->orWhereNull('course_id');
            });
        }
        return $query->orderByRaw('course_id IS NOT NULL DESC'); // course-specific first
    }
}
