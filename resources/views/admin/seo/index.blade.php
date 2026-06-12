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
