<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Temporarily removed product_id constraint as products table doesn't exist
            // Will be properly added when products are implemented
            $table->unsignedBigInteger('product_id')->nullable()->comment('Temporary field - will be removed later');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('rating')->unsigned()->comment('التقييم من 1 إلى 5');
            $table->string('title')->nullable()->comment('عنوان الرأي');
            $table->text('comment')->nullable()->comment('نص التعليق');
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('pending');
            $table->boolean('is_verified_purchase')->default(false)->comment('شراء موثق');
            $table->integer('helpful_count')->default(0)->comment('عدد من وجدوه مفيداً');
            $table->integer('not_helpful_count')->default(0)->comment('عدد من لم يجدوه مفيداً');
            $table->json('images')->nullable()->comment('صور مرفقة');
            $table->text('admin_response')->nullable()->comment('رد الإدارة');
            $table->timestamp('admin_response_at')->nullable();
            $table->foreignId('admin_response_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_featured')->default(false)->comment('رأي مميز');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('product_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('rating');
            $table->index('is_featured');
            $table->index('is_verified_purchase');
            
            // منع التكرار: نفس المستخدم لا يمكنه تقييم نفس المنتج مرتين
            // Temporarily removed unique constraint due to missing products table
            // Will be properly added when products are implemented
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
