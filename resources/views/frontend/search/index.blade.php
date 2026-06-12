@extends('layouts.frontend')

@section('meta_title', 'Search Results for "' . $query . '" — ' . \App\Models\Setting::getValue('site_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    {{-- Header Banner --}}
    <header class="bg-indigo-900 rounded-2xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="text-xs font-bold tracking-widest uppercase bg-indigo-650 text-indigo-100 px-3 py-1 rounded-full border border-indigo-500/35">
                Search Results
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                Query: "{{ $query }}"
            </h1>
            <p class="text-lg text-indigo-200">We found {{ $posts->total() }} matching articles matching your search terms.</p>
        </div>
    </header>

    {{-- Articles List --}}
    <div class="space-y-10">
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
                                    Result
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
            <div class="text-center py-20 bg-white rounded-xl border border-gray-150 p-8 max-w-xl mx-auto shadow-sm">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900">No results match your search</h3>
                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">Check spelling, try different keywords, or type a new query in the search bar below.</p>
                <form action="{{ route('blog.search') }}" method="GET" class="mt-6 flex max-w-md mx-auto">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search again..." required
                           class="flex-1 px-4 py-2.5 rounded-l-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-r-lg shadow-sm transition-all">
                        Search
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
