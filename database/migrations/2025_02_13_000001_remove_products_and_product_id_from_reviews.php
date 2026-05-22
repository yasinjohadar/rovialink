<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove product_id from reviews, then drop all product-related tables.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Check if the unique constraint exists before dropping it
            if (Schema::hasIndex('reviews', 'unique_product_user_review')) {
                $table->dropUnique('unique_product_user_review');
            }
            
            // Check if the foreign key exists before dropping it
            if (Schema::hasColumn('reviews', 'product_id')) {
                $table->dropColumn('product_id');
            }
        });

        // Try to drop product tables if they exist
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreating products tables would require copying original migrations.
        // This migration is intended as a one-way removal.
    }
};
