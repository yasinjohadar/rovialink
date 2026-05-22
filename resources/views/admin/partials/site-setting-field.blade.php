@php
    $value = old($key, $settings[$key] ?? $def['default']);
    $isBoolean = ($def['type'] ?? 'string') === 'boolean';
    $fullWidth = $fullWidth ?? ($isBoolean || in_array($key, [
        \App\Services\SiteSettingsService::KEY_HERO_SUBTITLE,
        \App\Services\SiteSettingsService::KEY_HERO_TYPING_WORDS,
        \App\Services\SiteSettingsService::KEY_HERO_STATS,
        \App\Services\SiteSettingsService::KEY_HERO_IMAGE,
        \App\Services\SiteSettingsService::KEY_HERO_BG_IMAGE,
        \App\Services\SiteSettingsService::KEY_SITE_DESCRIPTION,
        \App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MESSAGE,
        \App\Services\SiteSettingsService::KEY_SITE_ADDRESS,
        \App\Services\SiteSettingsService::KEY_SITE_META_DESCRIPTION,
        \App\Services\SiteSettingsService::KEY_SITE_FOOTER_TEXT,
    ], true));
    $disk = config('filesystems.default', 'public');
@endphp
<div class="col-12 {{ $fullWidth ? '' : 'col-md-6' }}">
    @if ($isBoolean)
        <div class="form-check form-switch">
            <input type="hidden" name="{{ $key }}" value="0">
            <input class="form-check-input @error($key) is-invalid @enderror" type="checkbox" name="{{ $key }}" value="1" id="input-{{ $key }}"
                   {{ $value ? 'checked' : '' }}>
            <label class="form-check-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
            @if (!empty($def['hint']))
                <small class="d-block text-muted">{{ $def['hint'] }}</small>
            @endif
            @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_LOGO)
        <label class="form-label">{{ $def['label'] }}</label>
        @if ($value && \Illuminate\Support\Facades\Storage::disk($disk)->exists($value))
            <div class="mb-2">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($value) }}" alt="الشعار" class="img-thumbnail" style="max-height: 60px;">
            </div>
        @endif
        <input type="file" class="form-control @error('site_logo_file') is-invalid @enderror" name="site_logo_file" accept="image/*">
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error('site_logo_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_FAVICON)
        <label class="form-label">{{ $def['label'] }}</label>
        @if ($value && \Illuminate\Support\Facades\Storage::disk($disk)->exists($value))
            <div class="mb-2">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($value) }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px;">
            </div>
        @endif
        <input type="file" class="form-control @error('site_favicon_file') is-invalid @enderror" name="site_favicon_file" accept="image/*">
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error('site_favicon_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif ($key === \App\Services\SiteSettingsService::KEY_HERO_IMAGE)
        <label class="form-label">{{ $def['label'] }}</label>
        @if ($value && \Illuminate\Support\Facades\Storage::disk($disk)->exists($value))
            <div class="mb-2">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($value) }}" alt="صورة الهيرو" class="img-thumbnail" style="max-height: 160px;">
            </div>
        @endif
        <input type="file" class="form-control @error('hero_image_file') is-invalid @enderror" name="hero_image_file" accept="image/*">
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error('hero_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif ($key === \App\Services\SiteSettingsService::KEY_HERO_BG_IMAGE)
        <label class="form-label">{{ $def['label'] }}</label>
        @if ($value && \Illuminate\Support\Facades\Storage::disk($disk)->exists($value))
            <div class="mb-2">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($value) }}" alt="خلفية الهيرو" class="img-thumbnail" style="max-height: 100px;">
            </div>
        @endif
        <input type="file" class="form-control @error('hero_bg_image_file') is-invalid @enderror" name="hero_bg_image_file" accept="image/*">
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error('hero_bg_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif ($key === \App\Services\SiteSettingsService::KEY_HERO_BG_MODE)
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <select class="form-select @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}">
            <option value="gradient" {{ $value === 'gradient' ? 'selected' : '' }}>تدرج وأشكال (افتراضي)</option>
            <option value="color" {{ $value === 'color' ? 'selected' : '' }}>لون ثابت</option>
            <option value="image" {{ $value === 'image' ? 'selected' : '' }}>صورة خلفية</option>
        </select>
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif ($key === \App\Services\SiteSettingsService::KEY_HERO_TYPING_WORDS)
        @php
            $typingDisplay = is_array($value)
                ? implode("\n", $value)
                : (old($key) ?? implode("\n", \App\Services\SiteSettingsService::defaultHeroTypingWords()));
        @endphp
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <textarea class="form-control @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}" rows="5">{{ $typingDisplay }}</textarea>
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif ($key === \App\Services\SiteSettingsService::KEY_HERO_STATS)
        @php
            $statsDisplay = is_array($value)
                ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                : (old($key) ?? json_encode(\App\Services\SiteSettingsService::defaultHeroStats(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        @endphp
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <textarea class="form-control font-monospace @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}" rows="8">{{ $statsDisplay }}</textarea>
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif (in_array($key, [
        \App\Services\SiteSettingsService::KEY_SITE_DESCRIPTION,
        \App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MESSAGE,
        \App\Services\SiteSettingsService::KEY_SITE_ADDRESS,
        \App\Services\SiteSettingsService::KEY_SITE_META_DESCRIPTION,
        \App\Services\SiteSettingsService::KEY_SITE_FOOTER_TEXT,
        \App\Services\SiteSettingsService::KEY_HERO_SUBTITLE,
    ], true))
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <textarea class="form-control @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}" rows="3">{{ $value }}</textarea>
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @elseif (($def['type'] ?? '') === 'color')
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <input type="color" class="form-control form-control-color @error($key) is-invalid @enderror"
                   id="color-picker-{{ $key }}" value="{{ $value ?: '#387e99' }}" style="width: 3.5rem; height: 2.5rem;">
            <input type="text" class="form-control @error($key) is-invalid @enderror" name="{{ $key }}"
                   id="input-{{ $key }}" value="{{ $value }}" placeholder="#387e99" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" maxlength="7" style="max-width: 8rem;">
        </div>
        @if (!empty($def['hint']))<small class="text-muted d-block mt-1">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        @push('scripts')
            <script>
                (function () {
                    const picker = document.getElementById('color-picker-{{ $key }}');
                    const text = document.getElementById('input-{{ $key }}');
                    if (!picker || !text) return;
                    picker.addEventListener('input', function () { text.value = this.value; });
                    text.addEventListener('input', function () {
                        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) picker.value = this.value;
                    });
                })();
            </script>
        @endpush
    @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_LOCALE)
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <select class="form-select @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}">
            <option value="ar" {{ $value === 'ar' ? 'selected' : '' }}>العربية</option>
            <option value="en" {{ $value === 'en' ? 'selected' : '' }}>English</option>
        </select>
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @else
        <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
        <input type="text" class="form-control @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}" value="{{ $value }}">
        @if (!empty($def['hint']))<small class="text-muted">{{ $def['hint'] }}</small>@endif
        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @endif
</div>
