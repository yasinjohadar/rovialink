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
        // Drop Zoom tables first (due to foreign keys)
        Schema::dropIfExists('zoom_attendance');
        Schema::dropIfExists('zoom_lectures');
        Schema::dropIfExists('zoom_accounts');
        
        // Drop Certificate tables
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('certificate_templates');
        
        // Drop Course Enrollment tables
        Schema::dropIfExists('course_enrollments');
        
        // Drop Course Resource tables
        Schema::dropIfExists('course_resources');
        
        // Drop Course Lesson tables
        Schema::dropIfExists('course_lessons');
        
        // Drop Course Section tables
        Schema::dropIfExists('course_sections');
        
        // Drop Course tables
        Schema::dropIfExists('courses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is one-way only
        // Tables would need to be recreated manually if needed
    }
};