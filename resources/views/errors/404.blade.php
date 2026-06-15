@extends('layouts.frontend')

@section('meta_title', __('Page Not Found') . ' (404) — ' . \App\Models\Setting::getValue('site_name'))

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16 bg-gray-50 dark:bg-slate-950">
    <div class="max-w-2xl w-full text-center space-y-8">
        {{-- Animated 404 Illustration --}}
        <div class="relative flex justify-center">
            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-400/5 rounded-full filter blur-3xl w-72 h-72 mx-auto"></div>
            
            <div class="relative space-y-2">
                <span class="text-9xl font-black tracking-widest text-indigo-600 dark:text-indigo-400 select-none animate-pulse">
                    404
                </span>
                <div class="absolute bottom-1 right-0 left-0 text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    {{ __('Page Not Found') }}
                </div>
            </div>
        </div>

        {{-- Explanatory Text --}}
        <div class="space-y-3">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                {{ __('Lost in the Digital Wilderness?') }}
            </h2>
            <p class="text-sm sm:text-base text-gray-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                {{ __('The article or page you are looking for might have been moved, deleted, or never existed in the first place.') }}
            </p>
        </div>

        {{-- Search Bar --}}
        <div class="max-w-md mx-auto">
            <form action="{{ route('blog.search') }}" method="GET" class="flex gap-2">
                <input type="text" name="q" placeholder="{{ __('Search for other articles...') }}" required
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-150 flex-shrink-0">
                    {{ __('Search') }}
                </button>
            </form>
        </div>

        {{-- Suggested Articles --}}
        @php
            $suggestedPosts = \App\Models\Post::published()->orderByDesc('views')->take(3)->get();
        @endphp

        @if($suggestedPosts->isNotEmpty())
            <div class="pt-8 border-t border-gray-150 dark:border-slate-800 max-w-md mx-auto">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-4 text-center">
                    {{ __('Popular Articles to Explore') }}
                </h3>
                <div class="space-y-3 text-left">
                    @foreach($suggestedPosts as $post)
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" 
                           class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800/80 rounded-xl hover:shadow-sm transition-all group">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                     class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 text-indigo-500">
                                    📄
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-semibold text-gray-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-405 line-clamp-1">
                                    {{ $post->title }}
                                </h4>
                                <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5">
                                    {{ $post->published_at->format('M d, Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Back Home --}}
        <div class="pt-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Return to Homepage') }}
            </a>
        </div>
    </div>
</div>
@endsection
