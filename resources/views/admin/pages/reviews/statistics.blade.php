@extends('admin.layouts.master')

@section('page-title')
    إحصائيات آراء العملاء
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إحصائيات آراء العملاء</h5>
                </div>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                    العودة للقائمة
                </a>
            </div>

            <div class="row">
                <!-- إحصائيات عامة -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-primary text-white rounded">
                                        <i class="bi bi-chat-dots fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">إجمالي الآراء</h6>
                                    <h3 class="mb-0">{{ $totalReviews }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-success text-white rounded">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">معتمد</h6>
                                    <h3 class="mb-0">{{ $approvedReviews }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-warning text-white rounded">
                                        <i class="bi bi-clock fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">في الانتظار</h6>
                                    <h3 class="mb-0">{{ $pendingReviews }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-info text-white rounded">
                                        <i class="bi bi-star fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">متوسط التقييم</h6>
                                    <h3 class="mb-0">{{ number_format($averageRating, 1) }}/5</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- توزيع التقييمات -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">توزيع التقييمات</h6>
                        </div>
                        <div class="card-body">
                            @foreach($ratingDistribution as $rating)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>
                                            {{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}
                                            ({{ $rating->rating }} نجوم)
                                        </span>
                                        <span><strong>{{ $rating->count }}</strong> رأي</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $totalReviews > 0 ? ($rating->count / $totalReviews * 100) : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- إحصائيات إضافية -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">إحصائيات إضافية</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>الآراء المرفوضة</th>
                                    <td><span class="badge bg-danger">{{ $rejectedReviews }}</span></td>
                                </tr>
                                <tr>
                                    <th>محتوى غير مرغوب</th>
                                    <td><span class="badge bg-secondary">{{ $spamReviews }}</span></td>
                                </tr>
                                <tr>
                                    <th>شراء موثق</th>
                                    <td><span class="badge bg-success">{{ $verifiedReviews }}</span></td>
                                </tr>
                                <tr>
                                    <th>آراء مميزة</th>
                                    <td><span class="badge bg-warning text-dark">{{ $featuredReviews }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- أحدث الآراء -->
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">أحدث الآراء</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>التقييم</th>
                                            <th>التعليق</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestReviews as $review)
                                            <tr>
                                                <td>{{ $review->user->name ?? 'زائر' }}</td>
                                                <td>
                                                    <span class="text-warning">{{ str_repeat('★', $review->rating) }}</span>
                                                    <span class="text-muted">{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                                </td>
                                                <td>{{ Str::limit($review->comment ?? $review->title ?? '-', 50) }}</td>
                                                <td>{{ $review->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">لا توجد آراء</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
