<?php

use App\Services\Store\StoreChatTopicGuard;

test('clearly off topic messages are detected', function () {
    $guard = new StoreChatTopicGuard;

    expect($guard->isClearlyOffTopic('ما هو الطقس في الرياض اليوم؟'))->toBeTrue();
    expect($guard->isClearlyOffTopic('اكتب لي قصة رومانسية'))->toBeTrue();
});

test('greetings and casual chat are allowed through', function () {
    $guard = new StoreChatTopicGuard;

    expect($guard->isClearlyOffTopic('مرحبا'))->toBeFalse();
    expect($guard->isClearlyOffTopic('كيفك ياحبيب'))->toBeFalse();
    expect($guard->isClearlyOffTopic('السلام عليكم'))->toBeFalse();
    expect($guard->isClearlyOffTopic('شكراً على المساعدة'))->toBeFalse();
});

test('product related messages are allowed', function () {
    $guard = new StoreChatTopicGuard;

    expect($guard->isClearlyOffTopic('كم سعر Windows 11 Pro عندكم؟'))->toBeFalse();
    expect($guard->isClearlyOffTopic('هل الإضافة متوافقة مع Elementor؟'))->toBeFalse();
});
