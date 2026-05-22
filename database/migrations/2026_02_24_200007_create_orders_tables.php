<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });

        DB::table('order_statuses')->insert([
            ['name' => 'قيد الانتظار', 'slug' => 'pending', 'color' => '#ffc107', 'order' => 1, 'is_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'معالجة', 'slug' => 'processing', 'color' => '#17a2b8', 'order' => 2, 'is_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'تم الشحن', 'slug' => 'shipped', 'color' => '#007bff', 'order' => 3, 'is_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مكتمل', 'slug' => 'completed', 'color' => '#28a745', 'order' => 4, 'is_final' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ملغي', 'slug' => 'cancelled', 'color' => '#dc3545', 'order' => 5, 'is_final' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مسترد', 'slug' => 'refunded', 'color' => '#6c757d', 'order' => 6, 'is_final' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('order_status_id')->default(1)->constrained('order_statuses')->onDelete('restrict');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('order_status_id');
            $table->index('created_at');
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('type'); // billing, shipping
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2);
            $table->timestamps();

            $table->index('order_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name');
            $table->string('variant_description')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_statuses');
    }
};
