<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Services\Storage\StorageHelperService;
use Illuminate\Support\Facades\Cache;

class SiteSettingsService
{
    public const GROUP = 'site';

    public const KEY_SITE_NAME = 'site_name';
    public const KEY_SITE_DESCRIPTION = 'site_description';
    public const KEY_SITE_LOGO = 'site_logo';
    public const KEY_SITE_FAVICON = 'site_favicon';
    public const KEY_SITE_ACCENT_COLOR = 'site_accent_color';
    public const KEY_SITE_MAINTENANCE_MODE = 'site_maintenance_mode';
    public const KEY_SITE_MAINTENANCE_MESSAGE = 'site_maintenance_message';
    public const KEY_SITE_CONTACT_EMAIL = 'site_contact_email';
    public const KEY_SITE_CONTACT_PHONE = 'site_contact_phone';
    public const KEY_SITE_ADDRESS = 'site_address';
    public const KEY_SITE_TIMEZONE = 'site_timezone';
    public const KEY_SITE_LOCALE = 'site_locale';
    public const KEY_SITE_META_KEYWORDS = 'site_meta_keywords';
    public const KEY_SITE_META_DESCRIPTION = 'site_meta_description';
    public const KEY_SITE_FOOTER_TEXT = 'site_footer_text';
    public const KEY_SITE_FACEBOOK_URL = 'site_facebook_url';
    public const KEY_SITE_TWITTER_URL = 'site_twitter_url';
    public const KEY_SITE_INSTAGRAM_URL = 'site_instagram_url';
    public const KEY_SITE_WHATSAPP_NUMBER = 'site_whatsapp_number';

    public const KEY_HERO_BADGE = 'hero_badge';
    public const KEY_HERO_TITLE_PREFIX = 'hero_title_prefix';
    public const KEY_HERO_TYPING_WORDS = 'hero_typing_words';
    public const KEY_HERO_SUBTITLE = 'hero_subtitle';
    public const KEY_HERO_BTN_PRIMARY_LABEL = 'hero_btn_primary_label';
    public const KEY_HERO_BTN_PRIMARY_URL = 'hero_btn_primary_url';
    public const KEY_HERO_BTN_SECONDARY_LABEL = 'hero_btn_secondary_label';
    public const KEY_HERO_BTN_SECONDARY_URL = 'hero_btn_secondary_url';
    public const KEY_HERO_IMAGE = 'hero_image';
    public const KEY_HERO_BG_MODE = 'hero_bg_mode';
    public const KEY_HERO_BG_COLOR = 'hero_bg_color';
    public const KEY_HERO_BG_IMAGE = 'hero_bg_image';
    public const KEY_HERO_STATS = 'hero_stats';

    public const KEY_ABOUT_HERO_TITLE = 'about_hero_title';
    public const KEY_ABOUT_HERO_SUBTITLE = 'about_hero_subtitle';
    public const KEY_ABOUT_STORY_TITLE = 'about_story_title';
    public const KEY_ABOUT_STORY_TEXT_1 = 'about_story_text_1';
    public const KEY_ABOUT_STORY_TEXT_2 = 'about_story_text_2';
    public const KEY_ABOUT_STORY_IMAGE = 'about_story_image';
    public const KEY_ABOUT_VISION_TITLE = 'about_vision_title';
    public const KEY_ABOUT_VISION_TEXT = 'about_vision_text';
    public const KEY_ABOUT_MISSION_TITLE = 'about_mission_title';
    public const KEY_ABOUT_MISSION_TEXT = 'about_mission_text';
    public const KEY_ABOUT_VALUES = 'about_values';
    public const KEY_ABOUT_STATS = 'about_stats';
    public const KEY_ABOUT_CTA_TITLE = 'about_cta_title';
    public const KEY_ABOUT_CTA_TEXT = 'about_cta_text';
    public const KEY_ABOUT_CTA_BTN_LABEL = 'about_cta_btn_label';
    public const KEY_ABOUT_CTA_BTN_URL = 'about_cta_btn_url';

    public const KEY_FAQ_HERO_TITLE = 'faq_hero_title';
    public const KEY_FAQ_HERO_SUBTITLE = 'faq_hero_subtitle';
    public const KEY_FAQ_GROUPS = 'faq_groups';
    public const KEY_FAQ_CTA_TITLE = 'faq_cta_title';
    public const KEY_FAQ_CTA_TEXT = 'faq_cta_text';
    public const KEY_FAQ_CTA_BTN_LABEL = 'faq_cta_btn_label';
    public const KEY_FAQ_CTA_BTN_URL = 'faq_cta_btn_url';

    public const KEY_TERMS_HERO_TITLE = 'terms_hero_title';
    public const KEY_TERMS_HERO_SUBTITLE = 'terms_hero_subtitle';
    public const KEY_TERMS_LAST_UPDATED = 'terms_last_updated';
    public const KEY_TERMS_INTRO = 'terms_intro';
    public const KEY_TERMS_SECTIONS = 'terms_sections';

    public const KEY_PRIVACY_HERO_TITLE = 'privacy_hero_title';
    public const KEY_PRIVACY_HERO_SUBTITLE = 'privacy_hero_subtitle';
    public const KEY_PRIVACY_LAST_UPDATED = 'privacy_last_updated';
    public const KEY_PRIVACY_INTRO = 'privacy_intro';
    public const KEY_PRIVACY_SECTIONS = 'privacy_sections';

    private const CACHE_KEY = 'site_settings';
    private const CACHE_TTL = 3600;

