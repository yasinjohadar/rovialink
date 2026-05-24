<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class GeminiModelKeyNormalizer
{
    /**
     * أسماء العرض الشائعة → معرف API الرسمي لـ Google Gemini.
     *
     * @var array<string, string>
     */
    protected const ALIASES = [
        'gemini pro' => 'gemini-1.5-pro',
        'gemini ultra' => 'gemini-1.5-pro',
        'gemini-pro' => 'gemini-1.5-pro',
        'gemini-ultra' => 'gemini-1.5-pro',
        'gemini 1.5 pro' => 'gemini-1.5-pro',
        'gemini 1.5 flash' => 'gemini-1.5-flash',
        'gemini 2.0 flash' => 'gemini-2.0-flash',
        'gemini 2.5 flash' => 'gemini-2.5-flash',
        'gemini 2.5 pro' => 'gemini-2.5-pro',
        'gemini flash' => 'gemini-2.0-flash',
    ];

  /**
     * يحوّل model_key المخزّن إلى معرف يقبله Gemini API / Laravel AI.
     */
    public function normalize(string $modelKey): string
    {
        $key = trim($modelKey);
        if ($key === '') {
            throw new \InvalidArgumentException('معرف الموديل فارغ.');
        }

        if (str_starts_with(strtolower($key), 'models/')) {
            $key = substr($key, strlen('models/'));
        }

        $aliasKey = strtolower(preg_replace('/\s+/u', ' ', $key) ?? $key);
        if (isset(self::ALIASES[$aliasKey])) {
            return self::ALIASES[$aliasKey];
        }

        if (preg_match('/\s/u', $key) && ! str_contains($key, '/')) {
            $slug = Str::slug($key);
            $guess = preg_replace('/^gemini-(\d+)-(\d+)-/', 'gemini-$1.$2-', $slug) ?? $slug;

            if ($this->looksLikeGeminiApiId($guess)) {
                return $guess;
            }

            throw new \InvalidArgumentException(
                'معرف الموديل لـ Google يجب أن يكون بصيغة API مثل: gemini-2.0-flash أو gemini-2.5-flash — وليس اسم العرض. '
                .'اختر من القائمة أو انسخ المعرف من Google AI Studio.'
            );
        }

        if (! $this->looksLikeGeminiApiId($key)) {
            throw new \InvalidArgumentException(
                'صيغة معرف الموديل غير صالحة لـ Gemini. أمثلة: gemini-2.0-flash, gemini-1.5-pro, gemini-2.5-flash'
            );
        }

        return $key;
    }

    public function looksLikeGeminiApiId(string $key): bool
    {
        return (bool) preg_match('/^gemini-[\w][\w\.\-]*$/i', $key);
    }

    public function isGeminiApiUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && str_contains(strtolower($host), 'generativelanguage.googleapis.com');
    }
}
