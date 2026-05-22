<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    private array $productsData = [
        'الإلكترونيات' => [
            ['name' => 'هاتف ذكي سامسونج جالكسي', 'price' => 2499, 'compare_at_price' => 2999, 'short_description' => 'هاتف ذكي بشاشة AMOLED كبيرة وكاميرا عالية الدقة'],
            ['name' => 'لابتوب ابل ماك بوك برو', 'price' => 7999, 'compare_at_price' => 8999, 'short_description' => 'لابتوب احترافي بمعالج M2 وشاشة ريتينا'],
            ['name' => 'سماعات لاسلكية ايربودز', 'price' => 699, 'compare_at_price' => 899, 'short_description' => 'سماعات لاسلكية بتقنية إلغاء الضوضاء'],
            ['name' => 'تلفزيون سامسونج 55 بوصة', 'price' => 3499, 'compare_at_price' => 4299, 'short_description' => 'تلفزيون ذكي بدقة 4K وتقنية QLED'],
            ['name' => 'جهاز لوحي آيباد اير', 'price' => 2199, 'compare_at_price' => 2599, 'short_description' => 'جهاز لوحي خفيف وقوي للاستخدام اليومي'],
            ['name' => 'كاميرا كانون احترافية', 'price' => 4599, 'compare_at_price' => 5299, 'short_description' => 'كاميرا DSLR بدقة 24 ميجابكسل'],
            ['name' => 'ساعة ذكية ابل واتش', 'price' => 1599, 'compare_at_price' => 1899, 'short_description' => 'ساعة ذكية لتتبع الصحة واللياقة'],
            ['name' => 'شاحن لاسلكي سريع', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'شاحن لاسلكي بقدرة 15 واط'],
            ['name' => 'ماوس لاسلكي لوجيتك', 'price' => 249, 'compare_at_price' => 299, 'short_description' => 'ماوس لاسلكي مريح ودقيق'],
            ['name' => 'لوحة مفاتيح ميكانيكية', 'price' => 399, 'compare_at_price' => 499, 'short_description' => 'لوحة مفاتيح ميكانيكية للألعاب'],
        ],
        'الأزياء والموضة' => [
            ['name' => 'فستان سهرة أنيق', 'price' => 899, 'compare_at_price' => 1299, 'short_description' => 'فستان سهرة بتصميم عصري وأنيق'],
            ['name' => 'بدلة رجالية كلاسيكية', 'price' => 1599, 'compare_at_price' => 1999, 'short_description' => 'بدلة رجالية من القماش الفاخر'],
            ['name' => 'حقيبة يد جلدية', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'حقيبة يد من الجلد الطبيعي'],
            ['name' => 'حذاء رياضي نايك', 'price' => 449, 'compare_at_price' => 549, 'short_description' => 'حذاء رياضي مريح للجري'],
            ['name' => 'جاكيت جلد طبيعي', 'price' => 1299, 'compare_at_price' => 1599, 'short_description' => 'جاكيت من الجلد الطبيعي الفاخر'],
            ['name' => 'قميص كتان رجالي', 'price' => 249, 'compare_at_price' => 349, 'short_description' => 'قميص من الكتان الخفيف'],
            ['name' => 'تنورة نسائية مطرزة', 'price' => 349, 'compare_at_price' => 449, 'short_description' => 'تنورة بتطريز يدوي جميل'],
            ['name' => 'وشاح حرير فاخر', 'price' => 199, 'compare_at_price' => 299, 'short_description' => 'وشاح من الحرير الطبيعي'],
            ['name' => 'نظارة شمسية ريبان', 'price' => 699, 'compare_at_price' => 899, 'short_description' => 'نظارة شمسية بحماية UV400'],
            ['name' => 'حزام جلد إيطالي', 'price' => 299, 'compare_at_price' => 399, 'short_description' => 'حزام من الجلد الإيطالي الفاخر'],
        ],
        'المنزل والحديقة' => [
            ['name' => 'طقم كنب مودرن', 'price' => 4999, 'compare_at_price' => 5999, 'short_description' => 'طقم كنب بتصميم عصري ومريح'],
            ['name' => 'طاولة طعام خشبية', 'price' => 1999, 'compare_at_price' => 2499, 'short_description' => 'طاولة طعام من الخشب الصلب'],
            ['name' => 'سرير مزدوج فاخر', 'price' => 3499, 'compare_at_price' => 4299, 'short_description' => 'سرير مزدوج مع مرتبة طبية'],
            ['name' => 'خزانة ملابس كبيرة', 'price' => 2499, 'compare_at_price' => 2999, 'short_description' => 'خزانة ملابس بأبواب منزلقة'],
            ['name' => 'مكتب عمل منزلي', 'price' => 899, 'compare_at_price' => 1199, 'short_description' => 'مكتب عمل مريح ومنظم'],
            ['name' => 'مصباح أرضي مودرن', 'price' => 399, 'compare_at_price' => 499, 'short_description' => 'مصباح أرضي بتصميم عصري'],
            ['name' => 'ستائر قماش فاخرة', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'ستائر من القماش الفاخر'],
            ['name' => 'سجادة يدوية الصنع', 'price' => 1299, 'compare_at_price' => 1599, 'short_description' => 'سجادة مصنوعة يدوياً'],
            ['name' => 'طقم أدوات حديقة', 'price' => 349, 'compare_at_price' => 449, 'short_description' => 'طقم كامل لأدوات الحديقة'],
            ['name' => 'أريكة خارجية مقاومة للماء', 'price' => 1799, 'compare_at_price' => 2199, 'short_description' => 'أريكة خارجية مقاومة للعوامل الجوية'],
        ],
        'الجمال والعناية' => [
            ['name' => 'طقم عناية بالبشرة', 'price' => 299, 'compare_at_price' => 399, 'short_description' => 'طقم متكامل للعناية بالبشرة'],
            ['name' => 'عطر نسائي فاخر', 'price' => 499, 'compare_at_price' => 699, 'short_description' => 'عطر نسائي برائحة مميزة'],
            ['name' => 'مجفف شعر احترافي', 'price' => 349, 'compare_at_price' => 449, 'short_description' => 'مجفف شعر بقوة 2200 واط'],
            ['name' => 'مجموعة مكياج كاملة', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'مجموعة مكياج من أفضل العلامات'],
            ['name' => 'كريم واقي من الشمس', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'كريم واقي SPF50+'],
            ['name' => 'زيت شعر أرجاني', 'price' => 199, 'compare_at_price' => 249, 'short_description' => 'زيت أرجاني طبيعي للشعر'],
            ['name' => 'جهاز تنظيف البشرة', 'price' => 249, 'compare_at_price' => 349, 'short_description' => 'جهاز سيليكون لتنظيف البشرة'],
            ['name' => 'مقص شعر احترافي', 'price' => 179, 'compare_at_price' => 229, 'short_description' => 'مقص شعر من الفولاذ المقاوم'],
            ['name' => 'طقم أظافر جل', 'price' => 129, 'compare_at_price' => 179, 'short_description' => 'طقم كامل لطلاء أظافر جل'],
            ['name' => 'غسول وجه طبيعي', 'price' => 89, 'compare_at_price' => 129, 'short_description' => 'غسول وجه بمكونات طبيعية'],
        ],
        'الرياضة واللياقة' => [
            ['name' => 'جهاز مشي كهربائي', 'price' => 2999, 'compare_at_price' => 3499, 'short_description' => 'جهاز مشي بشاشة رقمية و12 برنامج'],
            ['name' => 'دراجة هوائية جبلية', 'price' => 1799, 'compare_at_price' => 2199, 'short_description' => 'دراجة جبلية بإطارات 26 بوصة'],
            ['name' => 'طقم أثقال منزلي', 'price' => 899, 'compare_at_price' => 1099, 'short_description' => 'طقم أثقال 50 كجم مع حامل'],
            ['name' => 'حقيبة رياضية نايك', 'price' => 199, 'compare_at_price' => 249, 'short_description' => 'حقيبة رياضية متعددة الجيوب'],
            ['name' => 'ساعة رياضية جارسون', 'price' => 1299, 'compare_at_price' => 1599, 'short_description' => 'ساعة رياضية بتتبع GPS'],
            ['name' => 'حصيرة يوغا سميكة', 'price' => 99, 'compare_at_price' => 149, 'short_description' => 'حصيرة يوغا مانعة للانزلاق'],
            ['name' => 'قفازات ملاكمة', 'price' => 199, 'compare_at_price' => 249, 'short_description' => 'قفازات ملاكمة من الجلد'],
            ['name' => 'كرة قدم رسمية', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'كرة قدم معتمدة من FIFA'],
            ['name' => 'نظارة سباحة احترافية', 'price' => 79, 'compare_at_price' => 129, 'short_description' => 'نظارة سباحة مضادة للضباب'],
            ['name' => 'حبل قفز احترافي', 'price' => 49, 'compare_at_price' => 79, 'short_description' => 'حبل قفز بمقابض مريحة'],
        ],
        'الألعاب والترفيه' => [
            ['name' => 'بلايستيشن 5', 'price' => 2199, 'compare_at_price' => 2499, 'short_description' => 'جهاز ألعاب الجيل الجديد'],
            ['name' => 'لعبة ليجو مدينة', 'price' => 399, 'compare_at_price' => 499, 'short_description' => 'مجموعة ليجو لبناء مدينة'],
            ['name' => 'طائرة درون بكاميرا', 'price' => 899, 'compare_at_price' => 1099, 'short_description' => 'طائرة بدون طيار بكاميرا 4K'],
            ['name' => 'لعبة بوard عائلية', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'لعبة عائلية مسلية'],
            ['name' => 'روبوت تعليمي', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'روبوت تعليمي للأطفال'],
            ['name' => 'أحجية 1000 قطعة', 'price' => 79, 'compare_at_price' => 99, 'short_description' => 'أحجية بصور طبيعية'],
            ['name' => 'سيارة تحكم عن بعد', 'price' => 249, 'compare_at_price' => 299, 'short_description' => 'سيارة سباق بتحكم عن بعد'],
            ['name' => 'دمية باربي فاخرة', 'price' => 129, 'compare_at_price' => 179, 'short_description' => 'دمية باربي مع إكسسوارات'],
            ['name' => 'مكعب روبيك ذكي', 'price' => 99, 'compare_at_price' => 129, 'short_description' => 'مكعب روبيك إلكتروني'],
            ['name' => 'جهاز واقع افتراضي', 'price' => 1499, 'compare_at_price' => 1799, 'short_description' => 'نظارة واقع افتراضي للألعاب'],
        ],
        'المطبخ والأدوات' => [
            ['name' => 'خلاط كهربائي متعدد', 'price' => 499, 'compare_at_price' => 599, 'short_description' => 'خلاط متعدد الوظائف 1000 واط'],
            ['name' => 'طقم قدور ستانلس', 'price' => 799, 'compare_at_price' => 999, 'short_description' => 'طقم قدور 10 قطع ستانلس ستيل'],
            ['name' => 'ماكينة قهوة ايطالية', 'price' => 1299, 'compare_at_price' => 1599, 'short_description' => 'ماكينة قهوة إسبريسو أوتوماتيك'],
            ['name' => 'فرن كهربائي ذكي', 'price' => 899, 'compare_at_price' => 1099, 'short_description' => 'فرن كهربائي بـ 8 وظائف'],
            ['name' => 'طقم سكاكين ياباني', 'price' => 399, 'compare_at_price' => 499, 'short_description' => 'طقم سكاكين من الفولاذ الياباني'],
            ['name' => 'محضر طعام فيليبس', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'محضر طعام متعدد الاستخدامات'],
            ['name' => 'محمصة خبز رقمية', 'price' => 199, 'compare_at_price' => 249, 'short_description' => 'محمصة خبز بإعدادات متعددة'],
            ['name' => 'غلاية كهربائية زجاجية', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'غلاية زجاجية بسعة 1.7 لتر'],
            ['name' => 'طقم أطباق سيراميك', 'price' => 349, 'compare_at_price' => 449, 'short_description' => 'طقم أطباق 24 قطعة سيراميك'],
            ['name' => 'مقلاة تيفال غير لاصقة', 'price' => 179, 'compare_at_price' => 229, 'short_description' => 'مقلاة بطلاء غير لاصق'],
        ],
        'الكتب والقرطاسية' => [
            ['name' => 'مجموعة روايات عربية', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'مجموعة من أفضل الروايات العربية'],
            ['name' => 'قاموس أكسفورد المتقدم', 'price' => 99, 'compare_at_price' => 129, 'short_description' => 'قاموس إنجليزي-عربي شامل'],
            ['name' => 'حقيبة ظهر للكتب', 'price' => 199, 'compare_at_price' => 249, 'short_description' => 'حقيبة ظهر مريحة للكتب'],
            ['name' => 'طقم أقلام فاخرة', 'price' => 79, 'compare_at_price' => 99, 'short_description' => 'طقم أقلام حبر فاخرة'],
            ['name' => 'دفتر ملاحظات جلدي', 'price' => 59, 'compare_at_price' => 79, 'short_description' => 'دفتر ملاحظات بغلاف جلدي'],
            ['name' => 'كتاب تطوير الذات', 'price' => 49, 'compare_at_price' => 69, 'short_description' => 'كتاب عن تطوير الذات والنجاح'],
            ['name' => 'موسوعة العلوم المصورة', 'price' => 299, 'compare_at_price' => 399, 'short_description' => 'موسوعة علمية مصورة للأطفال'],
            ['name' => 'حاسبة علمية كاسيو', 'price' => 129, 'compare_at_price' => 159, 'short_description' => 'حاسبة علمية متقدمة'],
            ['name' => 'مجموعة ألوان مائية', 'price' => 89, 'compare_at_price' => 119, 'short_description' => 'مجموعة ألوان مائية 24 لون'],
            ['name' => 'كتاب طبخ عالمي', 'price' => 79, 'compare_at_price' => 99, 'short_description' => 'كتاب وصفات من مطابخ العالم'],
        ],
        'الساعات والمجوهرات' => [
            ['name' => 'ساعة رولكس كلاسيك', 'price' => 15999, 'compare_at_price' => 18999, 'short_description' => 'ساعة كلاسيكية فاخرة'],
            ['name' => 'خاتم ألماس ذهبي', 'price' => 4999, 'compare_at_price' => 5999, 'short_description' => 'خاتم ألماس من الذهب الأبيض'],
            ['name' => 'سلسلة ذهبية نسائية', 'price' => 2499, 'compare_at_price' => 2999, 'short_description' => 'سلسلة ذهبية عيار 18'],
            ['name' => 'ساعة كاسيو رياضية', 'price' => 399, 'compare_at_price' => 499, 'short_description' => 'ساعة رياضية مقاومة للماء'],
            ['name' => 'أقراط لؤلؤ طبيعي', 'price' => 899, 'compare_at_price' => 1099, 'short_description' => 'أقراط من اللؤلؤ الطبيعي'],
            ['name' => 'سوار فضي إيطالي', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'سوار فضة إيطالية عيار 925'],
            ['name' => 'ساعة ذكية سامسونج', 'price' => 1199, 'compare_at_price' => 1399, 'short_description' => 'ساعة ذكية بشاشة AMOLED'],
            ['name' => 'طقم مجوهرات كامل', 'price' => 3499, 'compare_at_price' => 3999, 'short_description' => 'طقم مجوهرات من الذهب والألماس'],
            ['name' => 'بروش ذهبي مرصع', 'price' => 799, 'compare_at_price' => 999, 'short_description' => 'بروش ذهبي مرصع بالأحجار'],
            ['name' => 'ساعة رجالية أوتوماتيك', 'price' => 2999, 'compare_at_price' => 3499, 'short_description' => 'ساعة أوتوماتيك بتصميم كلاسيكي'],
        ],
        'السيارات والدراجات' => [
            ['name' => 'كاميرا سيارة أمامية', 'price' => 299, 'compare_at_price' => 399, 'short_description' => 'كاميرا Dashboard بدقة 1080p'],
            ['name' => 'مضخة هواء محمولة', 'price' => 149, 'compare_at_price' => 199, 'short_description' => 'مضخة هواء كهربائية محمولة'],
            ['name' => 'غطاء مقاعد جلد', 'price' => 499, 'compare_at_price' => 599, 'short_description' => 'غطاء مقاعد من الجلد الصناعي'],
            ['name' => 'جهاز GPS للسيارة', 'price' => 399, 'compare_at_price' => 499, 'short_description' => 'جهاز ملاحة بشاشة 7 بوصة'],
            ['name' => 'منظف سيارة متكامل', 'price' => 79, 'compare_at_price' => 99, 'short_description' => 'طقم تنظيف سيارة متكامل'],
            ['name' => 'شاحن سيارة سريع', 'price' => 49, 'compare_at_price' => 69, 'short_description' => 'شاحن سيارة USB-C سريع'],
            ['name' => 'مرآة كاميرا مزدوجة', 'price' => 599, 'compare_at_price' => 799, 'short_description' => 'مرآة بكاميرا أمامية وخلفية'],
            ['name' => 'حامل هاتف للسيارة', 'price' => 39, 'compare_at_price' => 59, 'short_description' => 'حامل هاتف مغناطيسي للسيارة'],
            ['name' => 'إطار سيارة احتياطي', 'price' => 349, 'compare_at_price' => 449, 'short_description' => 'إطار سيارة بجميع المقاسات'],
            ['name' => 'زيت محرك اصطناعي', 'price' => 129, 'compare_at_price' => 159, 'short_description' => 'زيت محرك اصطناعي 5W-30'],
        ],
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try { Review::truncate(); } catch (\Exception $e) {}
        try { Product::truncate(); } catch (\Exception $e) {}
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = Category::all();
        foreach ($categories as $category) {
            if (isset($this->productsData[$category->name])) {
                foreach ($this->productsData[$category->name] as $productData) {
                    $product = Product::create([
                            'category_id' => $category->id,
                            'name' => $productData['name'],
                            'slug' => Str::slug($productData['name']),
                            'sku' => strtoupper(Str::random(8)),
                            'short_description' => $productData['short_description'],
                            'description' => $productData['short_description'] . '. هذا المنتج يتميز بالجودة العالية والسعر المناسب. مصنوع من أفضل المواد لضمان أفضل أداء. يأتي مع ضمان لمدة سنة كاملة.',
                            'price' => $productData['price'],
                            'compare_at_price' => $productData['compare_at_price'],
                            'is_digital' => true,
                            'status' => 'active',
                            'is_visible' => true,
                            'is_featured' => rand(0, 1),
                            'allow_reviews' => true,
                        'reviews_require_approval' => true,
                        'meta_title' => $productData['name'] . ' - متجرنا',
                        'meta_description' => $productData['short_description'],
                    ]);

                    $this->createReviews($product);
                }
            }
        }
    }

    private function createReviews(Product $product): void
    {
        $users = User::limit(20)->get();
        if ($users->isEmpty()) {
            return;
        }

        $reviewCount = rand(0, 5);
        $usedUsers = [];
        for ($i = 0; $i < $reviewCount; $i++) {
            $user = $users->random();
            if (in_array($user->id, $usedUsers)) {
                continue;
            }
            $usedUsers[] = $user->id;

            Review::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'rating' => rand(3, 5),
                'title' => $this->getRandomReviewTitle(),
                'comment' => $this->getRandomReviewComment(),
                'status' => 'approved',
            ]);
        }
    }

    private function getRandomReviewTitle(): string
    {
        $titles = [
            'منتج ممتاز',
            'جودة عالية جداً',
            'أنصح بشراءه',
            'قيمة مقابل السعر',
            'راضٍ جداً عن الشراء',
            'منتج رائع',
            'يستحق الشراء',
            'جيد جداً',
            'ممتاز',
            'فوق التوقعات',
        ];
        return $titles[array_rand($titles)];
    }

    private function getRandomReviewComment(): string
    {
        $comments = [
            'منتج رائع وجودة ممتازة. أنصح الجميع بشرائه.',
            'جودة عالية وسعر مناسب. التوصيل كان سريع جداً.',
            'أنا راضٍ جداً عن هذا المنتج. يعمل بشكل ممتاز.',
            'منتج يستحق كل ريال. شكراً للمتجر على الخدمة الممتازة.',
            'جودة التصنيع عالية جداً. سأشتري مرة أخرى بالتأكيد.',
            'منتج ممتاز وتوصيل سريع. أنصح بالتعامل مع هذا المتجر.',
            'فوق التوقعات! جودة عالية وتغليف ممتاز.',
            'منتج رائع يلبي جميع احتياجاتي. شكراً لكم.',
        ];
        return $comments[array_rand($comments)];
    }
}
