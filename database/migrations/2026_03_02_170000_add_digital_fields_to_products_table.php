<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_digital')->default(false)->after('brand_id');
            $table->integer('digital_download_limit')->nullable()->after('is_digital');
            $table->integer('digital_download_expiry_days')->nullable()->after('digital_download_limit');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_digital', 'digital_download_limit', 'digital_download_expiry_days']);
        });
    }
};

