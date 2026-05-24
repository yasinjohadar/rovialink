<?php

use App\Services\Ai\AiContentOrchestrator;

test('rejects pipe placeholder as product description', function () {
    $orchestrator = app(AiContentOrchestrator::class);
    $method = new ReflectionMethod($orchestrator, 'isValidProductDescription');
    $method->setAccessible(true);

    expect($method->invoke($orchestrator, '|short_description'))->toBeFalse();
    expect($method->invoke($orchestrator, '<p>'.str_repeat('محتوى ', 80).'</p>'))->toBeTrue();
});
