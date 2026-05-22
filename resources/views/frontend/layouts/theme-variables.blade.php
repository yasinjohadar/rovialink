@php
    $themeColors = $themeColors ?? app(\App\Services\ThemeColorService::class)->toCssVariables();
@endphp
<style id="site-theme-variables">
:root {
  --accent-color: {{ $themeColors['accent'] }};
  --accent-hover: {{ $themeColors['accent_hover'] }};
  --accent-light: {{ $themeColors['accent_light'] }};
  --accent-lighter: {{ $themeColors['accent_lighter'] }};
  --accent-muted: {{ $themeColors['accent_muted'] }};
  --accent-rgb: {{ $themeColors['accent_rgb'] }};
  --bg-gradient-1: {{ $themeColors['bg_gradient_1'] }};
  --bg-gradient-2: {{ $themeColors['bg_gradient_2'] }};
  --bg-gradient-3: {{ $themeColors['bg_gradient_3'] }};
  --page-bg: {{ $themeColors['page_bg'] }};
  --glass-shadow: {{ $themeColors['glass_shadow'] }};
  --glass-hover-border: rgba(var(--accent-rgb), 0.4);
  --text-primary: {{ $themeColors['text_primary_light'] }};
}
[data-theme="dark"] {
  --glass-border: {{ $themeColors['glass_border_dark'] }};
  --glass-hover-border: {{ $themeColors['glass_hover_border_dark'] }};
}
</style>
