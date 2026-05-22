<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('product_file_id')->constrained('product_files')->onDelete('cascade');
            $table->string('download_token')->unique();
            $table->integer('remaining_downloads')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();

            $table->index('download_token');
            $table->index(['order_id', 'order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_downloads');
    }
};

