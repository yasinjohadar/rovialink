@php
    $chatEnabled = (bool) \App\Models\AISetting::getValue('store_chat_enabled', false);
@endphp
@if($chatEnabled)
<link rel="stylesheet" href="{{ asset('frontend/assets/css/store-chat-widget.css') }}?v=1">
<button type="button" id="store-chat-launcher" class="store-chat-launcher" aria-label="فتح محادثة المساعد">
    &#128172;
</button>
<div id="store-chat-panel" class="store-chat-panel" role="dialog" aria-label="محادثة المنتجات">
    <div class="store-chat-header">
        <h6>مساعد المنتجات</h6>
        <button type="button" id="store-chat-close" class="store-chat-close" aria-label="إغلاق">&times;</button>
    </div>
    <div id="store-chat-messages" class="store-chat-messages"></div>
    <div id="store-chat-typing" class="store-chat-typing"></div>
    <form id="store-chat-form" class="store-chat-form">
        <input type="text" id="store-chat-input" placeholder="اسأل عن منتج..." maxlength="2000" autocomplete="off">
        <button type="submit" id="store-chat-send">إرسال</button>
    </form>
</div>
<script>
    window.STORE_CHAT_CONFIG = {
        enabled: true,
        welcomeMessage: @json(\App\Models\AISetting::getValue('store_chat_welcome_message', 'مرحباً! اسألني عن منتجاتنا.')),
        configUrl: @json(route('frontend.chat.config')),
        sessionUrl: @json(route('frontend.chat.session')),
        messageUrl: @json(route('frontend.chat.message')),
        historyUrl: @json(route('frontend.chat.history')),
    };
</script>
<script src="{{ asset('frontend/assets/js/store-chat-widget.js') }}?v=1"></script>
@endif
