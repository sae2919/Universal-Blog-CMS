@extends('layouts.frontend')

@section('meta_title', \App\Models\Setting::getValue('site_name') . ' — ' . \App\Models\Setting::getValue('site_tagline'))
@section('meta_description', \App\Models\Setting::getValue('default_meta_description'))

@section('content')

{{-- Full-Width Rikkeisoft-style Hero Section --}}
<section class="relative w-full bg-cover bg-center flex items-center justify-center text-center px-4 py-12" 
         style="min-height: 280px; background-image: linear-gradient(135deg, rgba(30, 27, 75, 0.85), rgba(15, 23, 42, 0.95)), url('{{ \App\Models\Setting::getValue('blog_hero_image') ? asset('storage/' . \App\Models\Setting::getValue('blog_hero_image')) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop' }}'); background-blend-mode: multiply;">
    <div class="max-w-4xl mx-auto space-y-4">
        {{-- Title --}}
        <h1 class="text-white text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight">
            Blog
        </h1>
        
        {{-- Subtitle/Description --}}
        <p class="text-gray-300 text-xs sm:text-sm lg:text-base max-w-2xl mx-auto leading-relaxed font-medium">
            {{ \App\Models\Setting::getValue('blog_description', 'Welcome to ' . \App\Models\Setting::getValue('site_name', 'Techsprout') . '\'s Blog, your go-to source for the latest updates, insights, and trends in the world of software development.') }}
        </p>
    </div>
</section>

{{-- Main Content Section: Left Grid + Sidebar --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Articles Grid --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($latestPosts as $post)
                    <article class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-shadow">
                        {{-- Image --}}
                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="block relative overflow-hidden h-48 bg-gray-50">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                            @endif
                            
                            {{-- Category Badge --}}
                            <span class="absolute top-4 left-4 inline-block text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider text-white shadow-sm"
                                  style="background-color: {{ $post->category->accent_color ?? '#dc2626' }};">
                                {{ $post->category->name }}
                            </span>
                        </a>

                        {{-- Content --}}
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div class="space-y-2">
                                <h3 class="text-base font-bold text-gray-900 group-hover:text-red-650 transition-colors line-clamp-2 leading-snug">
                                    <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}">{{ $post->title }}</a>
                                </h3>
                                @if($post->excerpt)
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-50 mt-4 text-[11px] text-gray-600">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $post->author->name }}
                                </span>
                                <span class="text-gray-300 font-bold">·</span>
                                <span class="text-gray-500 font-medium">
                                    {{ $post->published_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- View All Articles Button --}}
            <div class="pt-4 text-center">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 bg-red-650 text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-red-700 transition-colors shadow-sm hover:shadow">
                    {{ __('View All Articles') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Right: Sidebar --}}
        <aside class="space-y-6">
            
            {{-- Rikkeisoft-style Categories Widget --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 border-t-4 border-red-650">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-50 pb-2">{{ __('Categories') }}</h3>
                <div class="divide-y divide-gray-50">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}"
                           class="flex items-center justify-between py-3 hover:text-red-650 transition-colors group">
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-red-650 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-650 group-hover:scale-125 transition-transform"></span>
                                {{ $cat->name }}
                            </span>
                            <span class="text-xs text-gray-500 font-semibold group-hover:text-red-650">
                                ({{ $cat->posts_count }})
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Most Popular Articles Widget --}}
            @if($popularPosts->count())
                <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 border-t-4 border-indigo-650">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-50 pb-2">{{ __('Most Popular') }}</h3>
                    <div class="space-y-4">
                        @foreach($popularPosts as $post)
                            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="group flex gap-3 hover:text-indigo-650 transition-colors">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" width="48" height="48" loading="lazy"
                                         class="w-12 h-12 rounded-lg object-cover flex-shrink-0 border border-gray-100 group-hover:opacity-90 transition-opacity">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-500 font-bold text-[10px] uppercase flex items-center justify-center flex-shrink-0 border border-gray-100">
                                        Blog
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-gray-800 group-hover:text-indigo-650 line-clamp-2 leading-snug">
                                        {{ $post->title }}
                                    </h4>
                                    <p class="text-[10px] text-gray-500 mt-1">
                                        {{ number_format($post->views) }} {{ __('views') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </aside>
    </div>
</section>

@endsection
