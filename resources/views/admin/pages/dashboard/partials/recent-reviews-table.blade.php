<div class="table-responsive">
    <table class="table orders-index-table align-middle mb-0">
        <thead>
            <tr>
                <th>المستخدم</th>
                <th>التقييم</th>
                <th>التعليق</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentReviews as $review)
                <tr>
                    <td>
                        <span class="orders-index-customer">{{ $review->user?->name ?? 'مجهول' }}</span>
                    </td>
                    <td>
                        <span class="dash-review-stars" aria-label="{{ $review->rating }} من 5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </span>
                    </td>
                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit($review->comment, 40) }}</td>
                    <td>
                        @switch($review->status)
                            @case('approved')
                                <span class="dash-review-badge dash-review-badge--success">مقبول</span>
                                @break
                            @case('rejected')
                                <span class="dash-review-badge dash-review-badge--danger">مرفوض</span>
                                @break
                            @default
                                <span class="dash-review-badge dash-review-badge--warning">انتظار</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="orders-index-empty py-4">
                            <i class="bi bi-chat-square-text d-block"></i>
                            <p class="mb-0">لا توجد آراء حالياً</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
