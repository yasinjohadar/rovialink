<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Log;

class AiJsonParser
{
    /**
     * @return array<string, mixed>
     */
    public static function parse(string $response): array
    {
        $response = trim($response);

        if ($response === '') {
            return [];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $response, $matches)) {
            $decoded = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        if (preg_match('/(\{.*\})/s', $response, $matches)) {
            $decoded = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        Log::warning('AI JSON parse failed', ['snippet' => mb_substr($response, 0, 500)]);

        return [];
    }
}
