<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');
        });

        Schema::create('shopping_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_cart_id')->constrained('shopping_carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['shopping_cart_id', 'product_id', 'product_variant_id'], 'cart_product_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_cart_items');
        Schema::dropIfExists('shopping_carts');
    }
};
