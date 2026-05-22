<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الإلكترونيات',
                'description' => 'أحدث الأجهزة الإلكترونية والتقنية',
                'order' => 1,
            ],
            [
                'name' => 'الأزياء والموضة',
                'description' => 'أحدث صيحات الموضة والأزياء',
                'order' => 2,
            ],
            [
                'name' => 'المنزل والحديقة',
                'description' => 'كل ما يحتاجه منزلك وحديقتك',
                'order' => 3,
            ],
            [
                'name' => 'الجمال والعناية',
                'description' => 'منتجات العناية والجمال',
                'order' => 4,
            ],
            [
                'name' => 'الرياضة واللياقة',
                'description' => 'معدات ومستلزمات رياضية',
                'order' => 5,
            ],
            [
                'name' => 'الألعاب والترفيه',
                'description' => 'ألعاب للأطفال والكبار',
                'order' => 6,
            ],
            [
                'name' => 'المطبخ والأدوات',
                'description' => 'أدوات ومستلزمات المطبخ',
                'order' => 7,
            ],
            [
                'name' => 'الكتب والقرطاسية',
                'description' => 'كتب وأدوات مكتبية',
                'order' => 8,
            ],
            [
                'name' => 'الساعات والمجوهرات',
                'description' => 'ساعات ومجوهرات فاخرة',
                'order' => 9,
            ],
            [
                'name' => 'السيارات والدراجات',
                'description' => 'مستلزمات السيارات والدراجات',
                'order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'status' => 'active',
                'order' => $category['order'],
                'meta_title' => $category['name'] . ' - متجرنا',
                'meta_description' => $category['description'],
            ]);
        }
    }
}
