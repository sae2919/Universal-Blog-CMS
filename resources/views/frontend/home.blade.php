@extends('layouts.frontend')

@section('meta_title', \App\Models\Setting::getValue('site_name') . ' — ' . \App\Models\Setting::getValue('site_tagline'))
@section('meta_description', \App\Models\Setting::getValue('default_meta_description'))

@section('content')

{{-- Hero Section --}}
@if($featuredPosts->count())
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Main Featured Post --}}
            @php $main = $featuredPosts->first(); @endphp
            <div class="md:col-span-2">
                <a href="{{ route('blog.show', [$main->category->slug, $main->slug]) }}" class="group block relative rounded-2xl overflow-hidden h-80 lg:h-96 shadow-md">
                    @if($main->featured_image)
                        <img src="{{ asset('storage/' . $main->featured_image) }}" alt="{{ $main->title }}" width="800" height="384"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-3 border"
                              style="background-color: {{ $main->category->accent_color }}33; color: {{ $main->category->accent_color }}; border-color: {{ $main->category->accent_color }}55;">
                            {{ $main->category->icon_emoji }} {{ $main->category->name }}
                        </span>
                        <h2 class="text-white text-2xl font-bold leading-tight group-hover:text-indigo-200 transition-colors line-clamp-2">
                            {{ $main->title }}
                        </h2>
                        <p class="text-gray-300 text-sm mt-2">By {{ $main->author->name }} · {{ $main->published_at->diffForHumans() }}</p>
                    </div>
                </a>
            </div>

            {{-- Side Featured Posts --}}
            <div class="space-y-4">
                @foreach($featuredPosts->skip(1)->take(2) as $post)
                    <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="group flex gap-4 bg-gray-50 rounded-xl p-4 hover:bg-indigo-50 transition-colors">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" width="80" height="80" loading="lazy"
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-lg bg-indigo-100 flex-shrink-0"></div>
                        @endif
                        <div class="min-w-0">
                            <span class="text-xs font-bold" style="color: {{ $post->category->accent_color }};">
                                {{ $post->category->icon_emoji }} {{ $post->category->name }}
                            </span>
                            <h3 class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700 line-clamp-2 mt-1">{{ $post->title }}</h3>
                            <p class="text-xs text-gray-600 mt-1">{{ $post->published_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Trending Posts --}}
@if($trendingPosts->count())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-3 mb-6">
        <span class="w-1 h-6 bg-red-500 rounded-full"></span>
        <h2 class="text-xl font-bold text-gray-900">🔥 {{ __('Trending Now') }}</h2>
    </div>
    <div id="trending-container" class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
        @foreach($trendingPosts as $i => $post)
            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="group flex-shrink-0 w-64 bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                @if($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" width="256" height="144" loading="lazy"
                         class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-36 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <span class="text-3xl font-black text-indigo-200">{{ $i + 1 }}</span>
                    </div>
                @endif
                <div class="p-3">
                    <span class="text-xs font-bold" style="color: {{ $post->category->accent_color }};">
                        {{ $post->category->icon_emoji }} {{ $post->category->name }}
                    </span>
                    <h3 class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700 line-clamp-2 mt-1">{{ $post->title }}</h3>
                    <p class="text-xs text-gray-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ number_format($post->views) }} {{ __('views') }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Latest Posts + Sidebar --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 pb-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

        {{-- Latest Posts --}}
        <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-1 h-6 bg-indigo-600 rounded-full"></span>
                <h2 class="text-xl font-bold text-gray-900">{{ __('Latest Articles') }}</h2>
            </div>

            <div class="space-y-6">
                @foreach($latestPosts as $post)
                    <article class="flex gap-5 bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow group">
                        {{-- Image --}}
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="flex-shrink-0">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" width="112" height="96" loading="lazy"
                                     class="w-28 h-24 rounded-lg object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-28 h-24 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('blog.category', $post->category->slug) }}"
                                   class="text-xs font-bold hover:opacity-80" style="color: {{ $post->category->accent_color }};">
                                    {{ $post->category->icon_emoji }} {{ $post->category->name }}
                                </a>
                                <span class="text-gray-200">·</span>
                                <span class="text-xs text-gray-600">{{ $post->published_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-700 transition-colors line-clamp-2">
                                <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}">{{ $post->title }}</a>
                            </h3>
                            @if($post->excerpt)
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $post->excerpt }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $post->author->name }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ number_format($post->views) }}
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-full font-semibold text-sm hover:bg-indigo-700 transition-colors">
                    {{ __('View All Articles') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-8">

            {{-- Popular Posts --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-red-500"></span> {{ __('Most Popular') }}
                </h3>
                <div class="space-y-4">
                    @foreach($popularPosts as $post)
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="group flex gap-3 hover:text-indigo-600 transition-colors">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" width="48" height="48" loading="lazy"
                                     class="w-12 h-12 rounded-lg object-cover flex-shrink-0 border border-gray-100 dark:border-slate-800 group-hover:opacity-90 transition-opacity">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-indigo-50 dark:bg-slate-800 text-indigo-500 font-bold text-[10px] uppercase flex items-center justify-center flex-shrink-0 border border-gray-100 dark:border-slate-800">
                                    Blog
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 line-clamp-2 leading-snug">
                                    {{ $post->title }}
                                </h4>
                                <p class="text-[10px] text-gray-600 dark:text-slate-500 mt-0.5">
                                    {{ number_format($post->views) }} {{ __('views') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Categories --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">{{ __('Browse Categories') }}</h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}"
                           class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition-colors group">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-700">{{ $cat->name }}</span>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full group-hover:bg-indigo-100">
                                {{ $cat->posts_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

        </aside>
    </div>
</section>

@endsection

@push('scripts')
<script>
    window.addEventListener('load', () => {
        const container = document.getElementById('trending-container');
        if (!container) return;

        // Clone the children to create a seamless infinite loop
        const originalChildren = Array.from(container.children);
        if (originalChildren.length === 0) return;
        
        originalChildren.forEach(child => {
            const clone = child.cloneNode(true);
            container.appendChild(clone);
        });

        let scrollSpeed = 0.6; // Scroll speed in pixels per frame
        let isPaused = false;
        
        function scrollStep() {
            if (isPaused) return;

            container.scrollLeft += scrollSpeed;

            // Halfway point (where the clones start)
            const halfScrollWidth = container.scrollWidth / 2;
            if (container.scrollLeft >= halfScrollWidth) {
                container.scrollLeft -= halfScrollWidth;
            }
        }

        let animationFrameId;
        function animate() {
            scrollStep();
            animationFrameId = requestAnimationFrame(animate);
        }

        // Pause on hover
        container.addEventListener('mouseenter', () => isPaused = true);
        container.addEventListener('mouseleave', () => isPaused = false);

        // Pause on touch (for mobile devices)
        container.addEventListener('touchstart', () => isPaused = true);
        container.addEventListener('touchend', () => isPaused = false);

        // Start scrolling
        animate();
    });
</script>
@endpush

