@extends('admin.layouts.master')

@section('page-title')
لوحة التحكم
@stop

@section('content')
  <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Start::page-header -->
                <div class="page-header">
                    <div>
                        <h3 class="page-title">مرحباً بك في لوحة التحكم</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active" aria-current="page">الرئيسية</li>
                        </ol>
                    </div>
                </div>
                <!-- End::page-header -->

                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-2">التصنيفات</p>
                                        <h4 class="mb-0 number-font">{{ \App\Models\Category::count() }}</h4>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="bg-primary-transparent rounded-circle p-2">
                                            <svg class="feather feather-grid" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-2">آراء العملاء</p>
                                        <h4 class="mb-0 number-font">{{ \App\Models\Review::count() }}</h4>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="bg-primary-transparent rounded-circle p-2">
                                            <svg class="feather feather-message-square" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-2">المستخدمون</p>
                                        <h4 class="mb-0 number-font">{{ \App\Models\User::count() }}</h4>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="bg-primary-transparent rounded-circle p-2">
                                            <svg class="feather feather-users" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-2">مقالات المدونة</p>
                                        <h4 class="mb-0 number-font">{{ \App\Models\BlogPost::count() }}</h4>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="bg-primary-transparent rounded-circle p-2">
                                            <svg class="feather feather-file-text" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End::row-1 -->

                <!-- Start::row-2 -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    نظرة سريعة على آراء العملاء
                                </div>
                            </div>
                            <div class="card-body">
                                @if($recentReviews = \App\Models\Review::latest()->take(5)->get())
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>المستخدم</th>
                                                <th>التقييم</th>
                                                <th>التعليق</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentReviews as $review)
                                            <tr>
                                                <td>{{ $review->user->name ?? 'مستخدم مجهول' }}</td>
                                                <td>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::limit($review->comment, 50) }}</td>
                                                <td>
                                                    @switch($review->status)
                                                        @case('approved')
                                                            <span class="badge bg-success">مقبول</span>
                                                            @break
                                                        @case('rejected')
                                                            <span class="badge bg-danger">مرفوض</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-warning">في الانتظار</span>
                                                    @endswitch
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <p>لا توجد آراء حالياً</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    إجراءات سريعة
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg class="feather feather-folder" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">إدارة التصنيفات</h6>
                                                <p class="text-muted mb-0">إضافة وتعديل تصنيفات المنتجات</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.reviews.index') }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg class="feather feather-message-square" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">مراجعة آراء العملاء</h6>
                                                <p class="text-muted mb-0">قبول أو رفض الآراء الواردة</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.blog.posts.index') }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg class="feather feather-file-text" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">إدارة المدونة</h6>
                                                <p class="text-muted mb-0">كتابة ونشر المقالات</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End::row-2 -->

            </div>
        </div>
        <!-- End::app-content -->
@stop