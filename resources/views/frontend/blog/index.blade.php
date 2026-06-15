@extends('layouts.frontend')

@section('meta_title', __('All Articles') . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', __('Browse all articles on') . ' ' . \App\Models\Setting::getValue('site_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">{{ __('All Articles') }}</h1>
        <p class="text-gray-500 mt-1">{{ $posts->total() }} {{ __('articles found') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        {{-- Posts Grid --}}
        <div class="md:col-span-2 space-y-5">
            @forelse($posts as $post)
                <article class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group flex gap-5 p-4">
                    <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="flex-shrink-0">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                 class="w-32 h-24 rounded-lg object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-32 h-24 rounded-lg bg-indigo-50"></div>
                        @endif
                    </a>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <a href="{{ route('blog.category', $post->category->slug) }}"
                               class="text-xs font-bold hover:opacity-80" style="color: {{ $post->category->accent_color }};">
                                {{ $post->category->icon_emoji }} {{ $post->category->name }}
                            </a>
                            <span class="text-gray-200">·</span>
                            <span class="text-xs text-gray-400">{{ $post->published_at->diffForHumans() }}</span>
                        </div>
                        <h2 class="font-semibold text-gray-900 group-hover:text-indigo-700 transition-colors">
                            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="line-clamp-2">{{ $post->title }}</a>
                        </h2>
                        @if($post->excerpt)
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $post->excerpt }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">By {{ $post->author->name }} · {{ number_format($post->views) }} {{ __('views') }}</p>
                    </div>
                </article>
            @empty
                <div class="text-center py-16 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>{{ __('No articles published yet.') }}</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-8">
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">{{ __('Most Popular') }}</h3>
                <div class="space-y-4">
                    @foreach($popularPosts as $post)
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="group flex gap-3 hover:text-indigo-600 transition-colors">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
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
                                <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5">
                                    {{ number_format($post->views) }} {{ __('views') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">{{ __('Categories') }}</h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}"
                           class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-indigo-50 transition-colors group">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-700">{{ $cat->name }}</span>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
