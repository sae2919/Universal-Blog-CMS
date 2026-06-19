@extends('layouts.frontend')

@section('meta_title', 'Articles Tagged with #' . $tag->name . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', 'Browse all posts tagged with ' . $tag->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    {{-- Header Banner --}}
    <header class="bg-slate-900 rounded-2xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="text-xs font-bold tracking-widest uppercase bg-indigo-600 text-indigo-100 px-3 py-1 rounded-full border border-indigo-500/35">
                Tag Keywords
            </span>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                # {{ $tag->name }}
            </h1>
            <p class="text-lg text-slate-350 leading-relaxed">Discover all stories and columns marked with the hashtag keyword keyword #{{ $tag->name }}.</p>
        </div>
    </header>

    {{-- Articles Grid --}}
    <div class="space-y-10">
        <h2 class="text-2xl font-extrabold text-gray-900 border-b border-gray-100 pb-4 flex items-center gap-2">
            📰 Tagged Articles
        </h2>

        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                        {{-- Cover Image --}}
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="block relative h-48 w-full overflow-hidden bg-gray-100 group">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" width="400" height="240" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $post->title }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-gray-600 text-sm bg-indigo-50/50">
                                    #{{ $tag->name }}
                                </div>
                            @endif
                        </a>

                        {{-- Card Body --}}
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: {{ $post->category->accent_color }};">
                                    {{ $post->category->icon_emoji }} {{ $post->category->name }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-900 leading-snug hover:text-indigo-600 transition-colors">
                                    <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="text-sm text-gray-550 line-clamp-3 leading-relaxed">{{ $post->excerpt }}</p>
                            </div>

                            {{-- Footer Info --}}
                            <div class="flex items-center gap-3 pt-4 border-t border-gray-100 text-xs text-gray-500">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($post->author->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="font-semibold text-gray-900 block truncate leading-none mb-1">{{ $post->author->name }}</span>
                                    <div class="flex flex-wrap items-center gap-1.5 text-gray-600">
                                        <span class="inline-flex items-center py-1">{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                                        <span>·</span>
                                        <span class="inline-flex items-center gap-0.5 py-1">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ number_format($post->views) }} {{ __('views') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($posts->hasPages())
                <div class="pt-6">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16 bg-white rounded-xl border border-gray-150 p-8 text-gray-500">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                <p class="text-lg font-semibold">No articles tagged under #{{ $tag->name }}.</p>
                <p class="text-sm text-gray-600 mt-1">Check back later for new content updates!</p>
            </div>
        @endif
    </div>
</div>
@endsection
