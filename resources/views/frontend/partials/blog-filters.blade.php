@php
    $isAllActive = empty($activeCategory) && empty($activeTag) && !request('search');
@endphp
<div class="blog-toolbar section-fade-up mb-5">
    <div class="blog-toolbar__card">
        <div class="row g-3 g-lg-4 align-items-lg-center">
            <div class="col-lg-5">
                <form method="GET"
                      action="{{ route('frontend.blog.index') }}"
                      class="blog-toolbar__search-form"
                      role="search">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('tag'))
                        <input type="hidden" name="tag" value="{{ request('tag') }}">
                    @endif
                    <label class="visually-hidden" for="blog-search-input">بحث في المدونة</label>
                    <div class="blog-toolbar__search">
                        <i class="fas fa-search blog-toolbar__search-icon" aria-hidden="true"></i>
                        <input type="search"
                               id="blog-search-input"
                               name="search"
                               class="blog-toolbar__search-input"
                               placeholder="ابحث عن مقال، موضوع، أو كلمة مفتاحية..."
                               value="{{ request('search') }}"
                               autocomplete="off">
                        <button type="submit" class="blog-toolbar__search-btn">بحث</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-7">
                <nav class="blog-toolbar__nav" aria-label="تصنيفات المدونة">
                    <a href="{{ route('frontend.blog.index') }}"
                       class="blog-toolbar__chip {{ $isAllActive ? 'is-active' : '' }}">
                        الكل
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('frontend.blog.category', $cat->slug) }}"
                       class="blog-toolbar__chip {{ ($activeCategory && $activeCategory->id === $cat->id) ? 'is-active' : '' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </nav>
            </div>
        </div>

        @if($activeTag)
        <div class="blog-toolbar__tag-row">
            <span class="blog-toolbar__tag">
                <i class="fas fa-tag" aria-hidden="true"></i>
                وسم: {{ $activeTag->name }}
                <a href="{{ route('frontend.blog.index') }}"
                   class="blog-toolbar__tag-remove"
                   aria-label="إزالة الوسم">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </a>
            </span>
        </div>
        @endif
    </div>
</div>
