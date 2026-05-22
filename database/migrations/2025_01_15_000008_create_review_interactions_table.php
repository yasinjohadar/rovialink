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
        Schema::create('review_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['helpful', 'not_helpful', 'report'])->comment('نوع التفاعل');
            $table->timestamps();
            
            // Indexes
            $table->index('review_id');
            $table->index('user_id');
            $table->index('type');
            
            // منع التكرار: نفس المستخدم لا يمكنه التفاعل مع نفس الرأي بنفس الطريقة مرتين
            $table->unique(['review_id', 'user_id', 'type'], 'unique_review_user_interaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_interactions');
    }
};
