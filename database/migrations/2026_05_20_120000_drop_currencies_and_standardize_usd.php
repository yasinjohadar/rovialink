<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            DB::table('orders')->update(['currency' => 'USD']);
        }

        if (Schema::hasTable('payments')) {
            DB::table('payments')->update(['currency' => 'USD']);
        }

        if (Schema::hasTable('system_settings')) {
            DB::table('system_settings')
                ->where('key', 'payment_default_currency')
                ->where('group', 'payments')
                ->update(['value' => 'USD']);
        }

        Schema::dropIfExists('currencies');
    }

    public function down(): void
    {
        if (! Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('name');
                $table->string('symbol', 20)->nullable();
                $table->decimal('rate_to_default', 18, 6)->default(1);
                $table->boolean('is_default')->default(false);
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            DB::table('currencies')->insert([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'rate_to_default' => 1,
                'is_default' => true,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
