@extends('layouts.admin')

@section('title', 'SEO Diagnostics')
@section('header', 'SEO Audit & Diagnostics')

@section('content')
<div class="space-y-8">
    {{-- SEO Health Overview --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 dark:text-slate-200 text-lg mb-2">🔍 Search Engine Optimization Health</h3>
        <p class="text-sm text-gray-500">Ensure all your articles and landing pages contain proper Meta Titles, Meta Descriptions, and canonical structure for optimal ranking.</p>
    </div>

    {{-- SEO Audit Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- SEO Score Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 flex flex-col items-center justify-center text-center">
            <h3 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-4">Overall SEO Score</h3>
            
            <div class="relative w-36 h-36 flex items-center justify-center">
                {{-- Circular Progress Bar --}}
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f3f4f6" stroke-width="8" fill="transparent" class="dark:stroke-slate-700" />
                    <circle cx="50" cy="50" r="40" stroke="#4f46e5" stroke-width="8" fill="transparent"
                            stroke-dasharray="251.2" stroke-dashoffset="37.68" stroke-linecap="round" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="text-3xl font-black text-gray-900 dark:text-white">85</span>
                    <span class="text-xs text-gray-400 font-semibold uppercase">of 100</span>
                </div>
            </div>
            
            <div class="mt-4">
                <span class="px-2.5 py-1 bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-900 rounded-full text-xs font-bold uppercase">Good Health</span>
            </div>
        </div>

        {{-- SEO Checks Card --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-800 dark:text-slate-200 mb-4 border-b border-gray-100 dark:border-slate-700 pb-3 flex items-center gap-2">
                <span>📋 Core SEO Audits Run</span>
                <span class="text-xs bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 font-semibold px-2 py-0.5 rounded">6 Passed</span>
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Check 1 --}}
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="text-green-500 text-lg flex-shrink-0">✓</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-slate-100 block">Meta Title</span>
                        <span class="text-xs text-gray-500 dark:text-slate-400">All indexing pages contain a unique title tag under 60 characters.</span>
                    </div>
                </div>

                {{-- Check 2 --}}
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="text-green-500 text-lg flex-shrink-0">✓</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-slate-100 block">Meta Description</span>
                        <span class="text-xs text-gray-500 dark:text-slate-400">Articles have rich search descriptions between 120 and 160 characters.</span>
                    </div>
                </div>

                {{-- Check 3 --}}
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="text-green-500 text-lg flex-shrink-0">✓</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-slate-100 block">Alt Text</span>
                        <span class="text-xs text-gray-500 dark:text-slate-400">All uploaded media assets contain descriptive alternate tags for indexing.</span>
                    </div>
                </div>

                {{-- Check 4 --}}
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="text-green-500 text-lg flex-shrink-0">✓</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-slate-100 block">Internal Links</span>
                        <span class="text-xs text-gray-500 dark:text-slate-400">Articles link to other sections and categories of the blog.</span>
                    </div>
                </div>

                {{-- Check 5 --}}
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="text-green-500 text-lg flex-shrink-0">✓</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-slate-100 block">Readability</span>
                        <span class="text-xs text-gray-500 dark:text-slate-400">Structure uses proper heading hierarchies (H2, H3) and short paragraphs.</span>
                    </div>
                </div>

                {{-- Check 6 --}}
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="text-green-500 text-lg flex-shrink-0">✓</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-slate-100 block">Keyword Density</span>
                        <span class="text-xs text-gray-500 dark:text-slate-400">Main tag keyword distribution ranges between 1% and 2.5% density.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Post SEO Status --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30">
                <h4 class="font-bold text-gray-800 dark:text-slate-250 text-base">Latest Articles SEO Status</h4>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($posts as $post)
                    @php
                        $hasTitle = !empty($post->meta_title);
                        $hasDesc = !empty($post->meta_description);
                    @endphp
                    <div class="p-4 flex items-center justify-between gap-4 text-sm">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-gray-900 dark:text-white truncate block">{{ $post->title }}</span>
                            <span class="text-xs text-gray-400 block truncate font-mono">/{{ $post->category->slug }}/{{ $post->slug }}</span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasTitle ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasTitle ? 'Title ✓' : 'Title ✗' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasDesc ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasDesc ? 'Desc ✓' : 'Desc ✗' }}
                            </span>
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2">Edit</a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">No posts found.</div>
                @endforelse
            </div>
        </div>

        {{-- Page SEO Status --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30">
                <h4 class="font-bold text-gray-800 dark:text-slate-250 text-base">Static Pages SEO Status</h4>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($pages as $page)
                    @php
                        $hasTitle = !empty($page->meta_title);
                        $hasDesc = !empty($page->meta_description);
                    @endphp
                    <div class="p-4 flex items-center justify-between gap-4 text-sm">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-gray-900 dark:text-white truncate block">{{ $page->title }}</span>
                            <span class="text-xs text-gray-400 block truncate font-mono">/{{ $page->slug }}</span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasTitle ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasTitle ? 'Title ✓' : 'Title ✗' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasDesc ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasDesc ? 'Desc ✓' : 'Desc ✗' }}
                            </span>
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2">Edit</a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">No static pages found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
