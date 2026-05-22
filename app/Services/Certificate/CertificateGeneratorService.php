<?php

namespace App\Services\Certificate;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\CourseEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CertificateGeneratorService
{
    public const PLACEHOLDERS = [
        'student_name' => 'اسم الطالب',
        'course_name' => 'اسم الكورس',
        'completion_date' => 'تاريخ الإكمال',
        'duration' => 'مدة الكورس',
        'certificate_code' => 'كود الشهادة',
    ];

    /**
     * Resolve template: use given one or default for course or global.
     */
    public function resolveTemplate(CourseEnrollment $enrollment, ?CertificateTemplate $template = null): ?CertificateTemplate
    {
        if ($template !== null) {
            return $template;
        }
        $courseId = $enrollment->course_id;
        return CertificateTemplate::default($courseId)->first();
    }

    /**
     * Build placeholder values from enrollment.
     */
    public function buildPlaceholders(CourseEnrollment $enrollment, ?string $certificateCode = null): array
    {
        $completedAt = $enrollment->completed_at ?? $enrollment->updated_at ?? now();
        $duration = $enrollment->course->duration_minutes ?? 0;
        $durationText = $duration > 0
            ? (intdiv($duration, 60) > 0 ? intdiv($duration, 60) . ' ساعة ' : '') . ($duration % 60 > 0 ? ($duration % 60) . ' دقيقة' : '')
            : '—';

        return [
            '{{student_name}}' => $enrollment->user->name ?? '—',
            '{{course_name}}' => $enrollment->course->title ?? '—',
            '{{completion_date}}' => $completedAt ? $completedAt->format('Y-m-d') : '—',
            '{{duration}}' => $durationText,
            '{{certificate_code}}' => $certificateCode ?? $this->generateCertificateCode($enrollment),
        ];
    }

    public function generateCertificateCode(CourseEnrollment $enrollment): string
    {
        return strtoupper(substr(md5($enrollment->id . '-' . $enrollment->course_id . '-' . $enrollment->user_id), 0, 12));
    }

    /**
     * Fill template body with placeholders.
     */
    public function fillBody(CertificateTemplate $template, array $placeholders): string
    {
        $body = $template->body_html;
        foreach ($placeholders as $key => $value) {
            $body = str_replace($key, (string) $value, $body);
        }
        return $body;
    }

    /**
     * Build full HTML for PDF (with optional background).
     */
    public function buildHtml(CertificateTemplate $template, string $filledBody): string
    {
        $bgStyle = '';
        if ($template->type === CertificateTemplate::TYPE_WITH_BACKGROUND && $template->background_path) {
            $fullPath = Storage::disk('public')->path($template->background_path);
            if (file_exists($fullPath)) {
                $fullPath = realpath($fullPath);
                $url = 'file:///' . str_replace('\\', '/', $fullPath);
                $bgStyle = 'background-image: url(\'' . $url . '\'); background-size: cover; background-position: center;';
            }
        }

        $pageStyle = 'width: 210mm; min-height: 297mm; margin: 0; padding: 15mm; box-sizing: border-box; direction: rtl; font-family: DejaVu Sans, sans-serif;';
        if ($bgStyle) {
            $pageStyle .= ' ' . $bgStyle;
        }

        return '<!DOCTYPE html><html dir="rtl"><head><meta charset="UTF-8"></head><body style="' . $pageStyle . '">' . $filledBody . '</body></html>';
    }

    /**
     * Generate PDF for the enrollment. Returns PDF as download response or saves and returns path.
     *
     * @param bool $saveIfPath if true and certificate record exists, save PDF to storage and set pdf_path
     * @return Response|string PDF response for download, or stored path
     */
    public function generate(CourseEnrollment $enrollment, ?CertificateTemplate $template = null, bool $saveToStorage = false, ?Certificate $certificateRecord = null)
    {
        $template = $this->resolveTemplate($enrollment, $template);
        if (!$template) {
            throw new \RuntimeException('لا يوجد قالب شهادة متاح. يرجى إنشاء قالب افتراضي أو اختيار قالب.');
        }

        $enrollment->load(['user', 'course']);
        $code = $certificateRecord ? ($certificateRecord->id . '-' . $this->generateCertificateCode($enrollment)) : $this->generateCertificateCode($enrollment);
        $placeholders = $this->buildPlaceholders($enrollment, $code);
        $filledBody = $this->fillBody($template, $placeholders);
        $html = $this->buildHtml($template, $filledBody);

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        if ($saveToStorage && $certificateRecord) {
            $filename = 'certificates/' . $certificateRecord->id . '_' . $enrollment->id . '.pdf';
            Storage::disk('local')->put($filename, $pdf->output());
            return $filename;
        }

        $downloadName = 'certificate-' . ($enrollment->user->name ?? 'student') . '-' . ($enrollment->course->slug ?? $enrollment->course_id) . '.pdf';
        return $pdf->download($downloadName);
    }

    /**
     * Issue certificate: create or update Certificate record and optionally generate PDF.
     */
    public function issue(CourseEnrollment $enrollment, ?CertificateTemplate $template = null, ?int $issuedByUserId = null, bool $storePdf = true): Certificate
    {
        $certificate = $enrollment->certificate;
        if (!$certificate) {
            $template = $this->resolveTemplate($enrollment, $template);
            if (!$template) {
                throw new \RuntimeException('لا يوجد قالب شهادة متاح.');
            }
            $certificate = Certificate::create([
                'course_enrollment_id' => $enrollment->id,
                'certificate_template_id' => $template->id,
                'issued_at' => now(),
                'issued_by' => $issuedByUserId,
            ]);
        } else {
            if ($template !== null) {
                $certificate->update(['certificate_template_id' => $template->id]);
            }
            $certificate->update(['issued_at' => now(), 'issued_by' => $issuedByUserId]);
        }

        if ($storePdf) {
            $path = $this->generate($enrollment, $certificate->template, true, $certificate);
            $certificate->update(['pdf_path' => $path]);
        }

        return $certificate->fresh();
    }
}
