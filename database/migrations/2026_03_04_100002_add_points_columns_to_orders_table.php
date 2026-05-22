<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('points_redeemed')->default(0)->after('coupon_code');
            $table->decimal('points_discount_amount', 12, 2)->default(0)->after('points_redeemed');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['points_redeemed', 'points_discount_amount']);
        });
    }
};
