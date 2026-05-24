<?php

use App\Services\Ai\GeminiModelKeyNormalizer;

test('normalizes gemini display names to api ids', function () {
    $normalizer = new GeminiModelKeyNormalizer;

    expect($normalizer->normalize('Gemini 2.5 Flash'))->toBe('gemini-2.5-flash');
    expect($normalizer->normalize('gemini-2.0-flash'))->toBe('gemini-2.0-flash');
    expect($normalizer->normalize('models/gemini-1.5-pro'))->toBe('gemini-1.5-pro');
});

test('rejects invalid gemini model keys', function () {
    $normalizer = new GeminiModelKeyNormalizer;

    $normalizer->normalize('GPT-4');
})->throws(InvalidArgumentException::class);
