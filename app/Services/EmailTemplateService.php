<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class EmailTemplateService
{
    public function getTemplate(string $event, ?string $locale = null): ?EmailTemplate
    {
        $locale = $locale ?: App::getLocale() ?: config('app.locale', 'ar');

        $template = EmailTemplate::forEventAndLocale($event, $locale)->first();
        if ($template) {
            return $template;
        }

        // Fallback إلى العربية أو الإنجليزية إذا لم يوجد للّغة المطلوبة
        if ($locale !== 'ar') {
            $template = EmailTemplate::forEventAndLocale($event, 'ar')->first();
            if ($template) {
                return $template;
            }
        }
        if ($locale !== 'en') {
            $template = EmailTemplate::forEventAndLocale($event, 'en')->first();
            if ($template) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Render subject and HTML by replacing {{ placeholders }}.
     *
     * @param  EmailTemplate  $template
     * @param  array<string, mixed>  $data
     * @return array{subject: string, html: string}
     */
    public function render(EmailTemplate $template, array $data = []): array
    {
        $flat = $this->flattenData($data);

        $subject = $this->replacePlaceholders($template->subject, $flat);
        $html = $this->replacePlaceholders($template->body_html, $flat);

        return [
            'subject' => $subject,
            'html' => $html,
        ];
    }

    /**
     * Flatten nested data keys to dot.notation for use as {{ key }}.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    protected function flattenData(array $data): array
    {
        $flattened = Arr::dot($data);
        $result = [];
        foreach ($flattened as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $result[$key] = (string) $value;
            }
        }
        return $result;
    }

    /**
     * Replace {{ key }} placeholders in the given text.
     *
     * @param  string  $text
     * @param  array<string, string>  $vars
     */
    protected function replacePlaceholders(string $text, array $vars): string
    {
        $search = [];
        $replace = [];
        foreach ($vars as $key => $value) {
            $search[] = '{{ ' . $key . ' }}';
            $replace[] = $value;
        }
        return strtr($text, array_combine($search, $replace) ?: []);
    }
}