    /**
     * Schema for all site settings: key => [ type, default, label_ar, section, hint ]
     */
    public static function schema(): array
    {
        return [
            self::KEY_SITE_NAME => [
                'type' => 'string',
                'default' => 'RoviaLink',
                'label' => 'اسم الموقع',
                'section' => 'general',
                'hint' => 'يظهر في الهيدر والفوتر وعناوين الصفحات.',
            ],
            self::KEY_SITE_DESCRIPTION => [
                'type' => 'string',
                'default' => '',
                'label' => 'وصف الموقع',
                'section' => 'general',
                'hint' => 'وصف مختصر يظهر في محركات البحث.',
            ],
            self::KEY_SITE_LOGO => [
                'type' => 'string',
                'default' => '',
                'label' => 'شعار الموقع',
                'section' => 'branding',
                'hint' => 'مسار الصورة (يُرفع من الحقل أدناه).',
            ],
            self::KEY_SITE_FAVICON => [
                'type' => 'string',
                'default' => '',
                'label' => 'أيقونة الموقع (Favicon)',
                'section' => 'branding',
                'hint' => 'صورة صغيرة تظهر في تاب المتصفح.',
            ],
            self::KEY_SITE_ACCENT_COLOR => [
                'type' => 'color',
                'default' => '#387e99',
                'label' => 'لون التمييز (الفرونت اند)',
                'section' => 'branding',
                'hint' => 'يُطبَّق على الأزرار، الروابط النشطة، والهيدر. الافتراضي: تركوازي.',
            ],
            self::KEY_SITE_MAINTENANCE_MODE => [
                'type' => 'boolean',
                'default' => false,
                'label' => 'تفعيل وضع الصيانة',
                'section' => 'maintenance',
                'hint' => 'عند التفعيل يظهر زوار الموقع صفحة صيانة فقط.',
            ],
            self::KEY_SITE_MAINTENANCE_MESSAGE => [
                'type' => 'string',
                'default' => 'الموقع قيد الصيانة. نعود قريباً.',
                'label' => 'رسالة الصيانة',
                'section' => 'maintenance',
                'hint' => 'النص الذي يراه الزوار أثناء الصيانة.',
            ],
            self::KEY_SITE_CONTACT_EMAIL => [
                'type' => 'string',
                'default' => 'support@ediostore.com',
                'label' => 'البريد الإلكتروني للتواصل',
                'section' => 'contact',
                'hint' => 'يظهر في شريط الهيدر العلوي وتذييل الموقع.',
            ],
            self::KEY_SITE_CONTACT_PHONE => [
                'type' => 'string',
                'default' => '+971 50 123 4567',
                'label' => 'رقم الهاتف',
                'section' => 'contact',
                'hint' => 'يظهر في شريط الهيدر العلوي وتذييل الموقع.',
            ],
            self::KEY_SITE_ADDRESS => [
                'type' => 'string',
                'default' => 'دبي، الإمارات العربية المتحدة',
                'label' => 'العنوان',
                'section' => 'contact',
                'hint' => 'يظهر في قسم «تواصل معنا» في التذييل.',
            ],
            self::KEY_SITE_WHATSAPP_NUMBER => [
                'type' => 'string',
                'default' => '',
                'label' => 'رقم واتساب للتواصل',
                'section' => 'contact',
                'hint' => 'بدون + أو مسافات، مثال: 966501234567',
            ],
            self::KEY_SITE_TIMEZONE => [
                'type' => 'string',
                'default' => config('app.timezone', 'Asia/Riyadh'),
                'label' => 'المنطقة الزمنية',
                'section' => 'locale',
                'hint' => 'مثال: Asia/Riyadh',
            ],
            self::KEY_SITE_LOCALE => [
                'type' => 'string',
                'default' => config('app.locale', 'ar'),
                'label' => 'اللغة الافتراضية',
                'section' => 'locale',
                'hint' => 'مثال: ar, en',
            ],
            self::KEY_SITE_META_KEYWORDS => [
                'type' => 'string',
                'default' => '',
                'label' => 'كلمات ميتا (SEO)',
                'section' => 'seo',
                'hint' => 'كلمات مفصولة بفاصلة للبحث.',
            ],
            self::KEY_SITE_META_DESCRIPTION => [
                'type' => 'string',
                'default' => '',
                'label' => 'وصف ميتا (SEO)',
                'section' => 'seo',
                'hint' => 'وصف يظهر في نتائج محركات البحث.',
            ],
            self::KEY_SITE_FOOTER_TEXT => [
                'type' => 'string',
                'default' => 'متجرك الإلكتروني الأول للتسوق الذكي. نقدم لك أفضل المنتجات بأسعار تنافسية مع ضمان الجودة والتوصيل السريع في جميع أنحاء الإمارات.',
                'label' => 'نص تعريفي في الفوتر',
                'section' => 'contact',
                'hint' => 'الوصف تحت الشعار في عمود التذييل الأول.',
            ],
            self::KEY_SITE_FACEBOOK_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط فيسبوك',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_SITE_TWITTER_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط تويتر / X',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_SITE_INSTAGRAM_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط انستغرام',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_HERO_BADGE => [
                'type' => 'string',
                'default' => 'متجرك الرقمي — تسليم فوري وآمن',
                'label' => 'شارة الهيرو',
                'section' => 'homepage',
                'hint' => 'نص صغير أعلى العنوان.',
            ],
            self::KEY_HERO_TITLE_PREFIX => [
                'type' => 'string',
                'default' => 'احصل على أفضل',
                'label' => 'بادئة العنوان',
                'section' => 'homepage',
                'hint' => 'السطر الثابت قبل الكلمات المتحركة.',
            ],
            self::KEY_HERO_TYPING_WORDS => [
                'type' => 'json',
                'default' => '',
                'label' => 'كلمات العنوان المتحركة',
                'section' => 'homepage',
                'hint' => 'سطر لكل كلمة، أو JSON مصفوفة.',
            ],
            self::KEY_HERO_SUBTITLE => [
                'type' => 'string',
                'default' => 'منتجات رقمية أصلية بأسعار تنافسية — تنزيل فوري بعد الدفع، دفع آمن، ودعم فني على مدار الساعة.',
                'label' => 'وصف الهيرو',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_BTN_PRIMARY_LABEL => [
                'type' => 'string',
                'default' => 'تصفح المنتجات',
                'label' => 'نص الزر الرئيسي',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_BTN_PRIMARY_URL => [
                'type' => 'string',
                'default' => '/shop',
                'label' => 'رابط الزر الرئيسي',
                'section' => 'homepage',
                'hint' => 'مسار نسبي أو رابط كامل.',
            ],
            self::KEY_HERO_BTN_SECONDARY_LABEL => [
                'type' => 'string',
                'default' => 'التصنيفات',
                'label' => 'نص الزر الثانوي',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_BTN_SECONDARY_URL => [
                'type' => 'string',
                'default' => '/categories',
                'label' => 'رابط الزر الثانوي',
                'section' => 'homepage',
                'hint' => '',
            ],
            self::KEY_HERO_IMAGE => [
                'type' => 'string',
                'default' => '',
                'label' => 'صورة الهيرو (جانب)',
                'section' => 'homepage',
                'hint' => 'تظهر على يسار القسم في الصفحة الرئيسية.',
            ],
            self::KEY_HERO_BG_MODE => [
                'type' => 'string',
                'default' => 'gradient',
                'label' => 'نوع خلفية الهيرو',
                'section' => 'homepage',
                'hint' => 'gradient: تدرج وأشكال | color: لون ثابت | image: صورة خلفية.',
            ],
            self::KEY_HERO_BG_COLOR => [
                'type' => 'color',
                'default' => '#0a1628',
                'label' => 'لون خلفية الهيرو',
                'section' => 'homepage',
                'hint' => 'يُستخدم عند اختيار «لون ثابت».',
            ],
            self::KEY_HERO_BG_IMAGE => [
                'type' => 'string',
                'default' => '',
                'label' => 'صورة خلفية الهيرو',
                'section' => 'homepage',
                'hint' => 'تُستخدم عند اختيار «صورة خلفية».',
            ],
            self::KEY_HERO_STATS => [
                'type' => 'json',
                'default' => '',
                'label' => 'إحصائيات الهيرو',
                'section' => 'homepage',
                'hint' => 'JSON: مصفوفة من {icon, target, label}',
            ],
            self::KEY_ABOUT_HERO_TITLE => [
                'type' => 'string',
                'default' => 'من نحن',
                'label' => 'عنوان صفحة من نحن',
                'section' => 'about',
                'hint' => 'يظهر في هيرو الصفحة وعنوان المتصفح.',
            ],
            self::KEY_ABOUT_HERO_SUBTITLE => [
                'type' => 'string',
                'default' => 'نبني تجربة تسوق رقمية موثوقة — منتجات أصلية، تسليم فوري، ودعم حقيقي لعملائنا.',
                'label' => 'وصف الهيرو',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_STORY_TITLE => [
                'type' => 'string',
                'default' => 'كيف بدأنا؟',
                'label' => 'عنوان قصة المتجر',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_STORY_TEXT_1 => [
                'type' => 'string',
                'default' => 'انطلق {{site_name}} من رؤية بسيطة: جعل المنتجات الرقمية متاحة للجميع بأسعار عادلة وتجربة شراء سلسة. لاحظنا أن العملاء يحتاجون متجراً يجمع الجودة، السرعة، والشفافية في مكان واحد.',
                'label' => 'الفقرة الأولى',
                'section' => 'about',
                'hint' => 'يمكنك استخدام {{site_name}} لاسم المتجر.',
            ],
            self::KEY_ABOUT_STORY_TEXT_2 => [
                'type' => 'string',
                'default' => 'اليوم نخدم آلاف العملاء بمنتجات رقمية متنوعة — من الاشتراكات والبرمجيات إلى القوالب والأصول — مع التزامنا بالتسليم الفوري بعد الدفع ودعم فني على مدار الساعة.',
                'label' => 'الفقرة الثانية',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_STORY_IMAGE => [
                'type' => 'string',
                'default' => '',
                'label' => 'صورة قصة المتجر',
                'section' => 'about',
                'hint' => 'تظهر بجانب نص القصة.',
            ],
            self::KEY_ABOUT_VISION_TITLE => [
                'type' => 'string',
                'default' => 'رؤيتنا',
                'label' => 'عنوان الرؤية',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_VISION_TEXT => [
                'type' => 'string',
                'default' => 'أن نكون الوجهة الأولى للمنتجات الرقمية في العالم العربي، حيث يجد العميل الجودة والثقة والتسليم الفوري في كل عملية شراء.',
                'label' => 'نص الرؤية',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_MISSION_TITLE => [
                'type' => 'string',
                'default' => 'رسالتنا',
                'label' => 'عنوان الرسالة',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_MISSION_TEXT => [
                'type' => 'string',
                'default' => 'تمكين الأفراد والشركات من الوصول إلى منتجات رقمية أصلية بأسعار تنافسية، مع تجربة دفع آمنة ودعم مستمر قبل وبعد الشراء.',
                'label' => 'نص الرسالة',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_VALUES => [
                'type' => 'json',
                'default' => '',
                'label' => 'قيمنا',
                'section' => 'about',
                'hint' => 'JSON: مصفوفة من {icon, title, text}',
            ],
            self::KEY_ABOUT_STATS => [
                'type' => 'json',
                'default' => '',
                'label' => 'إحصائيات الصفحة',
                'section' => 'about',
                'hint' => 'JSON: مصفوفة من {icon, target, label}',
            ],
            self::KEY_ABOUT_CTA_TITLE => [
                'type' => 'string',
                'default' => 'جاهز لتجربة التسوق؟',
                'label' => 'عنوان دعوة الإجراء',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_CTA_TEXT => [
                'type' => 'string',
                'default' => 'تصفح منتجاتنا الرقمية واستمتع بتسليم فوري ودفع آمن.',
                'label' => 'نص دعوة الإجراء',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_CTA_BTN_LABEL => [
                'type' => 'string',
                'default' => 'تصفح المنتجات',
                'label' => 'نص زر دعوة الإجراء',
                'section' => 'about',
                'hint' => '',
            ],
            self::KEY_ABOUT_CTA_BTN_URL => [
                'type' => 'string',
                'default' => '/shop',
                'label' => 'رابط زر دعوة الإجراء',
                'section' => 'about',
                'hint' => 'مسار نسبي أو رابط كامل.',
            ],
            self::KEY_FAQ_HERO_TITLE => [
                'type' => 'string',
                'default' => 'الأسئلة الشائعة',
                'label' => 'عنوان صفحة الأسئلة الشائعة',
                'section' => 'faq',
                'hint' => 'يظهر في هيرو الصفحة وعنوان المتصفح.',
            ],
            self::KEY_FAQ_HERO_SUBTITLE => [
                'type' => 'string',
                'default' => 'إجابات سريعة عن الطلبات، الدفع، التسليم الرقمي، والدعم — كل ما تحتاج معرفته في مكان واحد.',
                'label' => 'وصف الهيرو',
                'section' => 'faq',
                'hint' => '',
            ],
            self::KEY_FAQ_GROUPS => [
                'type' => 'json',
                'default' => '',
                'label' => 'مجموعات الأسئلة',
                'section' => 'faq',
                'hint' => 'JSON: مصفوفة من {title, icon, items:[{question, answer}]}',
            ],
            self::KEY_FAQ_CTA_TITLE => [
                'type' => 'string',
                'default' => 'لم تجد إجابتك؟',
                'label' => 'عنوان دعوة الإجراء',
                'section' => 'faq',
                'hint' => '',
            ],
            self::KEY_FAQ_CTA_TEXT => [
                'type' => 'string',
                'default' => 'فريق الدعم جاهز لمساعدتك في أي استفسار حول طلباتك أو منتجاتنا.',
                'label' => 'نص دعوة الإجراء',
                'section' => 'faq',
                'hint' => '',
            ],
            self::KEY_FAQ_CTA_BTN_LABEL => [
                'type' => 'string',
                'default' => 'تواصل معنا',
                'label' => 'نص زر دعوة الإجراء',
                'section' => 'faq',
                'hint' => '',
            ],
            self::KEY_FAQ_CTA_BTN_URL => [
                'type' => 'string',
                'default' => '/contact',
                'label' => 'رابط زر دعوة الإجراء',
                'section' => 'faq',
                'hint' => 'مسار نسبي أو رابط كامل.',
            ],
            self::KEY_TERMS_HERO_TITLE => [
                'type' => 'string',
                'default' => 'الشروط والأحكام',
                'label' => 'عنوان صفحة الشروط',
                'section' => 'terms',
                'hint' => 'يظهر في هيرو الصفحة وعنوان المتصفح.',
            ],
            self::KEY_TERMS_HERO_SUBTITLE => [
                'type' => 'string',
                'default' => 'يرجى قراءة هذه الشروط بعناية قبل استخدام المتجر أو إتمام أي عملية شراء.',
                'label' => 'وصف الهيرو',
                'section' => 'terms',
                'hint' => '',
            ],
            self::KEY_TERMS_LAST_UPDATED => [
                'type' => 'string',
                'default' => '',
                'label' => 'تاريخ آخر تحديث',
                'section' => 'terms',
                'hint' => 'مثال: 20 مايو 2026 — يظهر للزوار أعلى المحتوى.',
            ],
            self::KEY_TERMS_INTRO => [
                'type' => 'string',
                'default' => 'باستخدامك لموقع {{site_name}} فإنك توافق على الالتزام بالشروط والأحكام التالية. نحتفظ بحق تحديث هذه الشروط في أي وقت، ويُعد استمرارك في استخدام الموقع موافقة على التعديلات.',
                'label' => 'مقدمة الصفحة',
                'section' => 'terms',
                'hint' => 'يمكنك استخدام {{site_name}} لاسم المتجر.',
            ],
            self::KEY_TERMS_SECTIONS => [
                'type' => 'json',
                'default' => '',
                'label' => 'أقسام الشروط',
                'section' => 'terms',
                'hint' => 'JSON: مصفوفة من {icon, title, content}',
            ],
            self::KEY_PRIVACY_HERO_TITLE => [
                'type' => 'string',
                'default' => 'سياسة الخصوصية',
                'label' => 'عنوان صفحة الخصوصية',
                'section' => 'privacy',
                'hint' => 'يظهر في هيرو الصفحة وعنوان المتصفح.',
            ],
            self::KEY_PRIVACY_HERO_SUBTITLE => [
                'type' => 'string',
                'default' => 'نلتزم بحماية بياناتك وشرح كيفية جمعها واستخدامها بشفافية تامة.',
                'label' => 'وصف الهيرو',
                'section' => 'privacy',
                'hint' => '',
            ],
            self::KEY_PRIVACY_LAST_UPDATED => [
                'type' => 'string',
                'default' => '',
                'label' => 'تاريخ آخر تحديث',
                'section' => 'privacy',
                'hint' => 'مثال: 20 مايو 2026 — يظهر للزوار أعلى المحتوى.',
            ],
            self::KEY_PRIVACY_INTRO => [
                'type' => 'string',
                'default' => 'توضّح سياسة الخصوصية هذه كيف يتعامل {{site_name}} مع بياناتك الشخصية عند زيارة الموقع أو إنشاء حساب أو إتمام عملية شراء. باستخدامك للموقع فإنك توافق على الممارسات الموضّحة أدناه.',
                'label' => 'مقدمة الصفحة',
                'section' => 'privacy',
                'hint' => 'يمكنك استخدام {{site_name}} لاسم المتجر.',
            ],
            self::KEY_PRIVACY_SECTIONS => [
                'type' => 'json',
                'default' => '',
                'label' => 'أقسام سياسة الخصوصية',
                'section' => 'privacy',
                'hint' => 'JSON: مصفوفة من {icon, title, content}',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function defaultHeroTypingWords(): array
    {
        return [
            'البرمجيات',
            'الاشتراكات الرقمية',
            'القوالب والأصول',
            'الكورسات الإلكترونية',
        ];
    }

    /**
     * @return list<array{icon: string, target: int, label: string}>
     */
    public static function defaultHeroStats(): array
    {
        return [
            ['icon' => 'fa-cloud-arrow-down', 'target' => 5000, 'label' => 'منتج رقمي'],
            ['icon' => 'fa-shield-halved', 'target' => 150, 'label' => 'دفع آمن'],
            ['icon' => 'fa-bolt', 'target' => 85000, 'label' => 'تسليم فوري'],
            ['icon' => 'fa-headset', 'target' => 15000, 'label' => 'دعم 24/7'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function heroKeys(): array
    {
        return [
            self::KEY_HERO_BADGE,
            self::KEY_HERO_TITLE_PREFIX,
            self::KEY_HERO_TYPING_WORDS,
            self::KEY_HERO_SUBTITLE,
            self::KEY_HERO_BTN_PRIMARY_LABEL,
            self::KEY_HERO_BTN_PRIMARY_URL,
            self::KEY_HERO_BTN_SECONDARY_LABEL,
            self::KEY_HERO_BTN_SECONDARY_URL,
            self::KEY_HERO_IMAGE,
            self::KEY_HERO_BG_MODE,
            self::KEY_HERO_BG_COLOR,
            self::KEY_HERO_BG_IMAGE,
            self::KEY_HERO_STATS,
        ];
    }

    /**
     * @return list<array{icon: string, title: string, text: string}>
     */
    public static function defaultAboutValues(): array
    {
        return [
            ['icon' => 'fa-gem', 'title' => 'جودة مضمونة', 'text' => 'منتجات أصلية مختارة بعناية من موردين موثوقين.'],
            ['icon' => 'fa-shield-halved', 'title' => 'دفع آمن', 'text' => 'بوابات دفع مشفرة وحماية كاملة لبياناتك.'],
            ['icon' => 'fa-bolt', 'title' => 'تسليم فوري', 'text' => 'استلم منتجك الرقمي مباشرة بعد إتمام الدفع.'],
            ['icon' => 'fa-headset', 'title' => 'دعم متواصل', 'text' => 'فريق دعم جاهز لمساعدتك قبل وبعد الشراء.'],
        ];
    }

    /**
     * @return list<array{icon: string, target: int, label: string}>
     */
    public static function defaultAboutStats(): array
    {
        return [
            ['icon' => 'fa-box', 'target' => 500, 'label' => 'منتج رقمي'],
            ['icon' => 'fa-users', 'target' => 12000, 'label' => 'عميل سعيد'],
            ['icon' => 'fa-star', 'target' => 4800, 'label' => 'تقييم إيجابي'],
            ['icon' => 'fa-clock', 'target' => 24, 'label' => 'دعم 24/7'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function aboutKeys(): array
    {
        return [
            self::KEY_ABOUT_HERO_TITLE,
            self::KEY_ABOUT_HERO_SUBTITLE,
            self::KEY_ABOUT_STORY_TITLE,
            self::KEY_ABOUT_STORY_TEXT_1,
            self::KEY_ABOUT_STORY_TEXT_2,
            self::KEY_ABOUT_STORY_IMAGE,
            self::KEY_ABOUT_VISION_TITLE,
            self::KEY_ABOUT_VISION_TEXT,
            self::KEY_ABOUT_MISSION_TITLE,
            self::KEY_ABOUT_MISSION_TEXT,
            self::KEY_ABOUT_VALUES,
            self::KEY_ABOUT_STATS,
            self::KEY_ABOUT_CTA_TITLE,
            self::KEY_ABOUT_CTA_TEXT,
            self::KEY_ABOUT_CTA_BTN_LABEL,
            self::KEY_ABOUT_CTA_BTN_URL,
        ];
    }

    /**
     * @return list<array{title: string, icon: string, items: list<array{question: string, answer: string}>}>
     */
    public static function defaultFaqGroups(): array
    {
        return [
            [
                'title' => 'الطلبات والدفع',
                'icon' => 'fa-credit-card',
                'items' => [
                    [
                        'question' => 'ما طرق الدفع المتاحة؟',
                        'answer' => 'نوفر عدة طرق دفع آمنة تشمل البطاقات البنكية، المحافظ الإلكترونية، والتحويل البنكي حسب ما هو متاح في متجرك.',
                    ],
                    [
                        'question' => 'هل معلومات الدفع آمنة؟',
                        'answer' => 'نعم، جميع المعاملات تتم عبر بوابات دفع مشفرة ولا نخزّن بيانات بطاقتك على خوادمنا.',
                    ],
                ],
            ],
            [
                'title' => 'التسليم والمنتجات الرقمية',
                'icon' => 'fa-cloud-arrow-down',
                'items' => [
                    [
                        'question' => 'متى أستلم منتجي الرقمي؟',
                        'answer' => 'يتم التسليم فوراً بعد تأكيد الدفع عبر البريد الإلكتروني أو من لوحة حسابك في قسم الطلبات.',
                    ],
                    [
                        'question' => 'أين أجد رابط التحميل؟',
                        'answer' => 'ستجد رابط التحميل أو تفاصيل المنتج في رسالة التأكيد وفي صفحة تفاصيل الطلب داخل حسابك.',
                    ],
                ],
            ],
            [
                'title' => 'الاسترجاع والضمان',
                'icon' => 'fa-shield-halved',
                'items' => [
                    [
                        'question' => 'هل يمكن استرجاع المنتجات الرقمية؟',
                        'answer' => 'نراجع طلبات الاسترجاع حسب سياسة المتجر وحالة المنتج. تواصل مع الدعم مع رقم الطلب لمساعدتك.',
                    ],
                    [
                        'question' => 'ماذا لو واجهت مشكلة في المنتج؟',
                        'answer' => 'فريق الدعم الفني متاح لحل أي مشكلة في التفعيل أو التحميل أو الاستخدام خلال فترة الضمان المحددة.',
                    ],
                ],
            ],
            [
                'title' => 'الحساب والدعم',
                'icon' => 'fa-headset',
                'items' => [
                    [
                        'question' => 'هل أحتاج حساباً للشراء؟',
                        'answer' => 'يمكنك الشراء كزائر، لكن إنشاء حساب يسهّل متابعة الطلبات وإعادة تحميل منتجاتك في أي وقت.',
                    ],
                    [
                        'question' => 'كيف أتواصل مع الدعم؟',
                        'answer' => 'عبر صفحة تواصل معنا أو البريد الإلكتروني وواتساب الموجودين في الموقع — نرد في أسرع وقت ممكن.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function faqKeys(): array
    {
        return [
            self::KEY_FAQ_HERO_TITLE,
            self::KEY_FAQ_HERO_SUBTITLE,
            self::KEY_FAQ_GROUPS,
            self::KEY_FAQ_CTA_TITLE,
            self::KEY_FAQ_CTA_TEXT,
            self::KEY_FAQ_CTA_BTN_LABEL,
            self::KEY_FAQ_CTA_BTN_URL,
        ];
    }

    /**
     * @return list<array{icon: string, title: string, content: string}>
     */
    public static function defaultTermsSections(): array
    {
        return [
            [
                'icon' => 'fa-user-check',
                'title' => 'قبول الشروط',
                'content' => "يُشكّل وصولك إلى الموقع أو إنشاء حساب أو إتمام طلب موافقةً على هذه الشروط.\nإذا لم توافق على أي بند، يرجى التوقف عن استخدام الخدمة.",
            ],
            [
                'icon' => 'fa-cart-shopping',
                'title' => 'الطلبات والمنتجات الرقمية',
                'content' => "جميع الأسعار المعروضة نهائية ما لم يُذكر خلاف ذلك.\nبعد تأكيد الدفع، يُسلَّم المنتج الرقمي وفق آلية التسليم المحددة في صفحة المنتج أو الطلب.\nنحتفظ بحق رفض أو إلغاء أي طلب عند الاشتباه في احتيال أو مخالفة للشروط.",
            ],
            [
                'icon' => 'fa-credit-card',
                'title' => 'الدفع والفوترة',
                'content' => "يجب تقديم معلومات دفع صحيحة ومحدّثة.\nجميع المعاملات تتم عبر بوابات دفع آمنة، ولا نخزّن بيانات بطاقتك الكاملة على خوادمنا.\nقد تُطبَّق رسوم بنكية أو تحويل حسب مزود الدفع أو البنك.",
            ],
            [
                'icon' => 'fa-rotate-left',
                'title' => 'الاسترجاع والاسترداد',
                'content' => "تخضع طلبات الاسترجاع لسياسة المتجر المعمول بها وقت الشراء.\nللمنتجات الرقمية، قد لا يُقبل الاسترجاع بعد التسليم أو التفعيل إلا في حالات العيوب المثبتة أو الخطأ من جانبنا.\nيتم مراجعة طلبات الاسترداد خلال مدة معقولة وإبلاغك بالقرار.",
            ],
            [
                'icon' => 'fa-copyright',
                'title' => 'الملكية الفكرية',
                'content' => "جميع محتويات الموقع — شعار، تصميم، نصوص، وواجهات — محمية بموجب قوانين الملكية الفكرية.\nالمنتجات الرقمية المباعة تُستخدم وفق ترخيص الاستخدام المرفق بكل منتج ولا يجوز إعادة بيعها أو توزيعها دون إذن.",
            ],
            [
                'icon' => 'fa-scale-balanced',
                'title' => 'حدود المسؤولية',
                'content' => "نبذل جهدنا لتوفير منتجات أصلية وخدمة موثوقة، لكننا لا نضمن خلو الخدمة من انقطاعات تقنية نادرة.\nلا نتحمل مسؤولية الأضرار غير المباشرة الناتجة عن استخدام المنتجات الرقمية في غير الغرض المخصص لها.",
            ],
            [
                'icon' => 'fa-envelope',
                'title' => 'التواصل',
                'content' => "لأي استفسار حول هذه الشروط، يرجى التواصل معنا عبر صفحة «تواصل معنا» أو البريد الإلكتروني الرسمي للمتجر.",
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function termsKeys(): array
    {
        return [
            self::KEY_TERMS_HERO_TITLE,
            self::KEY_TERMS_HERO_SUBTITLE,
            self::KEY_TERMS_LAST_UPDATED,
            self::KEY_TERMS_INTRO,
            self::KEY_TERMS_SECTIONS,
        ];
    }

    /**
     * @return list<array{icon: string, title: string, content: string}>
     */
    public static function defaultPrivacySections(): array
    {
        return [
            [
                'icon' => 'fa-database',
                'title' => 'البيانات التي نجمعها',
                'content' => "قد نجمع: الاسم، البريد الإلكتروني، رقم الهاتف، عنوان الفوترة، وسجل الطلبات.\nنقوم أيضاً بجمع بيانات تقنية مثل عنوان IP ونوع المتصفح لتحسين الأمان وتجربة الاستخدام.",
            ],
            [
                'icon' => 'fa-gears',
                'title' => 'كيف نستخدم بياناتك',
                'content' => "نستخدم بياناتك لمعالجة الطلبات، تسليم المنتجات الرقمية، تقديم الدعم الفني، وإرسال تحديثات مهمة عن حسابك أو طلباتك.\nقد نستخدم بيانات مجمّعة لتحسين أداء المتجر دون التعرّف على هويتك شخصياً.",
            ],
            [
                'icon' => 'fa-cookie-bite',
                'title' => 'ملفات تعريف الارتباط',
                'content' => "نستخدم ملفات تعريف الارتباط (Cookies) لتذكّر تفضيلاتك، مثل اللغة والسلة، ولأغراض أمنية.\nيمكنك التحكم في ملفات تعريف الارتباط من إعدادات المتصفح، لكن تعطيل بعضها قد يؤثر على وظائف الموقع.",
            ],
            [
                'icon' => 'fa-share-nodes',
                'title' => 'مشاركة البيانات',
                'content' => "لا نبيع بياناتك الشخصية لأطراف ثالثة.\nقد نشارك الحد الأدنى من البيانات مع مزودي الدفع وخدمات الاستضافة والبريد الإلكتروني فقط لتنفيذ الخدمة وفق اتفاقيات سرية.",
            ],
            [
                'icon' => 'fa-lock',
                'title' => 'حماية البيانات',
                'content' => "نطبّق إجراءات تقنية وتنظيمية مناسبة لحماية بياناتك من الوصول غير المصرّح به أو التعديل أو الإفشاء.\nرغم ذلك، لا يمكن ضمان أمان مطلق لأي نقل عبر الإنترنت.",
            ],
            [
                'icon' => 'fa-user-shield',
                'title' => 'حقوقك',
                'content' => "يحق لك طلب الوصول إلى بياناتك أو تصحيحها أو حذفها ضمن ما يسمح به القانون.\nللممارسة هذه الحقوق، تواصل معنا عبر صفحة «تواصل معنا» مع ذكر البريد المرتبط بحسابك.",
            ],
            [
                'icon' => 'fa-envelope',
                'title' => 'التواصل بخصوص الخصوصية',
                'content' => "لأي استفسار حول سياسة الخصوصية أو طلب متعلق ببياناتك، يرجى التواصل معنا عبر قنوات الدعم الرسمية للمتجر.",
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function privacyKeys(): array
    {
        return [
            self::KEY_PRIVACY_HERO_TITLE,
            self::KEY_PRIVACY_HERO_SUBTITLE,
            self::KEY_PRIVACY_LAST_UPDATED,
            self::KEY_PRIVACY_INTRO,
            self::KEY_PRIVACY_SECTIONS,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function schemaForSection(string $section): array
    {
        return array_filter(
            self::schema(),
            fn (array $def) => ($def['section'] ?? '') === $section
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function heroValidationRules(): array
    {
        $all = self::validationRules();
        $rules = array_intersect_key($all, array_flip(self::heroKeys()));

        return $rules + [
            'hero_image_file' => $all['hero_image_file'],
            'hero_bg_image_file' => $all['hero_bg_image_file'],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function aboutValidationRules(): array
    {
        $all = self::validationRules();
        $rules = array_intersect_key($all, array_flip(self::aboutKeys()));

        return $rules + [
            'about_story_image_file' => $all['about_story_image_file'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function saveAboutSettings(array $validated, ?\Illuminate\Http\UploadedFile $storyImage = null): void
    {
        if ($storyImage) {
            $this->storeUpload(self::KEY_ABOUT_STORY_IMAGE, $storyImage);
        }

        if (isset($validated[self::KEY_ABOUT_VALUES])) {
            $values = self::parseAboutValuesInput((string) $validated[self::KEY_ABOUT_VALUES]);
            $validated[self::KEY_ABOUT_VALUES] = json_encode($values, JSON_UNESCAPED_UNICODE);
        }
        if (isset($validated[self::KEY_ABOUT_STATS])) {
            $stats = self::parseHeroStatsInput((string) $validated[self::KEY_ABOUT_STATS]);
            $validated[self::KEY_ABOUT_STATS] = json_encode($stats, JSON_UNESCAPED_UNICODE);
        }

        $payload = array_intersect_key($validated, array_flip(self::aboutKeys()));
        $this->setMany($payload);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function faqValidationRules(): array
    {
        $all = self::validationRules();

        return array_intersect_key($all, array_flip(self::faqKeys()));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function saveFaqSettings(array $validated): void
    {
        if (isset($validated[self::KEY_FAQ_GROUPS])) {
            $groups = self::parseFaqGroupsInput((string) $validated[self::KEY_FAQ_GROUPS]);
            $validated[self::KEY_FAQ_GROUPS] = json_encode($groups, JSON_UNESCAPED_UNICODE);
        }

        $payload = array_intersect_key($validated, array_flip(self::faqKeys()));
        $this->setMany($payload);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function termsValidationRules(): array
    {
        $all = self::validationRules();

        return array_intersect_key($all, array_flip(self::termsKeys()));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function saveTermsSettings(array $validated): void
    {
        if (isset($validated[self::KEY_TERMS_SECTIONS])) {
            $sections = self::parseTermsSectionsInput((string) $validated[self::KEY_TERMS_SECTIONS]);
            $validated[self::KEY_TERMS_SECTIONS] = json_encode($sections, JSON_UNESCAPED_UNICODE);
        }

        $payload = array_intersect_key($validated, array_flip(self::termsKeys()));
        $this->setMany($payload);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function privacyValidationRules(): array
    {
        $all = self::validationRules();

        return array_intersect_key($all, array_flip(self::privacyKeys()));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function savePrivacySettings(array $validated): void
    {
        if (isset($validated[self::KEY_PRIVACY_SECTIONS])) {
            $sections = self::parsePrivacySectionsInput((string) $validated[self::KEY_PRIVACY_SECTIONS]);
            $validated[self::KEY_PRIVACY_SECTIONS] = json_encode($sections, JSON_UNESCAPED_UNICODE);
        }

        $payload = array_intersect_key($validated, array_flip(self::privacyKeys()));
        $this->setMany($payload);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function saveHeroSettings(array $validated, ?\Illuminate\Http\UploadedFile $heroImage = null, ?\Illuminate\Http\UploadedFile $heroBgImage = null): void
    {
        if ($heroImage) {
            $this->storeUpload(self::KEY_HERO_IMAGE, $heroImage);
        }
        if ($heroBgImage) {
            $this->storeUpload(self::KEY_HERO_BG_IMAGE, $heroBgImage);
        }

        if (isset($validated[self::KEY_HERO_TYPING_WORDS])) {
            $words = self::parseTypingWordsInput((string) $validated[self::KEY_HERO_TYPING_WORDS]);
            $validated[self::KEY_HERO_TYPING_WORDS] = json_encode($words, JSON_UNESCAPED_UNICODE);
        }
        if (isset($validated[self::KEY_HERO_STATS])) {
            $stats = self::parseHeroStatsInput((string) $validated[self::KEY_HERO_STATS]);
            $validated[self::KEY_HERO_STATS] = json_encode($stats, JSON_UNESCAPED_UNICODE);
        }

        $payload = array_intersect_key($validated, array_flip(self::heroKeys()));
        $this->setMany($payload);
    }

    public static function sectionLabels(): array
    {
        return [
            'general' => 'عام',
            'branding' => 'الهوية والعلامة',
            'homepage' => 'الصفحة الرئيسية',
            'about' => 'صفحة من نحن',
            'faq' => 'الأسئلة الشائعة',
            'terms' => 'الشروط والأحكام',
            'privacy' => 'سياسة الخصوصية',
            'contact' => 'التواصل',
            'maintenance' => 'وضع الصيانة',
            'locale' => 'اللغة والمنطقة',
            'seo' => 'SEO والتذييل',
            'social' => 'وسائل التواصل',
        ];
    }

    /**
     * Normalize hero typing words from admin textarea (lines or JSON).
     *
     * @return list<string>
     */
    public static function parseTypingWordsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultHeroTypingWords();
        }
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('strval', $decoded)));
            }
        }
        $lines = preg_split('/\r\n|\r|\n/', $input) ?: [];

        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * @return list<array{icon: string, target: int, label: string}>
     */
    public static function parseHeroStatsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultHeroStats();
        }
        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return self::defaultHeroStats();
        }
        $out = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            $out[] = [
                'icon' => (string) ($item['icon'] ?? 'fa-star'),
                'target' => (int) ($item['target'] ?? 0),
                'label' => (string) ($item['label'] ?? ''),
            ];
        }

        return $out ?: self::defaultHeroStats();
    }

    /**
     * @return list<array{icon: string, title: string, text: string}>
     */
    public static function parseAboutValuesInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultAboutValues();
        }
        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return self::defaultAboutValues();
        }
        $out = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            $out[] = [
                'icon' => (string) ($item['icon'] ?? 'fa-star'),
                'title' => (string) ($item['title'] ?? ''),
                'text' => (string) ($item['text'] ?? ''),
            ];
        }

        return $out ?: self::defaultAboutValues();
    }

    /**
     * @return list<array{title: string, icon: string, items: list<array{question: string, answer: string}>}>
     */
    public static function parseFaqGroupsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultFaqGroups();
        }
        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return self::defaultFaqGroups();
        }
        $out = [];
        foreach ($decoded as $group) {
            if (! is_array($group)) {
                continue;
            }
            $items = [];
            foreach ($group['items'] ?? [] as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $question = trim((string) ($item['question'] ?? ''));
                $answer = trim((string) ($item['answer'] ?? ''));
                if ($question === '' && $answer === '') {
                    continue;
                }
                $items[] = [
                    'question' => $question,
                    'answer' => $answer,
                ];
            }
            $title = trim((string) ($group['title'] ?? ''));
            if ($title === '' && $items === []) {
                continue;
            }
            $out[] = [
                'title' => $title !== '' ? $title : 'أسئلة عامة',
                'icon' => (string) ($group['icon'] ?? 'fa-circle-question'),
                'items' => $items,
            ];
        }

        return $out ?: self::defaultFaqGroups();
    }

    /**
     * @return list<array{icon: string, title: string, content: string}>
     */
    public static function parseTermsSectionsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultTermsSections();
        }
        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return self::defaultTermsSections();
        }
        $out = [];
        foreach ($decoded as $section) {
            if (! is_array($section)) {
                continue;
            }
            $title = trim((string) ($section['title'] ?? ''));
            $content = trim((string) ($section['content'] ?? ''));
            if ($title === '' && $content === '') {
                continue;
            }
            $out[] = [
                'icon' => (string) ($section['icon'] ?? 'fa-file-lines'),
                'title' => $title !== '' ? $title : 'قسم',
                'content' => $content,
            ];
        }

        return $out ?: self::defaultTermsSections();
    }

    /**
     * @return list<array{icon: string, title: string, content: string}>
     */
    public static function parsePrivacySectionsInput(string $input): array
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return self::defaultPrivacySections();
        }
        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return self::defaultPrivacySections();
        }
        $out = [];
        foreach ($decoded as $section) {
            if (! is_array($section)) {
                continue;
            }
            $title = trim((string) ($section['title'] ?? ''));
            $content = trim((string) ($section['content'] ?? ''));
            if ($title === '' && $content === '') {
                continue;
            }
            $out[] = [
                'icon' => (string) ($section['icon'] ?? 'fa-file-lines'),
                'title' => $title !== '' ? $title : 'قسم',
                'content' => $content,
            ];
        }

        return $out ?: self::defaultPrivacySections();
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $schema = self::schema();
            $out = [];
            foreach (array_keys($schema) as $key) {
                $def = $schema[$key];
                $value = SystemSetting::getValue($key, $def['default']);
                $out[$key] = $this->castValue($value, $def['type']);
            }
            return $out;
        });
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setMany(array $data): void
    {
        $schema = self::schema();
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $schema)) {
                continue;
            }
            $def = $schema[$key];
            $type = $def['type'];
            if ($type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            } elseif (in_array($type, ['json', 'array'], true)) {
                $value = is_array($value)
                    ? json_encode($value, JSON_UNESCAPED_UNICODE)
                    : (string) $value;
            } else {
                $value = (string) $value;
            }
            SystemSetting::set($key, $value, $type, self::GROUP);
        }
        $this->clearCache();
    }

    public function storeUpload(string $key, $file): ?string
    {
        $schema = self::schema();
        if (!isset($schema[$key]) || !$file || !$file->isValid()) {
            return null;
        }

        $storageHelper = app(StorageHelperService::class);
        $disk = $storageHelper->mediaDisk();
        $oldPath = $this->get($key);
        if ($oldPath) {
            $storageHelper->deleteMedia($disk, $oldPath);
        }

        $path = $storageHelper->storeUploadedFileWithFailover(
            $disk,
            'site-settings',
            $file,
            'image'
        );

        if (! $path) {
            return null;
        }

        SystemSetting::set($key, $path, $schema[$key]['type'], self::GROUP);
        $this->clearCache();

        return $path;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => is_string($value) && $value !== ''
                ? json_decode($value, true)
                : (is_array($value) ? $value : null),
            default => (string) $value,
        };
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function validationRules(): array
    {
        return [
            self::KEY_SITE_NAME => ['nullable', 'string', 'max:255'],
            self::KEY_SITE_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_LOGO => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_FAVICON => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_ACCENT_COLOR => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            self::KEY_SITE_MAINTENANCE_MODE => ['nullable', 'boolean'],
            self::KEY_SITE_MAINTENANCE_MESSAGE => ['nullable', 'string', 'max:1000'],
            self::KEY_SITE_CONTACT_EMAIL => ['nullable', 'email', 'max:255'],
            self::KEY_SITE_CONTACT_PHONE => ['nullable', 'string', 'max:50'],
            self::KEY_SITE_ADDRESS => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_WHATSAPP_NUMBER => ['nullable', 'string', 'max:20'],
            self::KEY_SITE_TIMEZONE => ['nullable', 'string', 'max:50'],
            self::KEY_SITE_LOCALE => ['nullable', 'string', 'max:10'],
            self::KEY_SITE_META_KEYWORDS => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_META_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_FOOTER_TEXT => ['nullable', 'string', 'max:2000'],
            self::KEY_SITE_FACEBOOK_URL => ['nullable', 'url', 'max:500'],
            self::KEY_SITE_TWITTER_URL => ['nullable', 'url', 'max:500'],
            self::KEY_SITE_INSTAGRAM_URL => ['nullable', 'url', 'max:500'],
            'site_logo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
            'site_favicon_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg,ico', 'max:1024'],
            self::KEY_HERO_BADGE => ['nullable', 'string', 'max:255'],
            self::KEY_HERO_TITLE_PREFIX => ['nullable', 'string', 'max:255'],
            self::KEY_HERO_TYPING_WORDS => ['nullable', 'string', 'max:5000'],
            self::KEY_HERO_SUBTITLE => ['nullable', 'string', 'max:2000'],
            self::KEY_HERO_BTN_PRIMARY_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_HERO_BTN_PRIMARY_URL => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_BTN_SECONDARY_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_HERO_BTN_SECONDARY_URL => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_IMAGE => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_BG_MODE => ['nullable', 'string', 'in:gradient,color,image'],
            self::KEY_HERO_BG_COLOR => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            self::KEY_HERO_BG_IMAGE => ['nullable', 'string', 'max:500'],
            self::KEY_HERO_STATS => ['nullable', 'string', 'max:10000'],
            'hero_image_file' => ['nullable', 'image', 'max:4096'],
            'hero_bg_image_file' => ['nullable', 'image', 'max:4096'],
            self::KEY_ABOUT_HERO_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_ABOUT_HERO_SUBTITLE => ['nullable', 'string', 'max:2000'],
            self::KEY_ABOUT_STORY_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_ABOUT_STORY_TEXT_1 => ['nullable', 'string', 'max:5000'],
            self::KEY_ABOUT_STORY_TEXT_2 => ['nullable', 'string', 'max:5000'],
            self::KEY_ABOUT_STORY_IMAGE => ['nullable', 'string', 'max:500'],
            self::KEY_ABOUT_VISION_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_ABOUT_VISION_TEXT => ['nullable', 'string', 'max:5000'],
            self::KEY_ABOUT_MISSION_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_ABOUT_MISSION_TEXT => ['nullable', 'string', 'max:5000'],
            self::KEY_ABOUT_VALUES => ['nullable', 'string', 'max:10000'],
            self::KEY_ABOUT_STATS => ['nullable', 'string', 'max:10000'],
            self::KEY_ABOUT_CTA_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_ABOUT_CTA_TEXT => ['nullable', 'string', 'max:2000'],
            self::KEY_ABOUT_CTA_BTN_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_ABOUT_CTA_BTN_URL => ['nullable', 'string', 'max:500'],
            'about_story_image_file' => ['nullable', 'image', 'max:4096'],
            self::KEY_FAQ_HERO_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_FAQ_HERO_SUBTITLE => ['nullable', 'string', 'max:2000'],
            self::KEY_FAQ_GROUPS => ['nullable', 'string', 'max:50000'],
            self::KEY_FAQ_CTA_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_FAQ_CTA_TEXT => ['nullable', 'string', 'max:2000'],
            self::KEY_FAQ_CTA_BTN_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_FAQ_CTA_BTN_URL => ['nullable', 'string', 'max:500'],
            self::KEY_TERMS_HERO_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_TERMS_HERO_SUBTITLE => ['nullable', 'string', 'max:2000'],
            self::KEY_TERMS_LAST_UPDATED => ['nullable', 'string', 'max:100'],
            self::KEY_TERMS_INTRO => ['nullable', 'string', 'max:5000'],
            self::KEY_TERMS_SECTIONS => ['nullable', 'string', 'max:50000'],
            self::KEY_PRIVACY_HERO_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_PRIVACY_HERO_SUBTITLE => ['nullable', 'string', 'max:2000'],
            self::KEY_PRIVACY_LAST_UPDATED => ['nullable', 'string', 'max:100'],
            self::KEY_PRIVACY_INTRO => ['nullable', 'string', 'max:5000'],
            self::KEY_PRIVACY_SECTIONS => ['nullable', 'string', 'max:50000'],
        ];
    }
}
