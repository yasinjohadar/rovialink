<div class="glass-panel p-4 mb-5">
    <div class="row g-3 align-items-center">
        <div class="col-md-6">
            <form method="GET" action="{{ route('frontend.blog.index') }}" class="input-group">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('tag'))
                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                @endif
                <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control bg-transparent border-secondary text-white" placeholder="ابحث عن مقال معين..." value="{{ request('search') }}">
            </form>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-md-end overflow-x-auto pb-2 pb-md-0">
                <a href="{{ route('frontend.blog.index') }}"
                   class="btn btn-sm rounded-pill px-4 {{ empty($activeCategory) && empty($activeTag) && !request('search') ? 'btn-accent' : 'btn-glass' }}">
                    الكل
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('frontend.blog.category', $cat->slug) }}"
                   class="btn btn-sm rounded-pill px-4 text-nowrap {{ ($activeCategory && $activeCategory->id === $cat->id) ? 'btn-accent' : 'btn-glass' }}">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @if($activeTag)
    <div class="mt-3">
        <span class="badge bg-glass border border-secondary text-white p-2 px-3">
            وسم: {{ $activeTag->name }}
            <a href="{{ route('frontend.blog.index') }}" class="text-accent ms-2 text-decoration-none"><i class="fas fa-times"></i></a>
        </span>
    </div>
    @endif
</div>
