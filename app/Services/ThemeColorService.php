<?php

namespace App\Services;

class ThemeColorService
{
    public const DEFAULT_ACCENT = '#00D8E4';

    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {}

    public function accentHex(): string
    {
        $color = $this->siteSettings->get(
            SiteSettingsService::KEY_SITE_ACCENT_COLOR,
            self::DEFAULT_ACCENT
        );

        return $this->normalizeHex((string) $color) ?? self::DEFAULT_ACCENT;
    }

    /**
     * @return array<string, string>
     */
    public function toCssVariables(): array
    {
        $accent = $this->accentHex();
        $hover = $this->darken($accent, 0.12);
        $light = $this->lighten($accent, 0.35);
        $lighter = $this->lighten($accent, 0.55);
        $muted = $this->lighten($accent, 0.75);
        $rgb = $this->hexToRgbString($accent);

        return [
            'accent' => $accent,
            'accent_hover' => $hover,
            'accent_light' => $light,
            'accent_lighter' => $lighter,
            'accent_muted' => $muted,
            'accent_rgb' => $rgb,
            'bg_gradient_1' => $this->lighten($accent, 0.82),
            'bg_gradient_2' => $this->lighten($accent, 0.92),
            'bg_gradient_3' => $this->lighten($accent, 0.68),
            'page_bg' => $this->lighten($accent, 0.92),
            'glass_shadow' => "rgba({$rgb}, 0.12)",
            'glass_border_dark' => "rgba({$rgb}, 0.3)",
            'glass_hover_border_dark' => "rgba({$rgb}, 0.55)",
            'text_primary_light' => $this->darken($accent, 0.55),
        ];
    }

    public function normalizeHex(string $hex): ?string
    {
        $hex = trim($hex);
        if ($hex === '') {
            return null;
        }
        if (! str_starts_with($hex, '#')) {
            $hex = '#'.$hex;
        }
        if (preg_match('/^#([A-Fa-f0-9]{3})$/', $hex, $m)) {
            $h = $m[1];

            return '#'.$h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        }
        if (preg_match('/^#([A-Fa-f0-9]{6})$/', $hex)) {
            return strtolower($hex);
        }

        return null;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    public function hexToRgb(string $hex): array
    {
        $hex = $this->normalizeHex($hex) ?? self::DEFAULT_ACCENT;
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public function hexToRgbString(string $hex): string
    {
        [$r, $g, $b] = $this->hexToRgb($hex);

        return "{$r}, {$g}, {$b}";
    }

    public function darken(string $hex, float $amount): string
    {
        [$r, $g, $b] = $this->hexToRgb($hex);
        $r = (int) max(0, round($r * (1 - $amount)));
        $g = (int) max(0, round($g * (1 - $amount)));
        $b = (int) max(0, round($b * (1 - $amount)));

        return $this->rgbToHex($r, $g, $b);
    }

    public function lighten(string $hex, float $amount): string
    {
        [$r, $g, $b] = $this->hexToRgb($hex);
        $r = (int) min(255, round($r + (255 - $r) * $amount));
        $g = (int) min(255, round($g + (255 - $g) * $amount));
        $b = (int) min(255, round($b + (255 - $b) * $amount));

        return $this->rgbToHex($r, $g, $b);
    }

    private function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
