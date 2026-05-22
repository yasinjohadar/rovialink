<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_enrollment_id',
        'certificate_template_id',
        'issued_at',
        'issued_by',
        'pdf_path',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
