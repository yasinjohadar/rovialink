<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reviews', 'product_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->onDelete('cascade');
                $table->index('product_id');
            });
        }
        // Add unique constraint for one review per user per product if not exists
        if (!Schema::hasIndex('reviews', 'unique_product_user_review')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique(['product_id', 'user_id'], 'unique_product_user_review');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasIndex('reviews', 'unique_product_user_review')) {
                $table->dropUnique('unique_product_user_review');
            }
            $table->dropForeign(['product_id']);
        });
    }
};
