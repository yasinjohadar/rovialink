<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coupons')) {
            return;
        }

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount', 'buy_x_get_y']);
            $table->decimal('value', 12, 2)->default(0);
            $table->decimal('minimum_order_amount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable()->default(1);
            $table->integer('usage_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('code');
            $table->index('status');
            $table->index('expires_at');
        });

        DB::table('coupons')->insert([
            ['code' => 'WELCOME10', 'name' => 'خصم ترحيبي', 'description' => 'خصم 10% على طلباتك الأولى', 'type' => 'percentage', 'value' => 10, 'minimum_order_amount' => 100, 'usage_limit' => null, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SAVE20', 'name' => 'خصم 20%', 'description' => 'خصم 20% على جميع المنتجات', 'type' => 'percentage', 'value' => 20, 'minimum_order_amount' => 200, 'usage_limit' => null, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'NEWUSER', 'name' => 'خصم 15% لعملاء جدد', 'description' => 'خصم 15% لأول 10 طلبات', 'type' => 'percentage', 'value' => 15, 'minimum_order_amount' => 50, 'usage_limit' => 1, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SUMMER50', 'name' => 'خصم 50%', 'description' => 'خصم 50% على طلبات فوق 100 ريال', 'type' => 'percentage', 'value' => 50, 'minimum_order_amount' => 100, 'usage_limit' => 3, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
