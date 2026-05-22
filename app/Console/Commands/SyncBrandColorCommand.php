<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use App\Services\SiteSettingsService;
use App\Services\ThemeColorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SyncBrandColorCommand extends Command
{
    protected $signature = 'brand:sync-color {hex? : لون hex مثل 387e99 أو #387e99}';

    protected $description = 'تطبيق لون العلامة التجارية في قاعدة البيانات ومسح الكاش';

    public function handle(ThemeColorService $themeColor): int
    {
        $input = $this->argument('hex') ?? ThemeColorService::DEFAULT_ACCENT;
        $hex = $themeColor->normalizeHex((string) $input);

        if (! $hex) {
            $this->error('صيغة اللون غير صالحة. مثال: brand:sync-color 387e99');

            return self::FAILURE;
        }

        SystemSetting::set(
            SiteSettingsService::KEY_SITE_ACCENT_COLOR,
            $hex,
            'color',
            SiteSettingsService::GROUP
        );

        app(SiteSettingsService::class)->clearCache();
        Cache::flush();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        $this->info("تم تعيين لون التمييز إلى {$hex} ومسح الكاش.");
        $this->line('تأكد من رفع ملف public/frontend/assets/css/style.css المحدّث إلى السيرفر.');

        return self::SUCCESS;
    }
}
