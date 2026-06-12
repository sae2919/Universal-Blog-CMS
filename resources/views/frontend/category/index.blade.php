@extends('layouts.frontend')

@section('meta_title', $category->name . ' Articles — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $category->description ?? 'Read posts about ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    {{-- Header Banner --}}
    <header class="rounded-2xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden"
            style="background: linear-gradient(135deg, {{ $category->accent_color }} 0%, #0f172a 100%);">
        <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="text-xs font-bold tracking-widest uppercase bg-white/10 text-indigo-100 px-3 py-1 rounded-full border border-white/20">
                Category
            </span>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                {{ $category->icon_emoji }} {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="text-lg text-slate-350 leading-relaxed">{{ $category->description }}</p>
            @endif
        </div>
    </header>

    {{-- Articles Grid --}}
    <div class="space-y-10">
        <h2 class="text-2xl font-extrabold text-gray-900 border-b border-gray-100 pb-4 flex items-center gap-2">
            📰 Articles in {{ $category->name }}
        </h2>

        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                        {{-- Cover Image --}}
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="block relative h-48 w-full overflow-hidden bg-gray-100 group">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $post->title }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-gray-400 text-sm bg-indigo-50/50">
                                    {{ $category->name }}
                                </div>
                            @endif
                        </a>

                        {{-- Card Body --}}
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: {{ $category->accent_color }};">
                                    {{ $category->icon_emoji }} {{ $category->name }}
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
                                <div class="min-w-0">
                                    <span class="font-semibold text-gray-900 block truncate leading-none mb-1">{{ $post->author->name }}</span>
                                    <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <p class="text-lg font-semibold">No articles found in this category.</p>
                <p class="text-sm text-gray-400 mt-1">Check back later for new content updates!</p>
            </div>
        @endif
    </div>
</div>
@endsection
