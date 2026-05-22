<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'تسوق إلكتروني', 'slug' => 'ecommerce', 'description' => 'نصائح وأدلة التسوق عبر الإنترنت', 'is_active' => true, 'is_featured' => true, 'order' => 1],
            ['name' => 'تطوير الويب', 'slug' => 'web-development', 'description' => 'مقالات حول تطوير الويب والتقنيات الحديثة', 'is_active' => true, 'order' => 2],
            ['name' => 'الذكاء الاصطناعي', 'slug' => 'artificial-intelligence', 'description' => 'أخبار ومقالات عن الذكاء الاصطناعي وتعلم الآلة', 'is_active' => true, 'order' => 3],
            ['name' => 'الأمن السيبراني', 'slug' => 'cybersecurity', 'description' => 'نصائح ومقالات حول حماية البيانات والأمن الرقمي', 'is_active' => true, 'order' => 4],
            ['name' => 'تطبيقات الجوال', 'slug' => 'mobile-apps', 'description' => 'تطوير تطبيقات iOS و Android', 'is_active' => true, 'order' => 5],
            ['name' => 'الحوسبة السحابية', 'slug' => 'cloud-computing', 'description' => 'خدمات الحوسبة السحابية وأفضل الممارسات', 'is_active' => true, 'order' => 6],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $category = BlogCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
            $categoryMap[$cat['slug']] = $category;
        }

        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel', 'is_active' => true],
            ['name' => 'PHP', 'slug' => 'php', 'is_active' => true],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'is_active' => true],
            ['name' => 'React', 'slug' => 'react', 'is_active' => true],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'is_active' => true],
            ['name' => 'Python', 'slug' => 'python', 'is_active' => true],
            ['name' => 'الذكاء الاصطناعي', 'slug' => 'ai', 'is_active' => true],
            ['name' => 'تعلم الآلة', 'slug' => 'machine-learning', 'is_active' => true],
            ['name' => 'الأمن السيبراني', 'slug' => 'security', 'is_active' => true],
            ['name' => 'Docker', 'slug' => 'docker', 'is_active' => true],
            ['name' => 'AWS', 'slug' => 'aws', 'is_active' => true],
            ['name' => 'Flutter', 'slug' => 'flutter', 'is_active' => true],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'is_active' => true],
            ['name' => 'Node.js', 'slug' => 'nodejs', 'is_active' => true],
            ['name' => 'MySQL', 'slug' => 'mysql', 'is_active' => true],
        ];

        $tagMap = [];
        foreach ($tags as $tag) {
            $t = BlogTag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
            $tagMap[$tag['slug']] = $t;
        }

        $author = User::role('admin')->first() ?? User::first();

        if (! $author) {
            $this->command->warn('لا يوجد مستخدمون. شغّل AdminUserSeeder أولاً.');

            return;
        }

        $posts = [
            [
                'title' => 'دليلك الشامل لتعلم Laravel 11 في 2025',
                'slug' => 'laravel-11-complete-guide',
                'excerpt' => 'اكتشف أحدث ميزات Laravel 11 وكيفية البدء في بناء تطبيقات ويب قوية وعصرية باستخدام هذا الإطار الرائع.',
                'content' => '
                    <h2>مقدمة عن Laravel 11</h2>
                    <p>Laravel 11 هو أحدث إصدار من إطار عمل PHP الأكثر شعبية في العالم. يأتي هذا الإصدار بالعديد من التحسينات والميزات الجديدة التي تجعل تطوير الويب أسهل وأكثر كفاءة.</p>

                    <h2>أهم الميزات الجديدة</h2>
                    <h3>1. هيكلية مبسطة</h3>
                    <p>تم تبسيط هيكلية المشروع بشكل كبير في Laravel 11. تم إزالة العديد من الملفات الافتراضية التي لم تكن ضرورية، مما يجعل المشروع أنظف وأسهل في الفهم.</p>

                    <h3>2. تحسين الأداء</h3>
                    <p>تم تحسين أداء Laravel بشكل ملحوظ في هذا الإصدار. تم تقليل وقت التحميل وتحسين استهلاك الذاكرة بشكل كبير.</p>

                    <h3>3. دعم PHP 8.3</h3>
                    <p>Laravel 11 يدعم بشكل كامل PHP 8.3 مع جميع ميزاته الجديدة مثل typed class constants و dynamic class constant fetch.</p>

                    <h3>4. نظام المصادقة المحسّن</h3>
                    <p>تم تحديث نظام المصادقة ليدعم ميزات جديدة مثل المصادقة الثنائية factor وتحسين إدارة الجلسات.</p>

                    <h2>كيفية البدء</h2>
                    <p>لبدء مشروع جديد مع Laravel 11، يمكنك استخدام Composer:</p>
                    <pre><code>composer create-project laravel/laravel my-project</code></pre>
                    <p>ثم قم بتشغيل الخادم المحلي:</p>
                    <pre><code>php artisan serve</code></pre>

                    <h2>نصائح للمبتدئين</h2>
                    <ul>
                        <li>ابدأ بتعلم أساسيات PHP أولاً</li>
                        <li>فهم مفهوم MVC (Model-View-Controller)</li>
                        <li>تعلم كيفية استخدام Eloquent ORM</li>
                        <li>تدرب على بناء مشاريع صغيرة</li>
                        <li>اقرأ التوثيق الرسمي بشكل منتظم</li>
                    </ul>

                    <h2>الخاتمة</h2>
                    <p>Laravel 11 يمثل قفزة كبيرة في عالم تطوير الويب باستخدام PHP. مع ميزاته الجديدة وتحسينات الأداء، يبقى الخيار الأمثل لبناء تطبيقات ويب حديثة وقوية.</p>
                ',
                'category_slug' => 'web-development',
                'tag_slugs' => ['laravel', 'php'],
                'reading_time' => 8,
                'is_featured' => true,
                'published_days_ago' => 2,
                'image_seed' => 'laravel-guide',
            ],
            [
                'title' => 'مستقبل الذكاء الاصطناعي: كيف سيغير حياتنا في العقد القادم',
                'slug' => 'future-of-ai-next-decade',
                'excerpt' => 'نظرة على التطورات المتوقعة في مجال الذكاء الاصطناعي وتأثيرها على مختلف جوانب حياتنا اليومية.',
                'content' => '
                    <h2>الذكاء الاصطناعي اليوم</h2>
                    <p>يشهد مجال الذكاء الاصطناعي تطوراً سريعاً لم يسبق له مثيل. من المساعدات الذكية إلى السيارات ذاتية القيادة، أصبح الذكاء الاصطناعي جزءاً لا يتجزأ من حياتنا اليومية.</p>

                    <h2>التطورات المتوقعة</h2>
                    <h3>1. الذكاء الاصطناعي التوليدي</h3>
                    <p>يتوقع الخبراء أن الذكاء الاصطناعي التوليدي سيصبح أكثر تطوراً وقدرة على إنشاء محتوى إبداعي ومعقد. من النصوص إلى الصور ومقاطع الفيديو، ستصبح هذه الأدوات أكثر دقة وواقعية.</p>

                    <h3>2. الرعاية الصحية</h3>
                    <p>سيلعب الذكاء الاصطناعي دوراً محورياً في تشخيص الأمراض وتطوير الأدوية الجديدة. سيتمكن الأطباء من استخدام أدوات الذكاء الاصطناعي لتحليل البيانات الطبية بدقة عالية.</p>

                    <h3>3. التعليم المخصص</h3>
                    <p>سيتمكن كل طالب من الحصول على تجربة تعليمية مخصصة تناسب أسلوب تعلمه وسرعته الخاصة. سيقوم الذكاء الاصطناعي بتكييف المحتوى التعليمي بناءً على أداء الطالب.</p>

                    <h3>4. النقل والمواصلات</h3>
                    <p>ستصبح السيارات ذاتية القيادة أكثر أماناً وانتشاراً. سيتم تحسين حركة المرور وتقليل الحوادث بشكل كبير.</p>

                    <h2>التحديات والمخاوف</h2>
                    <p>رغم الفوائد الكبيرة، هناك تحديات يجب مواجهتها:</p>
                    <ul>
                        <li>الخصوصية وحماية البيانات</li>
                        <li>التأثير على سوق العمل</li>
                        <li>الأخلاقيات والمسؤولية</li>
                        <li>التحيز في الخوارزميات</li>
                    </ul>

                    <h2>الخاتمة</h2>
                    <p>مستقبل الذكاء الاصطناعي مشرق ومليء بالفرص. لكن يجب علينا التعامل معه بحكمة ومسؤولية لضمان تحقيق أقصى فائدة مع تقليل المخاطر.</p>
                ',
                'category_slug' => 'artificial-intelligence',
                'tag_slugs' => ['ai', 'machine-learning'],
                'reading_time' => 10,
                'is_featured' => true,
                'published_days_ago' => 5,
                'image_seed' => 'ai-future',
            ],
            [
                'title' => 'أفضل 10 ممارسات لحماية بياناتك على الإنترنت',
                'slug' => 'best-practices-online-data-protection',
                'excerpt' => 'تعرف على أهم النصائح والممارسات لحماية بياناتك الشخصية والحفاظ على أمنك الرقمي في عالم متصل.',
                'content' => '
                    <h2>لماذا حماية البيانات مهمة؟</h2>
                    <p>في عالمنا الرقمي المتصل، أصبحت بياناتنا الشخصية هدفاً للقراصنين والمحتالين. حماية هذه البيانات لم تعد خياراً بل ضرورة.</p>

                    <h2>أفضل 10 ممارسات</h2>
                    <h3>1. استخدم كلمات مرور قوية وفريدة</h3>
                    <p>استخدم كلمة مرور مختلفة لكل حساب. اجعلها طويلة ومعقدة تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز.</p>

                    <h3>2. فعّل المصادقة الثنائية</h3>
                    <p>أضف طبقة حماية إضافية لحساباتك باستخدام المصادقة الثنائية factor. حتى لو عرف أحدهم كلمة مرورك، لن يتمكن من الدخول.</p>

                    <h3>3. حدّث برامجك بانتظام</h3>
                    <p>التحديثات تحتوي على إصلاحات أمنية مهمة. تأكد من تحديث نظام التشغيل وجميع التطبيقات بشكل منتظم.</p>

                    <h3>4. احذر من رسائل التصيد</h3>
                    <p>لا تنقر على روابط مشبوهة في رسائل البريد الإلكتروني. تحقق دائماً من مصدر الرسالة قبل التفاعل معها.</p>

                    <h3>5. استخدم شبكة VPN</h3>
                    <p>عند الاتصال بشبكات Wi-Fi عامة، استخدم VPN لتشفير اتصالك وحماية بياناتك من المتطفلين.</p>

                    <h3>6. قم بنسخ بياناتك احتياطياً</h3>
                    <p>احتفظ بنسخة احتياطية من بياناتك المهمة على جهاز خارجي أو خدمة سحابية موثوقة.</p>

                    <h3>7. راجع أذونات التطبيقات</h3>
                    <p>تحقق من الصلاحيات التي تمنحها للتطبيقات على هاتفك. أزل الصلاحيات غير الضرورية.</p>

                    <h3>8. استخدم مدير كلمات المرور</h3>
                    <p>مدير كلمات المرور يساعدك على إنشاء وتخزين كلمات مرور قوية وفريدة لكل حساب.</p>

                    <h3>9. شفّر بياناتك الحساسة</h3>
                    <p>استخدم التشفير لحماية الملفات الحساسة على جهازك. معظم أنظمة التشغيل توفر خيارات تشفير مدمجة.</p>

                    <h3>10. تثقف باستمرار</h3>
                    <p>ابقَ على اطلاع بآخر التهديدات الأمنية وأفضل الممارسات. المعرفة هي أفضل حماية.</p>

                    <h2>الخاتمة</h2>
                    <p>حماية بياناتك على الإنترنت مسؤولية مستمرة. بتطبيق هذه الممارسات العشر، ستقلل بشكل كبير من مخاطر تعرض بياناتك للسرقة أو الاختراق.</p>
                ',
                'category_slug' => 'ecommerce',
                'tag_slugs' => ['security'],
                'reading_time' => 7,
                'is_featured' => false,
                'published_days_ago' => 7,
                'image_seed' => 'data-protection',
            ],
            [
                'title' => 'بناء تطبيقات جوال احترافية باستخدام Flutter 2025',
                'slug' => 'building-professional-mobile-apps-flutter',
                'excerpt' => 'دليل شامل لبناء تطبيقات جوال متعددة المنصات باستخدام Flutter مع أفضل الممارسات والنصائح العملية.',
                'content' => '
                    <h2>ما هو Flutter؟</h2>
                    <p>Flutter هو إطار عمل مفتوح المصدر طورته Google لبناء تطبيقات جوال جميلة وعالية الأداء لمنصتي iOS و Android من قاعدة كود واحدة.</p>

                    <h2>لماذا Flutter؟</h2>
                    <ul>
                        <li>أداء قريب من الأداء الأصلي</li>
                        <li>واجهة مستخدم مرنة وقابلة للتخصيص</li>
                        <li>تطوير سريع مع Hot Reload</li>
                        <li>مجتمع كبير ودعم مستمر</li>
                        <li>دعم متعدد المنصات (iOS, Android, Web, Desktop)</li>
                    </ul>

                    <h2>البدء مع Flutter</h2>
                    <h3>1. تثبيت Flutter SDK</h3>
                    <p>قم بتحميل وتثبيت Flutter SDK من الموقع الرسمي. تأكد من تثبيت جميع المتطلبات المسبقة.</p>

                    <h3>2. إنشاء مشروع جديد</h3>
                    <pre><code>flutter create my_app
cd my_app
flutter run</code></pre>

                    <h3>3. فهم بنية المشروع</h3>
                    <p>يتكون مشروع Flutter من عدة ملفات ومجلدات مهمة. الملف الرئيسي هو lib/main.dart حيث يبدأ التطبيق.</p>

                    <h2>أفضل الممارسات</h2>
                    <h3>إدارة الحالة</h3>
                    <p>استخدم Provider أو Riverpod أو Bloc لإدارة حالة تطبيقك بشكل فعال. اختر الأداة التي تناسب حجم وتعقيد مشروعك.</p>

                    <h3>الأداء</h3>
                    <ul>
                        <li>استخدم const widgets قدر الإمكان</li>
                        <li>تجنب إعادة البناء غير الضرورية</li>
                        <li>استخدم ListView.builder للقوائم الطويلة</li>
                        <li>قم بتحليل الأداء باستخدام DevTools</li>
                    </ul>

                    <h3>التصميم</h3>
                    <p>اتبع مبادئ Material Design لنظام Android و Cupertino لنظام iOS. استخدم ThemeData لتخصيص مظهر تطبيقك.</p>

                    <h2>الخاتمة</h2>
                    <p>Flutter هو خيار ممتاز لبناء تطبيقات جوال متعددة المنصات. مع ممارسته المستمرة وتعلم أفضل الممارسات، ستتمكن من بناء تطبيقات احترافية عالية الجودة.</p>
                ',
                'category_slug' => 'mobile-apps',
                'tag_slugs' => ['flutter', 'javascript'],
                'reading_time' => 9,
                'is_featured' => false,
                'published_days_ago' => 10,
                'image_seed' => 'flutter-apps',
            ],
            [
                'title' => 'مقدمة في الحوسبة السحابية: AWS للمبتدئين',
                'slug' => 'cloud-computing-aws-beginners-guide',
                'excerpt' => 'تعرف على أساسيات الحوسبة السحابية وخدمات AWS الأساسية وكيف تبدأ في استخدام البنية التحتية السحابية.',
                'content' => '
                    <h2>ما هي الحوسبة السحابية؟</h2>
                    <p>الحوسبة السحابية هي تقديم خدمات الحوسبة مثل الخوادم والتخزين وقواعد البيانات والشبكات والبرمجيات عبر الإنترنت (السحابة).</p>

                    <h2>لماذا AWS؟</h2>
                    <p>Amazon Web Services (AWS) هي منصة الحوسبة السحابية الأكثر استخداماً في العالم. تقدم أكثر من 200 خدمة من مراكز بيانات حول العالم.</p>

                    <h2>الخدمات الأساسية</h2>
                    <h3>1. Amazon EC2</h3>
                    <p>خدمة الخوادم الافتراضية التي تتيح لك تشغيل تطبيقاتك على خوادم قابلة للتوسع في السحابة.</p>

                    <h3>2. Amazon S3</h3>
                    <p>خدمة تخزين الكائنات التي توفر تخزيناً آمناً وقابلاً للتوسع للبيانات والملفات.</p>

                    <h3>3. Amazon RDS</h3>
                    <p>خدمة قواعد البيانات المدعومة التي تدعم MySQL و PostgreSQL و Oracle و SQL Server.</p>

                    <h3>4. AWS Lambda</h3>
                    <p>خدمة الحوسبة بدون خادم التي تتيح لك تشغيل الكود دون إدارة الخوادم.</p>

                    <h2>كيفية البدء</h2>
                    <ol>
                        <li>أنشئ حساب AWS مجاني (Free Tier)</li>
                        <li>تعلم أساسيات IAM (إدارة الهويات والوصول)</li>
                        <li>ابدأ بـ EC2 لإنشاء خادم افتراضي</li>
                        <li>استخدم S3 لتخزين الملفات</li>
                        <li>جرب Lambda للحوسبة بدون خادم</li>
                    </ol>

                    <h2>نصائح للمبتدئين</h2>
                    <ul>
                        <li>ابدأ بالطبقة المجانية وتعلم تدريجياً</li>
                        <li>راقب تكاليفك باستمرار</li>
                        <li>استخدم AWS CloudFormation للبنية التحتية ككود</li>
                        <li>فعّل MFA لحماية حسابك</li>
                    </ul>

                    <h2>الخاتمة</h2>
                    <p>الحوسبة السحابية مع AWS تفتح آفاقاً واسعة للمطورين والشركات. ابدأ صغيراً وتعلم باستمرار لتحقيق أقصى استفادة.</p>
                ',
                'category_slug' => 'cloud-computing',
                'tag_slugs' => ['aws', 'docker'],
                'reading_time' => 6,
                'is_featured' => false,
                'published_days_ago' => 14,
                'image_seed' => 'aws-cloud',
            ],
            [
                'title' => 'React vs Vue.js: مقارنة شاملة في 2025',
                'slug' => 'react-vs-vuejs-comparison-2025',
                'excerpt' => 'مقارنة تفصيلية بين مكتبة React وإطار عمل Vue.js لمساعدتك في اختيار الأنسب لمشروعك القادم.',
                'content' => '
                    <h2>مقدمة</h2>
                    <p>React و Vue.js هما من أشهر أدوات بناء واجهات المستخدم في عالم تطوير الويب. كل منهما له مزاياه الفريدة ومجالات تميزه.</p>

                    <h2>React</h2>
                    <h3>المزايا</h3>
                    <ul>
                        <li>مدعوم من Meta (Facebook)</li>
                        <li>مجتمع ضخم وبيئة غنية بالمكتبات</li>
                        <li>React Native لتطوير تطبيقات الجوال</li>
                        <li>Next.js للتصيير من جانب الخادم</li>
                        <li>مرونة عالية في اختيار الأدوات</li>
                    </ul>

                    <h3>العيوب</h3>
                    <ul>
                        <li>منحنى تعلم أعلى للمبتدئين</li>
                        <li>يحتاج مكتبات إضافية لإدارة الحالة والتوجيه</li>
                        <li>تغييرات متكررة في أفضل الممارسات</li>
                    </ul>

                    <h2>Vue.js</h2>
                    <h3>المزايا</h3>
                    <ul>
                        <li>سهل التعلم والبدء</li>
                        <li>توثيق ممتاز وشامل</li>
                        <li>Nuxt.js للتصيير من جانب الخادم</li>
                        <li>أدوات رسمية مدمجة (Vuex, Vue Router)</li>
                        <li>أداء ممتاز وحجم ملف صغير</li>
                    </ul>

                    <h3>العيوب</h3>
                    <ul>
                        <li>مجتمع أصغر من React</li>
                        <li>فرص عمل أقل مقارنة بـ React</li>
                        <li>عدد أقل من المكتبات والإضافات</li>
                    </ul>

                    <h2>مقارنة الأداء</h2>
                    <p>في الاختبارات الحديثة، يتفوق Vue.js قليلاً في سرعة التصيير الأولي، بينما يتساوى الاثنان في تحديثات DOM. الفرق عملياً غير ملحوظ في معظم التطبيقات.</p>

                    <h2>أيّهما تختار؟</h2>
                    <p><strong>اختر React إذا:</strong> تحتاج مرونة عالية، تريد العمل في شركات كبيرة، أو تحتاج React Native.</p>
                    <p><strong>اختر Vue.js إذا:</strong> تريد سهولة التعلم، تفضل حلاً متكاملاً، أو تبني مشروعاً صغيراً إلى متوسط.</p>

                    <h2>الخاتمة</h2>
                    <p>لا يوجد خيار أفضل بشكل مطلق. يعتمد الاختيار على احتياجات مشروعك وخبرة فريقك وتفضيلاتك الشخصية.</p>
                ',
                'category_slug' => 'web-development',
                'tag_slugs' => ['react', 'vuejs', 'javascript'],
                'reading_time' => 8,
                'is_featured' => true,
                'published_days_ago' => 18,
                'image_seed' => 'react-vue',
            ],
            [
                'title' => 'تعلم الآلة للمبتدئين: من الصفر إلى أول نموذج',
                'slug' => 'machine-learning-beginners-guide',
                'excerpt' => 'دليل عملي للمبتدئين لفهم أساسيات تعلم الآلة وبناء أول نموذج تنبؤي باستخدام Python و scikit-learn.',
                'content' => '
                    <h2>ما هو تعلم الآلة؟</h2>
                    <p>تعلم الآلة هو فرع من فروع الذكاء الاصطناعي يركز على تطوير أنظمة تتعلم من البيانات وتحسن أداءها مع مرور الوقت دون برمجة صريحة.</p>

                    <h2>أنواع تعلم الآلة</h2>
                    <h3>1. التعلم الموجّه (Supervised Learning)</h3>
                    <p>يتم تدريب النموذج على بيانات مُصنّفة مسبقاً. أمثلة: التصنيف، الانحدار.</p>

                    <h3>2. التعلم غير الموجّه (Unsupervised Learning)</h3>
                    <p>يتم تدريب النموذج على بيانات غير مُصنّفة. أمثلة: التجميع، تقليل الأبعاد.</p>

                    <h3>3. التعلم التعزيزي (Reinforcement Learning)</h3>
                    <p>يتعلم النموذج من خلال التجربة والخطأ مع نظام المكافآت والعقوبات.</p>

                    <h2>بناء أول نموذج</h2>
                    <h3>المتطلبات</h3>
                    <pre><code>pip install numpy pandas scikit-learn matplotlib</code></pre>

                    <h3>الخطوات</h3>
                    <ol>
                        <li>استيراد المكتبات اللازمة</li>
                        <li>تحميل البيانات</li>
                        <li>استكشاف وتنظيف البيانات</li>
                        <li>تقسيم البيانات إلى تدريب واختبار</li>
                        <li>اختيار وتدريب النموذج</li>
                        <li>تقييم النموذج</li>
                        <li>تحسين الأداء</li>
                    </ol>

                    <h2>نصائح للبدء</h2>
                    <ul>
                        <li>ابدأ بمجموعات بيانات بسيطة ومشهورة</li>
                        <li>افهم البيانات قبل بناء النموذج</li>
                        <li>جرب خوارزميات متعددة</li>
                        <li>لا تهمل تقييم النموذج بشكل صحيح</li>
                        <li>تعلم من الأخطاء وحسّن نماذجك باستمرار</li>
                    </ul>

                    <h2>الخاتمة</h2>
                    <p>تعلم الآلة مجال واسع ومثير. ابدأ بالأساسيات وتدرج في التعلم. الممارسة المستمرة هي مفتاح النجاح في هذا المجال.</p>
                ',
                'category_slug' => 'artificial-intelligence',
                'tag_slugs' => ['python', 'machine-learning', 'ai'],
                'reading_time' => 12,
                'is_featured' => false,
                'published_days_ago' => 21,
                'image_seed' => 'machine-learning',
            ],
            [
                'title' => 'Docker للمطورين: دليل عملي شامل',
                'slug' => 'docker-developers-practical-guide',
                'excerpt' => 'تعرف على Docker وكيفية استخدامه لتبسيط عملية تطوير ونشر التطبيقات مع أمثلة عملية.',
                'content' => '
                    <h2>ما هو Docker؟</h2>
                    <p>Docker هو منصة مفتوحة المصدر لتطوير ونقل وتشغيل التطبيقات في حاويات معزولة وخفيفة الوزن.</p>

                    <h2>لماذا Docker؟</h2>
                    <ul>
                        <li>اتساق البيئة بين التطوير والإنتاج</li>
                        <li>عزل التطبيقات عن بعضها</li>
                        <li>سهولة النشر والتوسع</li>
                        <li>تقليل تعقيد إدارة البنية التحتية</li>
                        <li>دعم CI/CD بشكل ممتاز</li>
                    </ul>

                    <h2>المفاهيم الأساسية</h2>
                    <h3>الصورة (Image)</h3>
                    <p>قالب جاهز يحتوي على كل ما يحتاجه التطبيق للعمل. مثل قالب VM لكن أخف بكثير.</p>

                    <h3>الحاوية (Container)</h3>
                    <p>نسخة قابلة للتشغيل من الصورة. كل حاوية معزولة عن الأخرى.</p>

                    <h3>Dockerfile</h3>
                    <p>ملف نصي يحتوي على تعليمات لبناء صورة Docker.</p>

                    <h2>مثال عملي</h2>
                    <h3>إنشاء Dockerfile لتطبيق Laravel</h3>
                    <pre><code>FROM php:8.2-fpm
WORKDIR /var/www/html
COPY . .
RUN composer install
EXPOSE 9000
CMD ["php-fpm"]</code></pre>

                    <h3>بناء وتشغيل الحاوية</h3>
                    <pre><code>docker build -t my-laravel-app .
docker run -p 9000:9000 my-laravel-app</code></pre>

                    <h2>Docker Compose</h2>
                    <p>أداة لتعريف وتشغيل تطبيقات متعددة الحاويات. مثالية لبيئات التطوير المحلية.</p>

                    <h2>أفضل الممارسات</h2>
                    <ul>
                        <li>استخدم صور رسمية وموثوقة</li>
                        <li>قلل حجم الصور باستخدام Alpine</li>
                        <li>استخدم .dockerignore</li>
                        <li>لا تشغل الحاويات كـ root</li>
                        <li>استخدم Docker Compose للبيئات المحلية</li>
                    </ul>

                    <h2>الخاتمة</h2>
                    <p>Docker أداة قوية تغير طريقة تطوير ونشر التطبيقات. ابدأ بتجربته في مشاريعك الصغيرة ثم وسّع استخدامه تدريجياً.</p>
                ',
                'category_slug' => 'web-development',
                'tag_slugs' => ['docker', 'php', 'laravel'],
                'reading_time' => 10,
                'is_featured' => false,
                'published_days_ago' => 25,
                'image_seed' => 'docker-dev',
            ],
            [
                'title' => 'TypeScript: لماذا يجب عليك استخدامه في 2025',
                'slug' => 'why-use-typescript-2025',
                'excerpt' => 'اكتشف مزايا TypeScript وكيف يساعد في كتابة كود أكثر أماناً وقابلية للصيانة في مشاريع JavaScript.',
                'content' => '
                    <h2>ما هو TypeScript؟</h2>
                    <p>TypeScript هو إضافة لـ JavaScript طورته Microsoft. يضيف نظام أنواع اختياري على JavaScript العادي.</p>

                    <h2>المزايا الرئيسية</h2>
                    <h3>1. التحقق من الأنواع</h3>
                    <p>يكتشف TypeScript الأخطاء المتعلقة بالأنواع أثناء التطوير بدلاً من وقت التشغيل، مما يوفر وقتاً ثميناً.</p>

                    <h3>2. إكمال الكود الذكي</h3>
                    <p>يوفر IDE إكمال كود ذكي ودقيق بناءً على تعريفات الأنواع، مما يسرّع عملية التطوير.</p>

                    <h3>3. إعادة الهيكلة الآمنة</h3>
                    <p>يمكنك إعادة هيكلة كودك بثقة مع ضمان عدم كسر أي جزء من التطبيق.</p>

                    <h3>4. التوثيق الذاتي</h3>
                    <p>تعريفات الأنواع تعمل كتوثيق حي للكود، مما يسهل على المطورين الجدد فهم المشروع.</p>

                    <h2>مثال عملي</h2>
                    <pre><code>interface User {
  id: number;
  name: string;
  email: string;
  role: \'admin\' | \'user\';
}

function greet(user: User): string {
  return `مرحباً ${user.name}!`;
}</code></pre>

                    <h2>البدء مع TypeScript</h2>
                    <pre><code>npm install -g typescript
tsc --init
tsc</code></pre>

                    <h2>متى تستخدم TypeScript؟</h2>
                    <ul>
                        <li>مشاريع كبيرة ومعقدة</li>
                        <li>فرق عمل متعددة</li>
                        <li>مشاريع طويلة الأمد</li>
                        <li>عند استخدام React أو Angular أو Vue.js</li>
                    </ul>

                    <h2>الخاتمة</h2>
                    <p>TypeScript أصبح المعيار في تطوير JavaScript الحديث. استثمار الوقت في تعلمه سيؤتي ثماره في جودة وإنتاجية مشاريعك.</p>
                ',
                'category_slug' => 'web-development',
                'tag_slugs' => ['typescript', 'javascript', 'nodejs'],
                'reading_time' => 7,
                'is_featured' => false,
                'published_days_ago' => 30,
                'image_seed' => 'typescript-2025',
            ],
            [
                'title' => 'أمان قواعد البيانات: حماية MySQL من الاختراق',
                'slug' => 'mysql-database-security-protection',
                'excerpt' => 'دليل شامل لتأمين قواعد بيانات MySQL وحمايتها من التهديدات الأمنية الشائعة مع خطوات عملية.',
                'content' => '
                    <h2>أهمية تأمين قواعد البيانات</h2>
                    <p>قواعد البيانات هي قلب أي تطبيق. تحتوي على البيانات الأكثر حساسية والأهمية. تأمينها يجب أن يكون أولوية قصوى.</p>

                    <h2>أفضل الممارسات لتأمين MySQL</h2>

                    <h3>1. تغيير المنفذ الافتراضي</h3>
                    <p>لا تستخدم المنفذ 3306 الافتراضي. غيّره إلى منفذ غير شائع لتقليل هجمات المسح التلقائي.</p>

                    <h3>2. تعطيل الوصول عن بُعد</h3>
                    <p>إذا لم تكن بحاجة للوصول عن بُعد، قم بتعطيله. استخدم SSH للنفق الآمن عند الحاجة.</p>

                    <h3>3. استخدام كلمات مرور قوية</h3>
                    <p>تأكد من أن جميع حسابات MySQL تستخدم كلمات مرور قوية ومعقدة.</p>

                    <h3>4. مبدأ أقل امتياز</h3>
                    <p>امنح كل مستخدم الحد الأدنى من الصلاحيات التي يحتاجها فقط. لا تستخدم حساب root في التطبيقات.</p>

                    <h3>5. تشفير الاتصالات</h3>
                    <p>فعّل SSL/TLS لتشفير الاتصالات بين التطبيق وقاعدة البيانات.</p>

                    <h3>6. النسخ الاحتياطي المنتظم</h3>
                    <p>قم بنسخ بياناتك احتياطياً بشكل منتظم واحفظ النسخ في مواقع آمنة ومنعزلة.</p>

                    <h3>7. تحديث MySQL</h3>
                    <p>حافظ على تحديث MySQL لأحدث إصدار للحصول على آخر إصلاحات الأمان.</p>

                    <h3>8. مراقبة السجلات</h3>
                    <p>راقب سجلات MySQL لاكتشاف أي نشاط مشبوه أو محاولات وصول غير مصرح بها.</p>

                    <h2>أوامر أمنية مفيدة</h2>
                    <pre><code>-- عرض جميع المستخدمين
SELECT user, host FROM mysql.user;

-- إنشاء مستخدم جديد بصلاحيات محدودة
CREATE USER \'app_user\'@\'localhost\' IDENTIFIED BY \'strong_password\';
GRANT SELECT, INSERT, UPDATE, DELETE ON mydb.* TO \'app_user\'@\'localhost\';

-- إزالة المستخدمين غير المستخدمين
DROP USER \'\'@\'localhost\';</code></pre>

                    <h2>الخاتمة</h2>
                    <p>تأمين قواعد البيانات عملية مستمرة وليست خطوة واحدة. اتبع هذه الممارسات وراجع إعدادات الأمان بشكل دوري.</p>
                ',
                'category_slug' => 'cybersecurity',
                'tag_slugs' => ['security', 'mysql', 'php'],
                'reading_time' => 8,
                'is_featured' => false,
                'published_days_ago' => 35,
                'image_seed' => 'mysql-security',
            ],
        ];

        foreach ($posts as $postData) {
            $category = $categoryMap[$postData['category_slug']] ?? null;
            $tagSlugs = $postData['tag_slugs'] ?? [];
            $imageSeed = $postData['image_seed'] ?? $postData['slug'];
            $featuredImage = 'https://picsum.photos/seed/' . $imageSeed . '/800/500';

            $post = BlogPost::updateOrCreate(
                ['slug' => $postData['slug']],
                [
                    'title' => $postData['title'],
                    'excerpt' => $postData['excerpt'],
                    'content' => trim($postData['content']),
                    'featured_image' => $featuredImage,
                    'featured_image_alt' => $postData['title'],
                    'author_id' => $author->id,
                    'blog_category_id' => $category?->id,
                    'status' => 'published',
                    'published_at' => now()->subDays($postData['published_days_ago']),
                    'reading_time' => $postData['reading_time'],
                    'is_featured' => $postData['is_featured'] ?? false,
                    'views_count' => random_int(120, 2500),
                    'comments_count' => random_int(0, 18),
                    'is_indexable' => true,
                    'is_followable' => true,
                    'robots_meta' => 'index,follow',
                    'language' => 'ar',
                    'meta_title' => $postData['title'],
                    'meta_description' => $postData['excerpt'],
                    'schema_type' => 'BlogPosting',
                    'og_type' => 'article',
                    'og_locale' => 'ar_SA',
                    'twitter_card' => 'summary_large_image',
                ]
            );

            $tagIds = collect($tagSlugs)
                ->map(fn (string $slug) => $tagMap[$slug]->id ?? null)
                ->filter()
                ->values()
                ->all();

            $post->tags()->sync($tagIds);
        }

        foreach ($categoryMap as $category) {
            $category->updatePostsCount();
        }

        $this->command->info('تم إنشاء/تحديث المدونة: ' . count($posts) . ' تدوينة، ' . count($categories) . ' تصنيفات، ' . count($tags) . ' وسوم.');
    }
}
