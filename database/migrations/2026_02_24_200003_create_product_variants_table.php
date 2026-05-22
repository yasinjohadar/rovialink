<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_variants');

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->nullable();
            $table->decimal('price', 12, 2)->nullable(); // override product price
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('weight')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'sku']);
            $table->index('product_id');
        });

        Schema::create('product_variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('product_attribute_value_id');
            $table->timestamps();

            $table->foreign('product_variant_id', 'pva_product_variant_fk')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('product_attribute_value_id', 'pva_attr_value_fk')->references('id')->on('product_attribute_values')->onDelete('cascade');
            $table->unique(['product_variant_id', 'product_attribute_value_id'], 'variant_attr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_variants');
    }
};
