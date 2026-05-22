<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_class_id')->constrained('tax_classes')->onDelete('cascade');
            $table->string('name');
            $table->decimal('rate', 8, 4); // e.g. 0.2000 for 20%
            $table->string('country_code', 2)->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code_pattern')->nullable();
            $table->boolean('is_compound')->default(false);
            $table->boolean('is_inclusive')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['country_code', 'state', 'city']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tax_class_id')->nullable()->after('brand_id')->constrained('tax_classes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_class_id');
        });

        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('tax_classes');
    }
};

