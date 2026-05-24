<?php

namespace App\Services\Store;

class StoreChatTopicGuard
{
    /**
     * مواضيع ممنوعة بوضوح فقط — التحيات والمحادثة الودية تمرّ للذكاء الاصطناعي.
     *
     * @var array<int, string>
     */
    protected array $forbiddenPatterns = [
        'طقس', 'الجو اليوم', 'درجة الحرارة', 'توقعات الطقس',
        'رياضة', 'مباراة', 'كرة القدم', 'انتخابات', 'سياسة',
        'اكتب قصة', 'اكتب قصيدة', 'اكتب رواية', 'قصة رومانسية', 'اكتب لي قصة', 'اكتب كود كامل',
        'وصفة طبخ', 'علاج طبي', 'تشخيص طبي', 'وصفة دواء',
        'برمجة من الصفر', 'python tutorial', 'javascript tutorial',
        'تاريخ العالم', 'من هو مخترع', 'اخبار اليوم',
        'نكتة', 'احكي نكتة', 'لغز رياضي',
    ];

    public function isClearlyOffTopic(string $message): bool
    {
        $normalized = mb_strtolower(trim($message));

        if ($normalized === '') {
            return true;
        }

        foreach ($this->forbiddenPatterns as $pattern) {
            if (mb_strpos($normalized, mb_strtolower($pattern)) !== false) {
                return true;
            }
        }

        return false;
    }
}
