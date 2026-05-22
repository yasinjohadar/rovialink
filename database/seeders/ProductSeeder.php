<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * @var array<string, list<array{name: string, slug: string, price: float, compare_at_price: float, short_description: string}>>
     */
    private array $productsData = [
        'مفاتيح ويندوز' => [
            ['name' => 'Windows 11 Pro — مفتاح تفعيل أصلي', 'slug' => 'windows-11-pro-key', 'price' => 89, 'compare_at_price' => 129, 'short_description' => 'مفتاح Windows 11 Pro رقمي مع تسليم فوري عبر البريد'],
            ['name' => 'Windows 10 Pro — ترخيص مدى الحياة', 'slug' => 'windows-10-pro-key', 'price' => 69, 'compare_at_price' => 99, 'short_description' => 'تفعيل Windows 10 Pro لجهاز واحد'],
            ['name' => 'Windows 11 Home — مفتاح رقمي', 'slug' => 'windows-11-home-key', 'price' => 59, 'compare_at_price' => 89, 'short_description' => 'مناسب للاستخدام المنزلي والدراسة'],
            ['name' => 'ترقية Windows 10 إلى 11 Pro', 'slug' => 'windows-10-to-11-upgrade', 'price' => 79, 'compare_at_price' => 119, 'short_description' => 'مفتاح ترقية رسمي من Home/Pro إلى Windows 11 Pro'],
            ['name' => 'Windows Server 2022 Standard', 'slug' => 'windows-server-2022-key', 'price' => 299, 'compare_at_price' => 449, 'short_description' => 'ترخيص سيرفر للشركات والمشاريع'],
            ['name' => 'حزمة Office 2021 + Windows 11 Pro', 'slug' => 'windows-office-bundle', 'price' => 149, 'compare_at_price' => 219, 'short_description' => 'باقة ويندوز وأوفيس بسعر مخفّض'],
        ],
        'سيريالات البرامج' => [
            ['name' => 'Adobe Creative Cloud — سنة كاملة', 'slug' => 'adobe-cc-1year', 'price' => 399, 'compare_at_price' => 599, 'short_description' => 'Photoshop وIllustrator وPremiere ضمن اشتراك سنوي'],
            ['name' => 'Microsoft Office 2021 Professional Plus', 'slug' => 'office-2021-pro-plus', 'price' => 99, 'compare_at_price' => 159, 'short_description' => 'Word وExcel وPowerPoint وOutlook مدى الحياة'],
            ['name' => 'IDM — Internet Download Manager', 'slug' => 'idm-lifetime-license', 'price' => 29, 'compare_at_price' => 49, 'short_description' => 'تسريع التحميل مع ترخيص دائم'],
            ['name' => 'WinRAR — ترخيص مدى الحياة', 'slug' => 'winrar-lifetime', 'price' => 19, 'compare_at_price' => 35, 'short_description' => 'ضغط وفك ضغط الملفات بدون قيود'],
            ['name' => 'VMware Workstation Pro 17', 'slug' => 'vmware-workstation-17', 'price' => 79, 'compare_at_price' => 129, 'short_description' => 'بيئات افتراضية للمطورين والاختبار'],
            ['name' => 'AutoCAD 2024 — تفعيل سنوي', 'slug' => 'autocad-2024-license', 'price' => 249, 'compare_at_price' => 399, 'short_description' => 'برنامج التصميم الهندسي للمهندسين والمعماريين'],
        ],
        'قوالب ووردبريس' => [
            ['name' => 'قالب متجر WooCommerce احترافي', 'slug' => 'wp-woocommerce-store-theme', 'price' => 129, 'compare_at_price' => 199, 'short_description' => 'قالب عربي متجاوب مع دفع وسلة متكاملة'],
            ['name' => 'قالب شركة تقنية RTL', 'slug' => 'wp-tech-company-theme', 'price' => 89, 'compare_at_price' => 139, 'short_description' => 'صفحات خدمات وفريق وعملاء مع Elementor'],
            ['name' => 'قالب مدونة SEO سريع', 'slug' => 'wp-seo-blog-theme', 'price' => 59, 'compare_at_price' => 99, 'short_description' => 'محسّن للسرعة ومحركات البحث'],
            ['name' => 'قالب مطعم وتوصيل', 'slug' => 'wp-restaurant-delivery-theme', 'price' => 99, 'compare_at_price' => 149, 'short_description' => 'قائمة طعام وطلبات أونلاين'],
            ['name' => 'قالب عيادة طبية', 'slug' => 'wp-medical-clinic-theme', 'price' => 79, 'compare_at_price' => 119, 'short_description' => 'حجوزات وملفات مرضى وصفحات أطباء'],
            ['name' => 'قالب تعليمي LMS', 'slug' => 'wp-lms-education-theme', 'price' => 149, 'compare_at_price' => 229, 'short_description' => 'دورات وفيديو واختبارات مع LearnPress'],
        ],
        'إضافات ووردبريس' => [
            ['name' => 'Elementor Pro — ترخيص سنة', 'slug' => 'elementor-pro-1year', 'price' => 79, 'compare_at_price' => 129, 'short_description' => 'بناء صفحات بالسحب والإفلات بدون كود'],
            ['name' => 'Yoast SEO Premium', 'slug' => 'yoast-seo-premium', 'price' => 49, 'compare_at_price' => 89, 'short_description' => 'تحسين محركات البحث وتحليل المحتوى'],
            ['name' => 'WP Rocket — تسريع الموقع', 'slug' => 'wp-rocket-cache', 'price' => 39, 'compare_at_price' => 69, 'short_description' => 'كاش وتحسين Core Web Vitals'],
            ['name' => 'WooCommerce PDF Invoices Pro', 'slug' => 'wc-pdf-invoices-pro', 'price' => 29, 'compare_at_price' => 49, 'short_description' => 'فواتير عربية تلقائية للطلبات'],
            ['name' => 'Wordfence Premium — أمان', 'slug' => 'wordfence-premium', 'price' => 59, 'compare_at_price' => 99, 'short_description' => 'جدار ناري وفحص ملفات ومراقبة'],
            ['name' => 'TranslatePress Multilingual', 'slug' => 'translatepress-pro', 'price' => 45, 'compare_at_price' => 79, 'short_description' => 'موقع متعدد اللغات بما فيها العربية'],
        ],
        'اشتراكات رقمية' => [
            ['name' => 'Netflix Premium — شهر', 'slug' => 'netflix-premium-1month', 'price' => 35, 'compare_at_price' => 55, 'short_description' => 'حساب Premium بجودة 4K'],
            ['name' => 'Microsoft 365 Family — سنة', 'slug' => 'microsoft-365-family-year', 'price' => 199, 'compare_at_price' => 299, 'short_description' => 'OneDrive وOffice لـ 6 مستخدمين'],
            ['name' => 'Spotify Premium — 3 أشهر', 'slug' => 'spotify-premium-3months', 'price' => 45, 'compare_at_price' => 75, 'short_description' => 'استماع بدون إعلانات وتحميل'],
            ['name' => 'YouTube Premium — شهر', 'slug' => 'youtube-premium-1month', 'price' => 25, 'compare_at_price' => 40, 'short_description' => 'بدون إعلانات وتشغيل بالخلفية'],
            ['name' => 'Canva Pro — سنة', 'slug' => 'canva-pro-1year', 'price' => 89, 'compare_at_price' => 149, 'short_description' => 'قوالب وميزات Pro للتصميم'],
            ['name' => 'ChatGPT Plus — شهر', 'slug' => 'chatgpt-plus-1month', 'price' => 55, 'compare_at_price' => 85, 'short_description' => 'وصول GPT-4 وأدوات متقدمة'],
        ],
        'حسابات رقمية' => [
            ['name' => 'حساب Adobe Stock — 500 صورة', 'slug' => 'adobe-stock-500', 'price' => 69, 'compare_at_price' => 109, 'short_description' => 'تحميل صور عالية الجودة للمشاريع'],
            ['name' => 'حساب Envato Elements — شهر', 'slug' => 'envato-elements-1month', 'price' => 39, 'compare_at_price' => 65, 'short_description' => 'قوالب وفيديوهات وخطوط بلا حدود'],
            ['name' => 'حساب Freepik Premium — شهر', 'slug' => 'freepik-premium-1month', 'price' => 29, 'compare_at_price' => 49, 'short_description' => 'vectors وPSD وصور للمصممين'],
            ['name' => 'حساب Midjourney Pro — شهر', 'slug' => 'midjourney-pro-1month', 'price' => 79, 'compare_at_price' => 120, 'short_description' => 'توليد صور بالذكاء الاصطناعي'],
            ['name' => 'حساب Grammarly Premium — سنة', 'slug' => 'grammarly-premium-year', 'price' => 99, 'compare_at_price' => 159, 'short_description' => 'تدقيق لغوي واحترافي للنصوص'],
            ['name' => 'حساب Notion Plus — سنة', 'slug' => 'notion-plus-year', 'price' => 59, 'compare_at_price' => 96, 'short_description' => 'تنظيم مشاريع وملاحظات فرق العمل'],
        ],
        'قوالب تصميم' => [
            ['name' => 'باقة سوشيال ميديا — 500 قالب Canva', 'slug' => 'canva-social-pack-500', 'price' => 49, 'compare_at_price' => 89, 'short_description' => 'منشورات ستوري وريلز جاهزة للتعديل'],
            ['name' => 'هوية بصرية كاملة PSD', 'slug' => 'brand-identity-psd-kit', 'price' => 79, 'compare_at_price' => 129, 'short_description' => 'شعار وبطاقة وورق مراسلات'],
            ['name' => 'قوالب عروض تقديمية PowerPoint', 'slug' => 'powerpoint-pitch-deck', 'price' => 39, 'compare_at_price' => 69, 'short_description' => '30 شريحة احترافية للشركات الناشئة'],
            ['name' => 'مجموعة Mockup للمنتجات الرقمية', 'slug' => 'digital-product-mockups', 'price' => 59, 'compare_at_price' => 99, 'short_description' => 'عرض شاشات ولابتوب وموبايل'],
            ['name' => 'قوالب Figma لمتاجر إلكترونية', 'slug' => 'figma-ecommerce-ui-kit', 'price' => 69, 'compare_at_price' => 119, 'short_description' => 'UI kit متكامل للتطبيق والويب'],
            ['name' => 'بطاقات تهنئة وأعياد — Canva', 'slug' => 'canva-greeting-cards', 'price' => 25, 'compare_at_price' => 45, 'short_description' => 'تصاميم عربية للمناسبات'],
        ],
        'دورات إلكترونية' => [
            ['name' => 'كورس Laravel من الصفر للاحتراف', 'slug' => 'course-laravel-full', 'price' => 149, 'compare_at_price' => 299, 'short_description' => '40+ ساعة مشاريع عملية وشهادة'],
            ['name' => 'كورس تسويق إلكتروني متقدم', 'slug' => 'course-digital-marketing', 'price' => 99, 'compare_at_price' => 199, 'short_description' => 'إعلانات وSEO وتحويلات'],
            ['name' => 'كورس WordPress + WooCommerce', 'slug' => 'course-wordpress-woocommerce', 'price' => 79, 'compare_at_price' => 159, 'short_description' => 'بناء متجر كامل من الصفر'],
            ['name' => 'كورس UI/UX بتطبيق Figma', 'slug' => 'course-figma-uiux', 'price' => 89, 'compare_at_price' => 179, 'short_description' => 'تصميم واجهات وتجربة مستخدم'],
            ['name' => 'كورس Python للذكاء الاصطناعي', 'slug' => 'course-python-ai', 'price' => 129, 'compare_at_price' => 249, 'short_description' => 'تعلم آلي ونماذج جاهزة'],
            ['name' => 'كورس أمن سيبراني للمبتدئين', 'slug' => 'course-cybersecurity-basics', 'price' => 69, 'compare_at_price' => 139, 'short_description' => 'حماية شبكات وتطبيقات'],
        ],
        'ألعاب ومنصات' => [
            ['name' => 'بطاقة Steam 50 USD', 'slug' => 'steam-wallet-50usd', 'price' => 185, 'compare_at_price' => 210, 'short_description' => 'رصيد محفظة Steam فوري'],
            ['name' => 'بطاقة PlayStation 100 SAR', 'slug' => 'psn-card-100sar', 'price' => 100, 'compare_at_price' => 100, 'short_description' => 'شحن محفظة PSN السعودية'],
            ['name' => 'Xbox Game Pass Ultimate — 3 أشهر', 'slug' => 'xbox-game-pass-3m', 'price' => 89, 'compare_at_price' => 135, 'short_description' => 'مئات الألعاب + Online'],
            ['name' => 'Minecraft Java Edition — مفتاح', 'slug' => 'minecraft-java-key', 'price' => 79, 'compare_at_price' => 109, 'short_description' => 'نسخة Java الأصلية للكمبيوتر'],
            ['name' => 'EA Sports FC 25 — PC', 'slug' => 'ea-fc-25-pc-key', 'price' => 149, 'compare_at_price' => 229, 'short_description' => 'تفعيل Origin/EA App'],
            ['name' => 'Roblox 800 Robux — كود', 'slug' => 'roblox-800-robux', 'price' => 45, 'compare_at_price' => 65, 'short_description' => 'شحن سريع لحساب Roblox'],
        ],
        'أمن وحماية' => [
            ['name' => 'Kaspersky Total Security — سنة', 'slug' => 'kaspersky-total-1year', 'price' => 49, 'compare_at_price' => 89, 'short_description' => 'حماية 3 أجهزة + VPN'],
            ['name' => 'Norton 360 Deluxe — سنة', 'slug' => 'norton-360-deluxe', 'price' => 59, 'compare_at_price' => 99, 'short_description' => 'أمان ونسخ احتياطي سحابي'],
            ['name' => 'Malwarebytes Premium — سنة', 'slug' => 'malwarebytes-premium-year', 'price' => 39, 'compare_at_price' => 69, 'short_description' => 'إزالة البرمجيات الخبيثة'],
            ['name' => 'ESET NOD32 Antivirus', 'slug' => 'eset-nod32-1year', 'price' => 35, 'compare_at_price' => 59, 'short_description' => 'خفيف وسريع للحواسيب'],
            ['name' => 'NordVPN — سنتان', 'slug' => 'nordvpn-2years', 'price' => 89, 'compare_at_price' => 149, 'short_description' => 'تصفح آمن وسيرفرات عالمية'],
            ['name' => 'Bitdefender Internet Security', 'slug' => 'bitdefender-internet-security', 'price' => 45, 'compare_at_price' => 79, 'short_description' => 'جدار ناري ومراقبة تسوق'],
        ],
    ];

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
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = Category::all()->keyBy('name');

        foreach ($this->productsData as $categoryName => $products) {
            $category = $categories->get($categoryName);
            if (! $category) {
                $this->command?->warn("تصنيف غير موجود: {$categoryName}");

                continue;
            }

            foreach ($products as $index => $productData) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'sku' => 'DIG-' . $category->slug . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'short_description' => $productData['short_description'],
                    'description' => $productData['short_description'] . ' يتم التسليم فور إتمام الدفع عبر البريد الإلكتروني أو لوحة حسابك. المنتج رقمي 100% — لا شحن ولا انتظار. يشمل دعم فني لمدة 30 يوماً عند الحاجة.',
                    'price' => $productData['price'],
                    'compare_at_price' => $productData['compare_at_price'],
                    'is_digital' => true,
                    'digital_download_limit' => 5,
                    'digital_download_expiry_days' => 365,
                    'status' => 'active',
                    'is_visible' => true,
                    'is_featured' => $index < 2,
                    'allow_reviews' => true,
                    'reviews_require_approval' => true,
                    'meta_title' => $productData['name'] . ' - روفيا لينك',
                    'meta_description' => $productData['short_description'],
                ]);

                $this->createReviews($product);
            }
        }

        $total = Product::count();
        $this->command?->info("تم إنشاء {$total} منتجاً رقمياً.");
    }

    private function createReviews(Product $product): void
    {
        $users = User::limit(20)->get();
        if ($users->isEmpty()) {
            return;
        }

        $reviewCount = rand(1, 4);
        $usedUsers = [];
        for ($i = 0; $i < $reviewCount; $i++) {
            $user = $users->random();
            if (in_array($user->id, $usedUsers, true)) {
                continue;
            }
            $usedUsers[] = $user->id;

            Review::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'rating' => rand(4, 5),
                'title' => $this->getRandomReviewTitle(),
                'comment' => $this->getRandomReviewComment(),
                'status' => 'approved',
            ]);
        }
    }

    private function getRandomReviewTitle(): string
    {
        $titles = [
            'تسليم فوري',
            'الكود اشتغل مباشرة',
            'منتج رقمي ممتاز',
            'خدمة سريعة',
            'أنصح به',
            'تجربة ممتازة',
            'وفر وقتي',
            'موثوق 100%',
        ];

        return $titles[array_rand($titles)];
    }

    private function getRandomReviewComment(): string
    {
        $comments = [
            'وصلني الكود خلال دقائق والتفعيل تم بدون مشاكل.',
            'منتج رقمي أصلي والتسليم فوري عبر البريد.',
            'الدعم ساعدني في التفعيل بسرعة. شكراً روفيا لينك.',
            'سعر ممتاز مقارنة بالمواقع الأخرى.',
            'اشتريت مفتاح ويندوز واشتغل من أول مرة.',
            'قالب ووردبريس احترافي وسهل التخصيص.',
            'اشتراك رقمي فعّال والحساب يعمل بشكل ممتاز.',
        ];

        return $comments[array_rand($comments)];
    }
}
