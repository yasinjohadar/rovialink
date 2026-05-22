<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('event');
            $table->string('locale', 5)->default('ar');
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['event', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

