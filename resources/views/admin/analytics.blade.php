@extends('layouts.admin')

@section('title', 'Analytics')
@section('header', 'Analytics Dashboard')

@section('content')
<div class="space-y-8">
    {{-- Header Intro --}}
    <div class="bg-gradient-to-r from-violet-600 via-indigo-700 to-indigo-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight">Analytics Overview</h2>
                <p class="mt-1 text-indigo-150 text-sm max-w-xl">Deep dive into traffic, devices, referral sources, and content performance across your entire website.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-2 self-start md:self-auto flex items-center gap-2 text-sm font-semibold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                Live Tracking Enabled
            </div>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Visits --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-150 dark:border-slate-800 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Visits</span>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-slate-100 mt-1">{{ number_format($totalVisits) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-650 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>

        {{-- Unique Visitors --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-150 dark:border-slate-800 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Unique Visitors</span>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-slate-100 mt-1">{{ number_format($uniqueVisitors) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        {{-- Total Views --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-150 dark:border-slate-800 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Article Views</span>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-slate-100 mt-1">{{ number_format($totalViews) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-950/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

        {{-- Bounce Rate --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-150 dark:border-slate-800 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Bounce Rate</span>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-slate-100 mt-1">{{ $bounceRate }}%</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-rose-50 dark:bg-rose-950/30 flex items-center justify-center text-rose-600 dark:text-rose-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Main Grids --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Articles Performance Tables --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Top Articles --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-150 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-800">
                    <h3 class="font-bold text-gray-800 dark:text-slate-100 text-lg flex items-center gap-2">
                        <span>📄</span> Top Articles (by Visits Log)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-850/50 border-b border-gray-150 dark:border-slate-800">
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350">Page / Title</th>
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350">Category</th>
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350 text-right">Visits</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($topArticles as $article)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-slate-200">
                                        <div class="truncate max-w-md" title="{{ $article->title }}">
                                            {{ $article->title }}
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-slate-500 font-mono">{{ $article->url }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($article->category)
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full" 
                                                  style="background-color: {{ $article->category->accent_color }}18; color: {{ $article->category->accent_color }};">
                                                {{ $article->category->icon_emoji }} {{ $article->category->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">System / Main</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-slate-200">
                                        {{ number_format($article->count) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">No visits tracked yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Most Viewed Posts --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-150 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-800">
                    <h3 class="font-bold text-gray-800 dark:text-slate-100 text-lg flex items-center gap-2">
                        <span></span> Most Viewed Posts (Lifetime Counter)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-850/50 border-b border-gray-150 dark:border-slate-800">
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350">Article</th>
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350">Category</th>
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350 text-right">Views</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($mostViewed as $post)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-slate-200">
                                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 line-clamp-1">
                                            {{ $post->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" 
                                              style="background-color: {{ $post->category->accent_color }}18; color: {{ $post->category->accent_color }};">
                                            {{ $post->category->icon_emoji }} {{ $post->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-slate-200">
                                        {{ number_format($post->views) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">No posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Categories --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-150 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-800">
                    <h3 class="font-bold text-gray-800 dark:text-slate-100 text-lg flex items-center gap-2">
                        <span></span> Top Categories
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-850/50 border-b border-gray-150 dark:border-slate-800">
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350">Category</th>
                                <th class="px-6 py-3 font-semibold text-gray-600 dark:text-slate-350 text-right">Sum of Views</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($topCategories as $cat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold px-3 py-1 rounded-full" 
                                              style="background-color: {{ $cat->accent_color }}18; color: {{ $cat->accent_color }};">
                                            {{ $cat->icon_emoji }} {{ $cat->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-slate-200">
                                        {{ number_format($cat->posts_sum_views ?? 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-gray-400">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Demographics / Referrals (Sources, Devices, Countries) --}}
        <div class="space-y-8">
            {{-- Traffic Sources --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-150 dark:border-slate-800 shadow-sm p-6 space-y-6">
                <h3 class="font-bold text-gray-800 dark:text-slate-100 text-lg flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 pb-3">
                    <span></span> Traffic Sources
                </h3>
                <div class="space-y-4">
                    @forelse($sources as $source)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $source->source_name }}</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $source->percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-slate-800 rounded-full h-2">
                                <div class="bg-indigo-600 dark:bg-indigo-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $source->percentage }}%;"></div>
                            </div>
                            <span class="text-xs text-gray-400 font-medium block text-right">{{ number_format($source->count) }} visits</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No referer data available.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top Countries --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-150 dark:border-slate-800 shadow-sm p-6 space-y-6">
                <h3 class="font-bold text-gray-800 dark:text-slate-100 text-lg flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 pb-3">
                    <span></span> Top Countries
                </h3>
                <div class="space-y-4">
                    @forelse($topCountries as $country)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $country->country }}</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $country->percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-slate-800 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $country->percentage }}%;"></div>
                            </div>
                            <span class="text-xs text-gray-400 font-medium block text-right">{{ number_format($country->count) }} visits</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No location data available.</p>
                    @endforelse
                </div>
            </div>

            {{-- Devices --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-150 dark:border-slate-800 shadow-sm p-6 space-y-6">
                <h3 class="font-bold text-gray-800 dark:text-slate-100 text-lg flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 pb-3">
                    <span></span> Devices Used
                </h3>
                <div class="space-y-4">
                    @forelse($devices as $device)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-gray-700 dark:text-slate-300 capitalize">
                                    @if($device->device === 'desktop') 🖥️ @elseif($device->device === 'mobile') 📱 @else 🔌 @endif
                                    {{ $device->device }}
                                </span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ $device->percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-slate-800 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $device->percentage }}%;"></div>
                            </div>
                            <span class="text-xs text-gray-400 font-medium block text-right">{{ number_format($device->count) }} visits</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No device data available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
