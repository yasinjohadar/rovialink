<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSettingsService
{
    public const GROUP = 'site';

    public const KEY_SITE_NAME = 'site_name';
    public const KEY_SITE_DESCRIPTION = 'site_description';
    public const KEY_SITE_LOGO = 'site_logo';
    public const KEY_SITE_FAVICON = 'site_favicon';
    public const KEY_SITE_ACCENT_COLOR = 'site_accent_color';
    public const KEY_SITE_MAINTENANCE_MODE = 'site_maintenance_mode';
    public const KEY_SITE_MAINTENANCE_MESSAGE = 'site_maintenance_message';
    public const KEY_SITE_CONTACT_EMAIL = 'site_contact_email';
    public const KEY_SITE_CONTACT_PHONE = 'site_contact_phone';
    public const KEY_SITE_ADDRESS = 'site_address';
    public const KEY_SITE_TIMEZONE = 'site_timezone';
    public const KEY_SITE_LOCALE = 'site_locale';
    public const KEY_SITE_META_KEYWORDS = 'site_meta_keywords';
    public const KEY_SITE_META_DESCRIPTION = 'site_meta_description';
    public const KEY_SITE_FOOTER_TEXT = 'site_footer_text';
    public const KEY_SITE_FACEBOOK_URL = 'site_facebook_url';
    public const KEY_SITE_TWITTER_URL = 'site_twitter_url';
    public const KEY_SITE_INSTAGRAM_URL = 'site_instagram_url';
    public const KEY_SITE_WHATSAPP_NUMBER = 'site_whatsapp_number';

    public const KEY_HERO_BADGE = 'hero_badge';
    public const KEY_HERO_TITLE_PREFIX = 'hero_title_prefix';
    public const KEY_HERO_TYPING_WORDS = 'hero_typing_words';
    public const KEY_HERO_SUBTITLE = 'hero_subtitle';
    public const KEY_HERO_BTN_PRIMARY_LABEL = 'hero_btn_primary_label';
    public const KEY_HERO_BTN_PRIMARY_URL = 'hero_btn_primary_url';
    public const KEY_HERO_BTN_SECONDARY_LABEL = 'hero_btn_secondary_label';
    public const KEY_HERO_BTN_SECONDARY_URL = 'hero_btn_secondary_url';
    public const KEY_HERO_IMAGE = 'hero_image';
    public const KEY_HERO_BG_MODE = 'hero_bg_mode';
    public const KEY_HERO_BG_COLOR = 'hero_bg_color';
    public const KEY_HERO_BG_IMAGE = 'hero_bg_image';
    public const KEY_HERO_STATS = 'hero_stats';

    private const CACHE_KEY = 'site_settings';
    private const CACHE_TTL = 3600;

    /**
     * Schema for all site settings: key => [ type, default, label_ar, section, hint ]
     */
    public static function schema(): array
    {
        return [
            self::KEY_SITE_NAME => [
                'type' => 'string',
                'default' => config('app.name', 'المتجر'),
                'label' => 'اسم الموقع',
                'section' => 'general',
                'hint' => 'يظهر في الهيدر والعنوان والبريد.',
            ],
            self::KEY_SITE_DESCRIPTION => [
                'type' => 'string',
                'default' => '',
                'label' => 'وصف الموقع',
                'section' => 'general',
                'hint' => 'وصف مختصر يظهر في محركات البحث.',
            ],
            self::KEY_SITE_LOGO => [
                'type' => 'string',
                'default' => '',
                'label' => 'شعار الموقع',
                'section' => 'branding',
                'hint' => 'مسار الصورة (يُرفع من الحقل أدناه).',
            ],
            self::KEY_SITE_FAVICON => [
                'type' => 'string',
                'default' => '',
                'label' => 'أيقونة الموقع (Favicon)',
                'section' => 'branding',
                'hint' => 'صورة صغيرة تظهر في تاب المتصفح.',
            ],
            self::KEY_SITE_ACCENT_COLOR => [
                'type' => 'color',
                'default' => '#387e99',
                'label' => 'لون التمييز (الفرونت اند)',
                'section' => 'branding',
                'hint' => 'يُطبَّق على الأزرار، الروابط النشطة، والهيدر. الافتراضي: تركوازي.',
            ],
            self::KEY_SITE_MAINTENANCE_MODE => [
                'type' => 'boolean',
                'default' => false,
                'label' => 'تفعيل وضع الصيانة',
                'section' => 'maintenance',
                'hint' => 'عند التفعيل يظهر زوار الموقع صفحة صيانة فقط.',
            ],
            self::KEY_SITE_MAINTENANCE_MESSAGE => [
                'type' => 'string',
                'default' => 'الموقع قيد الصيانة. نعود قريباً.',
                'label' => 'رسالة الصيانة',
                'section' => 'maintenance',
                'hint' => 'النص الذي يراه الزوار أثناء الصيانة.',
            ],
            self::KEY_SITE_CONTACT_EMAIL => [
                'type' => 'string',
                'default' => '',
                'label' => 'البريد الإلكتروني للتواصل',
                'section' => 'contact',
                'hint' => '',
            ],
            self::KEY_SITE_CONTACT_PHONE => [
                'type' => 'string',
                'default' => '',
                'label' => 'رقم الهاتف',
                'section' => 'contact',
                'hint' => '',
            ],
            self::KEY_SITE_ADDRESS => [
                'type' => 'string',
                'default' => '',
                'label' => 'العنوان',
                'section' => 'contact',
                'hint' => 'عنوان المتجر أو الشركة.',
            ],
            self::KEY_SITE_WHATSAPP_NUMBER => [
                'type' => 'string',
                'default' => '',
                'label' => 'رقم واتساب للتواصل',
                'section' => 'contact',
                'hint' => 'بدون + أو مسافات، مثال: 966501234567',
            ],
            self::KEY_SITE_TIMEZONE => [
                'type' => 'string',
                'default' => config('app.timezone', 'Asia/Riyadh'),
                'label' => 'المنطقة الزمنية',
                'section' => 'locale',
                'hint' => 'مثال: Asia/Riyadh',
            ],
            self::KEY_SITE_LOCALE => [
                'type' => 'string',
                'default' => config('app.locale', 'ar'),
                'label' => 'اللغة الافتراضية',
                'section' => 'locale',
                'hint' => 'مثال: ar, en',
            ],
            self::KEY_SITE_META_KEYWORDS => [
                'type' => 'string',
                'default' => '',
                'label' => 'كلمات ميتا (SEO)',
                'section' => 'seo',
                'hint' => 'كلمات مفصولة بفاصلة للبحث.',
            ],
            self::KEY_SITE_META_DESCRIPTION => [
                'type' => 'string',
                'default' => '',
                'label' => 'وصف ميتا (SEO)',
                'section' => 'seo',
                'hint' => 'وصف يظهر في نتائج محركات البحث.',
            ],
            self::KEY_SITE_FOOTER_TEXT => [
                'type' => 'string',
                'default' => '',
                'label' => 'نص التذييل',
                'section' => 'seo',
                'hint' => 'نص يظهر في أسفل الموقع.',
            ],
            self::KEY_SITE_FACEBOOK_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط فيسبوك',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_SITE_TWITTER_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط تويتر / X',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_SITE_INSTAGRAM_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط انستغرام',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_HERO_BADGE => [
                'type' => 'string',
                'default' => 'متجرك الرقمي — تسليم فوري وآمن',
                'label' => 'شارة الهيرو',
                'section' => 'homepage',
                'hint' => 'نص صغير أعلى العنوان.',
            ],
            self::KEY_HERO_TITLE_PREFIX => [
                'type' => 'string',
                'default' => 'احصل على أفضل',
                'label' => 'بادئة العنوان',
                'section' => 'homepage',
                'hint' => 'السطر الثابت قبل الكلمات المتحركة.',
            ],
            self::KEY_HERO_TYPING_WORDS => [
                'type' => 'json',
                'default' => '',
                'label' => 'كلمات العنوان المتحركة',
                'section' => 'homepage',
                'hint' => 'سطر لكل كلمة، أو JSON مصفوفة.',
            ],
            self::KEY_HERO_SUBTITLE => [
                'type' => 'string',
                'default' => 'منتجات رقمية أصلية بأسعار تنافسية — تنزيل فوري بعد الدفع، دفع آمن، ودعم فني على مدار الساعة.',
                'label' => 'وصف الهيرو',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_BTN_PRIMARY_LABEL => [
                'type' => 'string',
                'default' => 'تصفح المنتجات',
                'label' => 'نص الزر الرئيسي',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_BTN_PRIMARY_URL => [
                'type' => 'string',
                'default' => '/shop',
                'label' => 'رابط الزر الرئيسي',
                'section' => 'homepage',
                'hint' => 'مسار نسبي أو رابط كامل.',
            ],
            self::KEY_HERO_BTN_SECONDARY_LABEL => [
                'type' => 'string',
                'default' => 'التصنيفات',
                'label' => 'نص الزر الثانوي',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_BTN_SECONDARY_URL => [
                'type' => 'string',
                'default' => '/categories',
                'label' => 'رابط الزر الثانوي',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_IMAGE => [
                'type' => 'string',
                'default' => '',
                'label' => 'صورة الهيرو (جانب)',
                'section' => 'homepage',
                'hint' => 'تظهر على يسار القسم في الصفحة الرئيسية.',
            ],
            self::KEY_HERO_BG_MODE => [
                'type' => 'string',
                'default' => 'gradient',
                'label' => 'نوع خلفية الهيرو',
                'section' => 'homepage',
                'hint' => 'gradient: تدرج وأشكال | color: لون ثابت | image: صورة خلفية.',
            ],
            self::KEY_HERO_BG_COLOR => [
                'type' => 'color',
                'default' => '#0a1628',
                'label' => 'لون خلفية الهيرو',
                'section' => 'homepage',
                'hint' => 'يُستخدم عند اختيار «لون ثابت».',
            ],
            self::KEY_HERO_BG_IMAGE => [
                'type' => 'string',
                'default' => '',
                'label' => 'صورة خلفية الهيرو',
                'section' => 'homepage',
                'hint' => 'تُستخدم عند اختيار «صورة خلفية».',
            ],
            self::KEY_HERO_STATS => [
                'type' => 'json',
                'default' => '',
                'label' => 'إحصائيات الهيرو',
                'section' => 'homepage',
                'hint' => 'JSON: مصفوفة من {icon, target, label}',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function defaultHeroTypingWords(): array
    {
        return [
            'البرمجيات',
            'الاشتراكات الرقمية',
            'القوالب والأصول',
            'الكورسات الإلكترونية',
        ];
    }

    /**
     * @return list<array{icon: string, target: int, label: string}>
     */
    public static function defaultHeroStats(): array
    {
        return [
            ['icon' => 'fa-cloud-arrow-down', 'target' => 5000, 'label' => 'منتج رقمي'],
            ['icon' => 'fa-shield-halved', 'target' => 150, 'label' => 'دفع آمن'],
            ['icon' => 'fa-bolt', 'target' => 85000, 'label' => 'تسليم فوري'],
            ['icon' => 'fa-headset', 'target' => 15000, 'label' => 'دعم 24/7'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function heroKeys(): array
    {
        return [
            self::KEY_HERO_BADGE,
            self::KEY_HERO_TITLE_PREFIX,
            self::KEY_HERO_TYPING_WORDS,
            self::KEY_HERO_SUBTITLE,
            self::KEY_HERO_BTN_PRIMARY_LABEL,
            self::KEY_HERO_BTN_PRIMARY_URL,
            self::KEY_HERO_BTN_SECONDARY_LABEL,
            self::KEY_HERO_BTN_SECONDARY_URL,
            self::KEY_HERO_IMAGE,
            self::KEY_HERO_BG_MODE,
            self::KEY_HERO_BG_COLOR,
            self::KEY_HERO_BG_IMAGE,
            self::KEY_HERO_STATS,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function schemaForSection(string $section): array
    {
        return array_filter(
            self::schema(),
            fn (array $def) => ($def['section'] ?? '') === $section
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function heroValidationRules(): array
    {
        $all = self::validationRules();
        $rules = array_intersect_key($all, array_flip(self::heroKeys()));

        return $rules + [
            'hero_image_file' => $all['hero_image_file'],
            'hero_bg_image_file' => $all['hero_bg_image_file'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function saveHeroSettings(array $validated, ?\Illuminate\Http\UploadedFile $heroImage = null, ?\Illuminate\Http\UploadedFile $heroBgImage = null): void
    {
        if ($heroImage) {
            $this->storeUpload(self::KEY_HERO_IMAGE, $heroImage);
        }
        if ($heroBgImage) {
            $this->storeUpload(self::KEY_HERO_BG_IMAGE, $heroBgImage);
        }

        if (isset($validated[self::KEY_HERO_TYPING_WORDS])) {
            $words = self::parseTypingWordsInput((string) $validated[self::KEY_HERO_TYPING_WORDS]);
            $validated[self::KEY_HERO_TYPING_WORDS] = json_encode($words, JSON_UNESCAPED_UNICODE);
        }
        if (isset($validated[self::KEY_HERO_STATS])) {
            $stats = self::parseHeroStatsInput((string) $validated[self::KEY_HERO_STATS]);
            $validated[self::KEY_HERO_STATS] = json_encode($stats, JSON_UNESCAPED_UNICODE);
        }

        $payload = array_intersect_key($validated, array_flip(self::heroKeys()));
        $this->setMany($payload);
    }

    public static function sectionLabels(): array
    {
        return [
            'general' => 'عام',
            'branding' => 'الهوية والعلامة',
            'homepage' => 'الصفحة الرئيسية',
            'contact' => 'التواصل',
            'maintenance' => 'وضع الصيانة',
            'locale' => 'اللغة والمنطقة',
            'seo' => 'SEO والتذييل',
            'social' => 'وسائل التواصل',
        ];
    }

    /**
     * Normalize hero typing words from admin textarea (lines or JSON).
     *
     * @return list<string>
     */
    public static function parseTypingWordsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultHeroTypingWords();
        }
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('strval', $decoded)));
            }
        }
        $lines = preg_split('/\r\n|\r|\n/', $input) ?: [];

        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * @return list<array{icon: string, target: int, label: string}>
     */
    public static function parseHeroStatsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultHeroStats();
        }
        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return self::defaultHeroStats();
        }
        $out = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            $out[] = [
                'icon' => (string) ($item['icon'] ?? 'fa-star'),
                'target' => (int) ($item['target'] ?? 0),
                'label' => (string) ($item['label'] ?? ''),
            ];
        }

        return $out ?: self::defaultHeroStats();
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $schema = self::schema();
            $out = [];
            foreach (array_keys($schema) as $key) {
                $def = $schema[$key];
                $value = SystemSetting::getValue($key, $def['default']);
                $out[$key] = $this->castValue($value, $def['type']);
            }
            return $out;
        });
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setMany(array $data): void
    {
        $schema = self::schema();
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $schema)) {
                continue;
            }
            $def = $schema[$key];
            $type = $def['type'];
            if ($type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            } elseif (in_array($type, ['json', 'array'], true)) {
                $value = is_array($value)
                    ? json_encode($value, JSON_UNESCAPED_UNICODE)
                    : (string) $value;
            } else {
                $value = (string) $value;
            }
            SystemSetting::set($key, $value, $type, self::GROUP);
        }
        $this->clearCache();
    }

    public function storeUpload(string $key, $file): ?string
    {
        $schema = self::schema();
        if (!isset($schema[$key]) || !$file || !$file->isValid()) {
            return null;
        }

        $disk = config('filesystems.default', 'public');
        $oldPath = $this->get($key);
        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }

        $path = $file->store('site-settings', $disk);
        $this->setMany([$key => $path]);
        return $path;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => is_string($value) && $value !== ''
                ? json_decode($value, true)
                : (is_array($value) ? $value : null),
            default => (string) $value,
        };
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function validationRules(): array
    {
        return [
            self::KEY_SITE_NAME => ['nullable', 'string', 'max:255'],
            self::KEY_SITE_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_LOGO => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_FAVICON => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_ACCENT_COLOR => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            self::KEY_SITE_MAINTENANCE_MODE => ['nullable', 'boolean'],
            self::KEY_SITE_MAINTENANCE_MESSAGE => ['nullable', 'string', 'max:1000'],
            self::KEY_SITE_CONTACT_EMAIL => ['nullable', 'email', 'max:255'],
            self::KEY_SITE_CONTACT_PHONE => ['nullable', 'string', 'max:50'],
            self::KEY_SITE_ADDRESS => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_WHATSAPP_NUMBER => ['nullable', 'string', 'max:20'],
            self::KEY_SITE_TIMEZONE => ['nullable', 'string', 'max:50'],
            self::KEY_SITE_LOCALE => ['nullable', 'string', 'max:10'],
            self::KEY_SITE_META_KEYWORDS => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_META_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_FOOTER_TEXT => ['nullable', 'string', 'max:2000'],
            self::KEY_SITE_FACEBOOK_URL => ['nullable', 'url', 'max:500'],
            self::KEY_SITE_TWITTER_URL => ['nullable', 'url', 'max:500'],
            self::KEY_SITE_INSTAGRAM_URL => ['nullable', 'url', 'max:500'],
            'site_logo_file' => ['nullable', 'image', 'max:2048'],
            'site_favicon_file' => ['nullable', 'image', 'max:512'],
            self::KEY_HERO_BADGE => ['nullable', 'string', 'max:255'],
            self::KEY_HERO_TITLE_PREFIX => ['nullable', 'string', 'max:255'],
            self::KEY_HERO_TYPING_WORDS => ['nullable', 'string', 'max:5000'],
            self::KEY_HERO_SUBTITLE => ['nullable', 'string', 'max:2000'],
            self::KEY_HERO_BTN_PRIMARY_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_HERO_BTN_PRIMARY_URL => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_BTN_SECONDARY_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_HERO_BTN_SECONDARY_URL => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_IMAGE => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_BG_MODE => ['nullable', 'string', 'in:gradient,color,image'],
            self::KEY_HERO_BG_COLOR => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            self::KEY_HERO_BG_IMAGE => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_STATS => ['nullable', 'string', 'max:10000'],
            'hero_image_file' => ['nullable', 'image', 'max:4096'],
            'hero_bg_image_file' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
