        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="header-logo">
                    <svg class="desktop-logo" width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
                        <text x="60" y="25" font-family="Arial, sans-serif" font-size="13" font-weight="bold" fill="#4f46e5" text-anchor="middle">روفيا لينك</text>
                    </svg>
                    <svg class="toggle-logo" width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <text x="20" y="25" font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="#4f46e5" text-anchor="middle">RL</text>
                    </svg>
                    <svg class="desktop-white" width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
                        <text x="60" y="25" font-family="Arial, sans-serif" font-size="13" font-weight="bold" fill="#1f2937" text-anchor="middle">روفيا لينك</text>
                    </svg>
                    <svg class="toggle-white" width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <text x="20" y="25" font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="#1f2937" text-anchor="middle">RL</text>
                    </svg>
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                    </div>
                    <ul class="main-menu">
                        @php
                            $catalogActive = request()->routeIs(
                                'admin.categories.*', 'admin.brands.*', 'admin.products.*', 'admin.attributes.*'
                            );
                            $salesActive = request()->routeIs(
                                'admin.orders.*', 'admin.order-returns.*', 'admin.customers.*', 'admin.wishlists.*'
                            );
                            $marketingActive = request()->routeIs(
                                'admin.reviews.*', 'admin.review-settings.*', 'admin.coupons.*', 'admin.loyalty.*'
                            );
                            $financeActive = request()->routeIs(
                                'admin.payment-methods.*', 'admin.payments.settings.*', 'admin.payments.*', 'admin.tax.*',
                                'admin.reports.*'
                            );
                            $storeActive = $catalogActive || $salesActive || $marketingActive || $financeActive;

                            $frontendActive = request()->routeIs('admin.homepage.*', 'admin.site-settings.*');

                            $contentActive = request()->routeIs('admin.blog.*');

                            $integrationsActive = request()->routeIs('admin.whatsapp*');

                            $usersActive = request()->routeIs('admin.roles.*', 'admin.users.*');
                            $generalActive = request()->routeIs('admin.system-status.*', 'admin.activity-log.*');
                            $mailActive = request()->routeIs('admin.settings.email.*', 'admin.email-templates.*');
                            $storageActive = request()->routeIs(
                                'admin.storage.*', 'admin.storage-disk-mappings.*'
                            );
                            $backupActive = request()->routeIs(
                                'admin.backups.*', 'admin.backup-schedules.*', 'admin.backup-storage.*'
                            );
                            $aiActive = request()->routeIs('admin.ai.*');
                            $systemActive = $usersActive || $generalActive || $mailActive || $storageActive || $backupActive || $aiActive;
                        @endphp

                        {{-- لوحة التحكم --}}
                        <li class="slide {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
                                <span class="side-menu__label">لوحة التحكم</span>
                            </a>
                        </li>

                        <li class="slide">
                            <a href="{{ route('frontend.home') }}" class="side-menu__item" target="_blank" rel="noopener noreferrer" title="فتح المتجر في تبويب جديد">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                <span class="side-menu__label">الواجهة الخارجية</span>
                                <i class="fe fe-external-link side-menu__angle opacity-75" style="font-size: 0.85rem;"></i>
                            </a>
                        </li>

                        {{-- المتجر --}}
                        <li class="slide has-sub {{ $storeActive ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <span class="side-menu__label">المتجر</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide has-sub {{ $catalogActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">الكتالوج</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.categories.index') }}" class="side-menu__item">التصنيفات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.brands.index') }}" class="side-menu__item">العلامات التجارية</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.products.index') }}" class="side-menu__item">المنتجات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.attributes.index') }}" class="side-menu__item">سمات المنتجات</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $salesActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">الطلبات والعملاء</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.orders.index') }}" class="side-menu__item">الطلبات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.order-returns.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.order-returns.index') }}" class="side-menu__item">طلبات المرتجع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.customers.index') }}" class="side-menu__item">العملاء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.wishlists.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.wishlists.index') }}" class="side-menu__item">المفضلة</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $marketingActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">التقييمات والعروض</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.reviews.*') && ! request()->routeIs('admin.review-settings.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reviews.index') }}" class="side-menu__item">آراء العملاء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.review-settings.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.review-settings.index') }}" class="side-menu__item">إعدادات التقييمات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.coupons.index') }}" class="side-menu__item">الكوبونات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.loyalty.settings.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.loyalty.settings.index') }}" class="side-menu__item">إعدادات نقاط الولاء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.loyalty.transactions.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.loyalty.transactions.index') }}" class="side-menu__item">سجل نقاط الولاء</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $financeActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">الدفع والتقارير</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.payment-methods.index') }}" class="side-menu__item">وسائل الدفع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.payments.settings.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.payments.settings.index') }}" class="side-menu__item">إعدادات الدفع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.payments.index', 'admin.payments.show', 'admin.payments.webhooks', 'admin.payments.confirm', 'admin.payments.reject', 'admin.payments.refund') ? 'active' : '' }}">
                                            <a href="{{ route('admin.payments.index') }}" class="side-menu__item">المدفوعات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.tax.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.tax.index') }}" class="side-menu__item">فئات ومعدلات الضرائب</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.dashboard') }}" class="side-menu__item">تقارير المتجر</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        {{-- الواجهة الأمامية --}}
                        <li class="slide has-sub {{ $frontendActive ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                <span class="side-menu__label">الواجهة الأمامية</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.homepage.index') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.index') }}" class="side-menu__item">لوحة الواجهة</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.homepage.hero.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.hero.edit') }}" class="side-menu__item">هيرو الصفحة الرئيسية</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.homepage.about.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.about.edit') }}" class="side-menu__item">صفحة من نحن</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.homepage.faq.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.faq.edit') }}" class="side-menu__item">الأسئلة الشائعة</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.homepage.terms.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.terms.edit') }}" class="side-menu__item">الشروط والأحكام</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.homepage.privacy.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.privacy.edit') }}" class="side-menu__item">سياسة الخصوصية</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.site-settings.index') }}" class="side-menu__item">إعدادات الموقع</a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('frontend.home') }}" class="side-menu__item" target="_blank" rel="noopener noreferrer">معاينة المتجر</a>
                                </li>
                            </ul>
                        </li>

                        {{-- المحتوى --}}
                        <li class="slide has-sub {{ $contentActive ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                <span class="side-menu__label">المحتوى</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.posts.index') }}" class="side-menu__item">المقالات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.blog.ai-posts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.ai-posts.create') }}" class="side-menu__item">مقال بالذكاء الاصطناعي</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.categories.index') }}" class="side-menu__item">تصنيفات المدونة</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.blog.tags.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.tags.index') }}" class="side-menu__item">وسوم المدونة</a>
                                </li>
                            </ul>
                        </li>

                        {{-- التواصل --}}
                        <li class="slide has-sub {{ $integrationsActive ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/></svg>
                                <span class="side-menu__label">التواصل</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.whatsapp-messages.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-messages.index') }}" class="side-menu__item">رسائل واتساب</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.whatsapp-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-settings.index') }}" class="side-menu__item">إعدادات Meta API</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.whatsapp-web.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-web.connect') }}" class="side-menu__item">واتساب ويب</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.whatsapp-web-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-web-settings.index') }}" class="side-menu__item">إعدادات واتساب ويب</a>
                                </li>
                            </ul>
                        </li>

                        {{-- النظام والإعدادات --}}
                        <li class="slide has-sub {{ $systemActive ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                <span class="side-menu__label">النظام والإعدادات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide has-sub {{ $usersActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">المستخدمون والصلاحيات</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.roles.index') }}" class="side-menu__item">الصلاحيات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.users.index') }}" class="side-menu__item">المستخدمون</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $generalActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">المراقبة والسجلات</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.system-status.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.system-status.index') }}" class="side-menu__item">حالة النظام</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.activity-log.index') }}" class="side-menu__item">سجل النشاط</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $mailActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">البريد الإلكتروني</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.settings.email.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.settings.email.index') }}" class="side-menu__item">إعدادات SMTP</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.email-templates.index') }}" class="side-menu__item">قوالب الإيميلات</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $storageActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">التخزين السحابي</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.storage.index', 'admin.storage.edit', 'admin.storage.update', 'admin.storage.destroy', 'admin.storage.test') ? 'active' : '' }}">
                                            <a href="{{ route('admin.storage.index') }}" class="side-menu__item">أماكن التخزين</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.storage.create', 'admin.storage.store') ? 'active' : '' }}">
                                            <a href="{{ route('admin.storage.create') }}" class="side-menu__item">إضافة مكان تخزين</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.storage.analytics') ? 'active' : '' }}">
                                            <a href="{{ route('admin.storage.analytics') }}" class="side-menu__item">إحصائيات التخزين</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.storage-disk-mappings.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.storage-disk-mappings.index') }}" class="side-menu__item">ربط الأقراص</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $backupActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">النسخ الاحتياطي</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.backups.index', 'admin.backups.show', 'admin.backups.edit', 'admin.backups.store') ? 'active' : '' }}">
                                            <a href="{{ route('admin.backups.index') }}" class="side-menu__item">قائمة النسخ</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.backups.create') ? 'active' : '' }}">
                                            <a href="{{ route('admin.backups.create') }}" class="side-menu__item">إنشاء نسخة</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.backup-schedules.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.backup-schedules.index') }}" class="side-menu__item">جداول النسخ</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.backup-storage.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.backup-storage.index') }}" class="side-menu__item">إعدادات النسخ</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="slide has-sub {{ $aiActive ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <span class="side-menu__label">الذكاء الاصطناعي</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        <li class="slide {{ request()->routeIs('admin.ai.models.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.ai.models.index') }}" class="side-menu__item">نماذج AI</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.ai.settings.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.ai.settings.index') }}" class="side-menu__item">إعدادات AI</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                    </ul>
                    <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
                </nav>
                <!-- End::nav -->

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->
