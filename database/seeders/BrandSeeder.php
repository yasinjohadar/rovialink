<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * شعارات تجريبية للصفحة الرئيسية (روابط خارجية — يمكن استبدالها برفع صور من لوحة التحكم).
     */
    public function run(): void
    {
        $accent = '387e99';

        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'order' => 1, 'icon' => 'apple'],
            ['name' => 'Microsoft', 'slug' => 'microsoft', 'order' => 2, 'icon' => 'microsoft'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'order' => 3, 'icon' => 'samsung'],
            ['name' => 'Sony', 'slug' => 'sony', 'order' => 4, 'icon' => 'sony'],
            ['name' => 'Nike', 'slug' => 'nike', 'order' => 5, 'icon' => 'nike'],
            ['name' => 'Adobe', 'slug' => 'adobe', 'order' => 6, 'icon' => 'adobe'],
            ['name' => 'Google', 'slug' => 'google', 'order' => 7, 'icon' => 'google'],
            ['name' => 'Intel', 'slug' => 'intel', 'order' => 8, 'icon' => 'intel'],
        ];

        foreach ($brands as $data) {
            Brand::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'order' => $data['order'],
                    'show_on_homepage' => true,
                    'image' => 'https://cdn.simpleicons.org/'.$data['icon'].'/'.$accent,
                ]
            );
        }
    }
}
