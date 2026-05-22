<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->onDelete('cascade');
            $table->string('title');
            $table->string('type', 20); // 'link' | 'file'
            $table->string('url', 500)->nullable();
            $table->string('link_display', 20)->default('new_tab'); // 'embed' | 'new_tab'
            $table->string('file_path', 500)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index('course_section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_resources');
    }
};
