<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('select'); // select, color, image
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index('slug');
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->string('value');
            $table->string('slug')->nullable();
            $table->string('color_hex')->nullable(); // for color type
            $table->string('image')->nullable(); // for image type
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['product_attribute_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
