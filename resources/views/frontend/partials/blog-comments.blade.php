<div class="blog-panel blog-panel--comments">
    <h2 class="blog-panel__title">التعليقات ({{ $comments->count() }})</h2>

    @forelse($comments as $comment)
    <div class="blog-comment">
        <img src="https://picsum.photos/seed/user{{ $comment->user_id ?? $comment->id }}/80/80"
             alt="{{ $comment->user->name ?? 'مستخدم' }}"
             class="blog-comment__avatar"
             width="44"
             height="44"
             loading="lazy">
        <div class="blog-comment__body">
            <h3 class="blog-comment__author">{{ $comment->user->name ?? 'مستخدم' }}</h3>
            <time class="blog-comment__time">{{ $comment->created_at->diffForHumans() }}</time>
            <p class="blog-comment__text">{{ $comment->content }}</p>
        </div>
    </div>
    @empty
    <p class="blog-panel__empty">لا توجد تعليقات بعد. كن أول من يعلق!</p>
    @endforelse
</div>

<div class="blog-panel blog-panel--form">
    <h2 class="blog-panel__title">اترك تعليقاً</h2>

    @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @auth
    <form action="{{ route('frontend.blog.comment.store', $post->slug) }}" method="POST" class="blog-comment-form">
        @csrf
        <div class="blog-comment-form__field">
            <textarea name="content"
                      class="blog-comment-form__textarea"
                      rows="4"
                      placeholder="اكتب تعليقك هنا..."
                      required>{{ old('content') }}</textarea>
            @error('content')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-accent blog-comment-form__submit">إرسال التعليق</button>
    </form>
    @else
    <p class="blog-panel__empty mb-0">
        <a href="{{ route('login') }}" class="text-accent text-decoration-none">سجّل دخولك</a> لإضافة تعليق.
    </p>
    @endauth
</div>
