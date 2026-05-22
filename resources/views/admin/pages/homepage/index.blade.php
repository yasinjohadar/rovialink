@extends('admin.layouts.master')

@section('page-title')
    الواجهة الأمامية
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">إدارة الواجهة الأمامية</h5>
                    <p class="text-muted mb-0 small">تعديل محتوى الصفحة الرئيسية وإعدادات الموقع الظاهرة للزوار.</p>
                </div>
                <a href="{{ route('frontend.home') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                    <i class="fe fe-external-link me-1"></i> معاينة المتجر
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card custom-card h-100 border-primary border-opacity-25">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="avatar avatar-lg rounded bg-primary-transparent text-primary">
                                    <i class="fe fe-image fs-20"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1">هيرو الصفحة الرئيسية</h6>
                                    <p class="text-muted small mb-0">العنوان، الوصف، الصورة، الخلفية، الأزرار، والإحصائيات.</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.homepage.hero.edit') }}" class="btn btn-primary mt-auto">تعديل الهيرو</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card custom-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="avatar avatar-lg rounded bg-secondary-transparent text-secondary">
                                    <i class="fe fe-settings fs-20"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1">إعدادات الموقع</h6>
                                    <p class="text-muted small mb-0">الاسم، الشعار، الألوان، التواصل، SEO، ووسائل التواصل.</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.site-settings.index') }}" class="btn btn-outline-primary mt-auto">فتح الإعدادات</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card custom-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="avatar avatar-lg rounded bg-success-transparent text-success">
                                    <i class="fe fe-globe fs-20"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1">المتجر للزوار</h6>
                                    <p class="text-muted small mb-0">معاينة الصفحة الرئيسية والمتجر كما يراها العميل.</p>
                                </div>
                            </div>
                            <a href="{{ route('frontend.home') }}" class="btn btn-outline-success mt-auto" target="_blank" rel="noopener">فتح المتجر</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
