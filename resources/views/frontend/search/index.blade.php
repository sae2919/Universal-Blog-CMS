@extends('layouts.frontend')

@section('meta_title', 'Search Results for "' . $query . '" — ' . \App\Models\Setting::getValue('site_name'))

@section('content')
@php
    $categories = \Illuminate\Support\Facades\Cache::remember('categories.list.with_count.' . app()->getLocale(), now()->addHours(6), function() {
        return \App\Models\Category::active()->orderBy('name')->withCount(['posts' => function($q) {
            $q->published();
        }])->get();
    });
@endphp

<!-- Page Header Banner -->
<div class="relative py-16 md:py-24 lg:py-32 px-4 sm:px-6 lg:px-8 text-center bg-cover bg-top bg-no-repeat transition-all duration-300 mb-8 md:mb-12"
     style="background-image: url('{{ \App\Models\Setting::getValue('blog_banner_image') ? asset('storage/' . \App\Models\Setting::getValue('blog_banner_image')) : asset('images/blogg.png') }}');">
    {{-- Dark mode background overlay --}}
    <div class="absolute inset-0 bg-slate-950/80 opacity-0 dark:opacity-100 transition-opacity duration-300"></div>
    
    <div class="relative max-w-4xl mx-auto space-y-4 w-full z-10">
        <h1 class="text-[34px] md:text-[48px] lg:text-[64px] font-semibold leading-[37px] md:leading-[54px] lg:leading-[70px] text-[#000d44] dark:text-white tracking-tight font-heading">
            {{ __('Search Results') }}
        </h1>
        
        {{-- Breadcrumbs --}}
        <div class="flex items-center justify-center gap-1.5 text-sm font-semibold text-[#788094] dark:text-slate-350">
            <a href="{{ url('/') }}" class="text-[#000d44] dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('Home') }}</a>
            <span class="text-gray-400 dark:text-slate-600 px-1">/</span>
            <a href="{{ route('blog.index') }}" class="text-[#000d44] dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('Blog') }}</a>
            <span class="text-gray-400 dark:text-slate-600 px-1">/</span>
            <span class="text-[#788094] dark:text-slate-400">{{ __('Search') }}</span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 space-y-10">
    
    {{-- Search and Categories Filter Bar --}}
    <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-6 pb-2">
        {{-- Categories Pills (Horizontal scroll on mobile, wrap on desktop) --}}
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-1.5 flex-1 -mx-4 px-4 sm:mx-0 sm:px-0 flex-nowrap lg:flex-wrap">
            <a href="{{ route('blog.index') }}" 
               class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all duration-300 bg-white border border-gray-200 text-gray-650 hover:border-indigo-600 hover:text-indigo-600 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-300 dark:hover:text-white dark:hover:border-slate-700">
                {{ __('All Posts') }}
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('blog.category', $cat->slug) }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all duration-300 bg-white border border-gray-200 text-gray-650 hover:border-indigo-600 hover:text-indigo-600 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-300 dark:hover:text-white dark:hover:border-slate-700">
                    {{ $cat->name }}
                    @if(isset($cat->posts_count))
                        <span class="text-xs opacity-75 ml-0.5">({{ $cat->posts_count }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search Input --}}
        <form action="{{ route('blog.search') }}" method="GET" class="w-full lg:w-80 relative flex-shrink-0">
            <input type="text" name="q" placeholder="Search articles..." value="{{ $query }}" aria-label="Search posts"
                   class="w-full pl-11 pr-4 py-2.5 border border-gray-200 dark:border-slate-800 rounded-full text-sm bg-white dark:bg-slate-900 text-gray-850 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-all shadow-sm">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    @if($posts->count() > 0)
        <div class="text-sm font-medium text-gray-500 dark:text-slate-400 pb-2">
            {{ __('Found :count matching articles for your query.', ['count' => $posts->total()]) }}
        </div>
        
        {{-- Full-width Grid Layout (No Sidebar) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-[30px]">
            @foreach($posts as $post)
                <article class="flex flex-col h-full bg-transparent group relative blog-card-hover-blink">
                    {{-- Cover Image --}}
                    <div class="relative overflow-hidden aspect-[16/10] w-full rounded-[10px] bg-gray-100 dark:bg-slate-950">
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="absolute inset-0 z-10" aria-label="{{ $post->title }}"></a>
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" width="400" height="250" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" alt="{{ $post->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-gray-400 text-sm bg-indigo-50/50 dark:bg-indigo-950/20">
                                Blog Image
                            </div>
                        @endif
                        {{-- Overlay hover background --}}
                        <div class="absolute inset-0 bg-indigo-600 hover-blink-overlay opacity-0 pointer-events-none"></div>
                    </div>

                    {{-- Card Body --}}
                    <div class="pt-4 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            {{-- Date --}}
                            <div class="text-xs font-semibold text-[#9C9C9C] dark:text-slate-500 uppercase tracking-wider">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                            </div>
                            
                            {{-- Title --}}
                            <h3 class="text-[22px] font-semibold leading-[28px] text-[#000d44] dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-heading tracking-tight line-clamp-2">
                                <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}">{{ $post->title }}</a>
                            </h3>

                            {{-- Excerpt --}}
                            @if($post->excerpt)
                                <p class="text-sm text-gray-550 dark:text-slate-400 line-clamp-2 leading-relaxed font-medium">
                                    {{ $post->excerpt }}
                                </p>
                            @endif
                        </div>

                        {{-- Read More --}}
                        <div class="pt-3">
                            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" 
                               class="inline-flex items-center gap-1.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-[#000d44] dark:hover:text-white transition-colors group/btn">
                                <span>Read More</span>
                                <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $posts->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-20 bg-white dark:bg-slate-900 rounded-[10px] border border-gray-150 dark:border-slate-800 p-8 max-w-xl mx-auto shadow-sm">
            <svg class="w-16 h-16 text-gray-300 dark:text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('No results match your search') }}</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1.5 leading-relaxed">{{ __('Check spelling, try different keywords, or type a new query in the search bar above.') }}</p>
        </div>
    @endif
</div>
@endsection
