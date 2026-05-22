<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->string('path');
            $table->boolean('downloadable')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'downloadable', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_files');
    }
};

