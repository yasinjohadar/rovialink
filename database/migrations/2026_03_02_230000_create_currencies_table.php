<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('symbol', 20)->nullable();
            $table->decimal('rate_to_default', 18, 6)->default(1)->comment('Units of default currency per 1 unit of this currency');
            $table->boolean('is_default')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_default');
            $table->index('is_active');
        });

        DB::table('currencies')->insert([
            'code' => 'SAR',
            'name' => 'ريال سعودي',
            'symbol' => 'ر.س',
            'rate_to_default' => 1,
            'is_default' => true,
            'order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
