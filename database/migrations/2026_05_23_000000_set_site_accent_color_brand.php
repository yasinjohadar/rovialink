<?php

use App\Services\SiteSettingsService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $key = SiteSettingsService::KEY_SITE_ACCENT_COLOR;
        $value = '#387e99';

        $exists = DB::table('system_settings')->where('key', $key)->exists();

        if ($exists) {
            DB::table('system_settings')->where('key', $key)->update([
                'value' => $value,
                'type' => 'color',
                'group' => 'site',
                'updated_at' => now(),
            ]);
        } else {
            DB::table('system_settings')->insert([
                'key' => $key,
                'value' => $value,
                'type' => 'color',
                'group' => 'site',
                'description' => 'لون التمييز للواجهة الأمامية',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('site_settings');
    }

    public function down(): void
    {
        DB::table('system_settings')
            ->where('key', SiteSettingsService::KEY_SITE_ACCENT_COLOR)
            ->update([
                'value' => '#00D8E4',
                'updated_at' => now(),
            ]);

        Cache::forget('site_settings');
    }
};
