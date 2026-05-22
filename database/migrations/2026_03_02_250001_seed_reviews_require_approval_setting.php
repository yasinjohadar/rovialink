<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('system_settings')->where('key', 'reviews_require_approval')->exists();
        if (!$exists) {
            DB::table('system_settings')->insert([
                'key' => 'reviews_require_approval',
                'value' => '1',
                'type' => 'string',
                'group' => 'reviews',
                'description' => 'التعليقات تحتاج موافقة الإدارة قبل النشر (1=نعم، 0=نشر تلقائي)',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('system_settings')->where('key', 'reviews_require_approval')->delete();
    }
};
