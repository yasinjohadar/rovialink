<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['name' => 'الأجهزة الإلكترونية', 'description' => 'جميع أنواع الأجهزة الإلكترونية والرقمية', 'status' => 'active', 'order' => 1],
            ['name' => 'الملابس والأزياء', 'description' => 'أحدث صيحات الموضة والملابس', 'status' => 'active', 'order' => 2],
            ['name' => 'الأثاث والديكور', 'description' => 'قطع الأثاث المنزلي والديكور', 'status' => 'active', 'order' => 3],
            ['name' => 'المنتجات الرياضية', 'description' => 'المعدات والملابس الرياضية', 'status' => 'active', 'order' => 4],
            ['name' => 'الكتب والمجلات', 'description' => 'مجموعة متنوعة من الكتب والمجلات', 'status' => 'active', 'order' => 5],
            ['name' => 'المنتجات الصحية', 'description' => 'منتجات العناية الصحية والطبية', 'status' => 'active', 'order' => 6],
            ['name' => 'المنتجات الغذائية', 'description' => 'الأطعمة والمشروبات المختلفة', 'status' => 'active', 'order' => 7],
            ['name' => 'الألعاب والهدايا', 'description' => 'ألعاب الأطفال والهدايا', 'status' => 'active', 'order' => 8],
            ['name' => 'الإكسسوارات', 'description' => 'إكسسوارات متنوعة للرجال والنساء', 'status' => 'active', 'order' => 9],
            ['name' => 'المنتجات المنزلية', 'description' => 'الأدوات والمستلزمات المنزلية', 'status' => 'active', 'order' => 10],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'status' => $categoryData['status'],
                'order' => $categoryData['order'],
                'meta_title' => $categoryData['name'],
                'meta_description' => $categoryData['description'],
            ]);
            $this->command->info("تم إنشاء التصنيف: {$categoryData['name']}");
        }

        $this->command->info('تم إنشاء ' . count($categories) . ' تصنيف بنجاح!');
    }
}
