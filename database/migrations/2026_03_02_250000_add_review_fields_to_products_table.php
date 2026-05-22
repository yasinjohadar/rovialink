<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('allow_reviews')->default(true)->after('is_visible')
                ->comment('السماح بالتعليقات والتقييمات لهذا المنتج');
            $table->boolean('reviews_require_approval')->nullable()->after('allow_reviews')
                ->comment('null=افتراضي المتجر، true=بعد الموافقة، false=تلقائي');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['allow_reviews', 'reviews_require_approval']);
        });
    }
};
