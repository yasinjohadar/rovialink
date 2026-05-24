<?php

namespace App\Services\Seo;

class SeoAuditResult
{
    /**
     * @param  array<int, array<string, mixed>>  $checks
     */
    public function __construct(
        public int $score,
        public array $checks,
        public string $summaryAr = '',
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'checks' => $this->checks,
            'summary_ar' => $this->summaryAr,
        ];
    }
}
