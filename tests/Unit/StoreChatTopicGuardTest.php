<?php

use App\Services\Store\StoreChatTopicGuard;

test('clearly off topic messages are detected', function () {
    $guard = new StoreChatTopicGuard;

    expect($guard->isClearlyOffTopic('ما هو الطقس في الرياض اليوم؟'))->toBeTrue();
    expect($guard->isClearlyOffTopic('اكتب لي قصة رومانسية'))->toBeTrue();
});

test('product related messages are not flagged off topic', function () {
    $guard = new StoreChatTopicGuard;

    expect($guard->isClearlyOffTopic('كم سعر Windows 11 Pro عندكم؟'))->toBeFalse();
    expect($guard->isClearlyOffTopic('هل الإضافة متوافقة مع Elementor؟'))->toBeFalse();
});
