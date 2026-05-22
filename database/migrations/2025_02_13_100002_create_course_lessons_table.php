<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['video', 'text', 'quiz']);
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->timestamps();

            $table->index('course_section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
