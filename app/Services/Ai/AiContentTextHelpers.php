<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

trait AiContentTextHelpers
{
    protected function cleanText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }

        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text);
        $text = preg_replace('/[^\p{Arabic}\p{L}\p{N}\s.,!?;:()\-\'"]/u', '', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    protected function cleanKeywords(string $keywords): string
    {
        if ($keywords === '') {
            return '';
        }

        if (! mb_check_encoding($keywords, 'UTF-8')) {
            $keywords = mb_convert_encoding($keywords, 'UTF-8', 'auto');
        }

        $keywordArray = preg_split('/[,،\n\r\t|]/u', $keywords);
        $cleanedKeywords = [];

        foreach ($keywordArray as $keyword) {
            $keyword = trim(preg_replace('/[^\p{Arabic}\p{L}\p{N}\s-]/u', '', trim($keyword)));
            $keyword = preg_replace('/\s+/u', ' ', $keyword);

            if (mb_strlen($keyword) < 2 || $keyword === '') {
                continue;
            }

            if (! in_array($keyword, $cleanedKeywords, true)) {
                $cleanedKeywords[] = $keyword;
            }
        }

        return implode(', ', $cleanedKeywords);
    }

    protected function extractKeywords(string $content, int $count = 10): string
    {
        $text = $this->cleanText(strip_tags($content));
        $words = preg_split('/[\s\p{P}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = ['في', 'من', 'إلى', 'على', 'هذا', 'هذه', 'التي', 'الذي', 'كان', 'كانت', 'مع', 'عن', 'أن', 'إن', 'ما', 'لا', 'لم', 'و'];

        $filteredWords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) < 3 || in_array($word, $stopWords, true)) {
                continue;
            }
            $filteredWords[] = $word;
        }

        $wordFreq = array_count_values($filteredWords);
        arsort($wordFreq);

        return implode(', ', array_slice(array_keys($wordFreq), 0, $count));
    }

    protected function extractMainKeyword(string $topic, string $content): string
    {
        $topicWords = preg_split('/\s+/u', trim($topic), -1, PREG_SPLIT_NO_EMPTY);

        return count($topicWords) <= 3
            ? trim($topic)
            : implode(' ', array_slice($topicWords, 0, 3));
    }

    protected function generateExcerpt(string $content): string
    {
        $text = preg_replace('/\s+/u', ' ', strip_tags($content));

        return Str::limit(trim($text), 150);
    }

    protected function generateSlug(string $title): string
    {
        $slug = preg_replace('/\s+/u', '-', trim($title));
        $slug = preg_replace('/[^\p{Arabic}a-zA-Z0-9-]/u', '', $slug);
        $slug = preg_replace('/-+/u', '-', $slug);

        return trim($slug, '-') ?: Str::slug($title);
    }
}
