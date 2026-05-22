<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('shipping_zone_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->string('country_code', 2)->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code_pattern')->nullable();
            $table->string('type')->default('country'); // country, state, city, postal
            $table->timestamps();

            $table->index(['country_code', 'state', 'city']);
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // flat_rate, free_shipping, by_weight, by_price
            $table->boolean('is_active')->default(true);
            $table->decimal('base_cost', 12, 2)->default(0);
            $table->decimal('min_cart_total', 12, 2)->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('shipping_method_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained('shipping_methods')->onDelete('cascade');
            $table->string('condition_type'); // weight, subtotal
            $table->decimal('min_value', 12, 3)->default(0);
            $table->decimal('max_value', 12, 3)->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->decimal('per_unit', 12, 2)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_method_rules');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_zone_locations');
        Schema::dropIfExists('shipping_zones');
    }
};

