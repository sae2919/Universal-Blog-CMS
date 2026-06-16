@extends('layouts.frontend')

@section('meta_title', __('All Articles') . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', __('Browse all articles on') . ' ' . \App\Models\Setting::getValue('site_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    {{-- Search and Categories Filter Bar --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        {{-- Search Input --}}
        <form action="{{ route('blog.search') }}" method="GET" class="w-full md:w-1/2 relative">
            <input type="text" name="q" placeholder="Search" value="{{ request('q') }}"
                   class="w-full pl-5 pr-12 py-3 border border-gray-250 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-all">
            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>

        {{-- Categories Select Dropdown --}}
        <div class="w-full md:w-72 relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false"
                    class="w-full flex justify-between items-center px-5 py-3 border border-gray-250 rounded-lg text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <span>Categories (All)</span>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="absolute right-0 top-full mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 max-h-60 overflow-y-auto" style="display: none;">
                <a href="{{ route('blog.index') }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-indigo-50">Categories (All)</a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Main Grid Layout --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        {{-- Posts Cards Grid (Spans 3 columns on tablet and desktop) --}}
        <div class="md:col-span-3 flex flex-col justify-between">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($posts as $post)
                    <article class="bg-white rounded-2xl border border-gray-150 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between h-full group">
                        <div>
                            {{-- Cover Image --}}
                            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="block relative h-48 w-full overflow-hidden bg-gray-100">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $post->title }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-bold text-gray-400 text-sm bg-indigo-50/50">
                                        Blog
                                    </div>
                                @endif
                            </a>

                            {{-- Card Body --}}
                            <div class="p-5 space-y-3">
                                <h3 class="font-bold text-gray-950 leading-snug hover:text-indigo-600 transition-colors text-[15px]">
                                    <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="line-clamp-2 leading-tight">{{ $post->title }}</a>
                                </h3>
                                @if($post->excerpt)
                                    <p class="text-xs text-gray-550 line-clamp-3 leading-relaxed">{{ $post->excerpt }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Footer Info --}}
                        <div class="px-5 pb-5 pt-3 border-t border-gray-100 flex items-center gap-1.5 text-xs text-gray-400">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $post->published_at ? $post->published_at->format('d M y') : $post->created_at->format('d M y') }}</span>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-16 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">{{ __('No articles published yet.') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($posts->hasPages())
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar (Spans 1 column on tablet and desktop) --}}
        <aside class="space-y-8 md:col-span-1">
            {{-- Blog Topics Sidebar Widget --}}
            <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm">
                <h3 class="font-extrabold text-[#1d4ed8] text-xl mb-4 leading-none">{{ __('Blog Topics') }}</h3>
                <div class="space-y-3">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}"
                           class="block text-sm text-gray-700 hover:text-[#1d4ed8] hover:underline transition-all leading-normal">
                           {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
