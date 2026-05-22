<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            Review::truncate();
        } catch (\Exception $e) {
        }
        try {
            Product::truncate();
        } catch (\Exception $e) {
        }
        try {
            Category::truncate();
        } catch (\Exception $e) {
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'name' => 'مفاتيح ويندوز',
                'slug' => 'windows-keys',
                'description' => 'مفاتيح تفعيل أصلية لجميع إصدارات Windows مع تسليم فوري',
                'order' => 1,
            ],
            [
                'name' => 'سيريالات البرامج',
                'slug' => 'software-serials',
                'description' => 'أكواد تفعيل للبرامج والأدوات الاحترافية',
                'order' => 2,
            ],
            [
                'name' => 'قوالب ووردبريس',
                'slug' => 'wordpress-themes',
                'description' => 'قوالب ووردبريس جاهزة للمتاجر والشركات والمدونات',
                'order' => 3,
            ],
            [
                'name' => 'إضافات ووردبريس',
                'slug' => 'wordpress-plugins',
                'description' => 'إضافات Premium لتحسين الأداء والأمان والمتاجر',
                'order' => 4,
            ],
            [
                'name' => 'اشتراكات رقمية',
                'slug' => 'digital-subscriptions',
                'description' => 'اشتراكات Netflix وOffice وAdobe وخدمات سحابية',
                'order' => 5,
            ],
            [
                'name' => 'حسابات رقمية',
                'slug' => 'digital-accounts',
                'description' => 'حسابات جاهزة لمنصات التصميم والذكاء الاصطناعي والألعاب',
                'order' => 6,
            ],
            [
                'name' => 'قوالب تصميم',
                'slug' => 'design-templates',
                'description' => 'قوالب Canva وPSD وFigma للسوشيال والهوية البصرية',
                'order' => 7,
            ],
            [
                'name' => 'دورات إلكترونية',
                'slug' => 'online-courses',
                'description' => 'كورسات برمجة وتسويق وتصميم بصيغة رقمية',
                'order' => 8,
            ],
            [
                'name' => 'ألعاب ومنصات',
                'slug' => 'games-platforms',
                'description' => 'أكواد Steam وXbox وPlayStation وبطاقات شحن',
                'order' => 9,
            ],
            [
                'name' => 'أمن وحماية',
                'slug' => 'security-software',
                'description' => 'مفاتيح Kaspersky وNorton وMalwarebytes وVPN',
                'order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
                'status' => 'active',
                'order' => $category['order'],
                'meta_title' => $category['name'] . ' - روفيا لينك',
                'meta_description' => $category['description'],
            ]);
        }

        $this->command?->info('تم إنشاء ' . count($categories) . ' تصنيف للمنتجات الرقمية.');
    }
}
