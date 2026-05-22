<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->string('system_role', 32)->nullable()->after('slug');
        });

        DB::table('order_statuses')->where('slug', 'pending')->update(['system_role' => 'checkout']);
        DB::table('order_statuses')->where('slug', 'refunded')->update(['system_role' => 'return_refund']);
    }

    public function down(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->dropColumn('system_role');
        });
    }
};
