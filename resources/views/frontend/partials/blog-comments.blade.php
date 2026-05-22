<div class="glass-panel p-4 p-md-5 rounded-4 mt-5">
    <h4 class="fw-bold text-white mb-4">التعليقات ({{ $comments->count() }})</h4>

    @forelse($comments as $comment)
    <div class="d-flex gap-3 mb-4 pb-4 border-bottom border-secondary border-opacity-25">
        <img src="https://picsum.photos/seed/user{{ $comment->user_id ?? $comment->id }}/80/80"
             alt="{{ $comment->user->name ?? 'مستخدم' }}"
             class="rounded-circle flex-shrink-0"
             width="48" height="48"
             loading="lazy">
        <div>
            <h6 class="fw-bold text-white mb-1">{{ $comment->user->name ?? 'مستخدم' }}</h6>
            <span class="text-secondary small d-block mb-2">{{ $comment->created_at->diffForHumans() }}</span>
            <p class="text-secondary mb-0">{{ $comment->content }}</p>
        </div>
    </div>
    @empty
    <p class="text-secondary text-center py-3">لا توجد تعليقات بعد. كن أول من يعلق!</p>
    @endforelse
</div>

<div class="glass-panel p-4 p-md-5 rounded-4 mt-4">
    <h4 class="fw-bold text-white mb-4">اترك تعليقاً</h4>

    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @auth
    <form action="{{ route('frontend.blog.comment.store', $post->slug) }}" method="POST">
        @csrf
        <div class="mb-3">
            <textarea name="content" class="form-control bg-transparent border-secondary text-white" rows="4" placeholder="اكتب تعليقك هنا..." required>{{ old('content') }}</textarea>
            @error('content')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-accent px-5 py-2 rounded-pill">إرسال التعليق</button>
    </form>
    @else
    <p class="text-secondary mb-0">
        <a href="{{ route('login') }}" class="text-accent text-decoration-none">سجّل دخولك</a> لإضافة تعليق.
    </p>
    @endauth
</div>
