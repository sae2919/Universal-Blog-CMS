@extends('layouts.admin')

@section('title', 'Trash Bin')
@section('header', 'Trash Bin')

@section('content')
<div x-data="{ activeTab: 'posts' }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-slate-100">Trash Bin</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                Restore soft-deleted posts, pages, and comments, or delete them permanently.
            </p>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200 dark:border-slate-700">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'posts'"
                    type="button"
                    :class="activeTab === 'posts' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 focus:outline-none cursor-pointer">
                Posts
                <span :class="activeTab === 'posts' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-gray-100 text-gray-900 dark:bg-slate-800 dark:text-slate-300'"
                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold">
                    {{ $posts->count() }}
                </span>
            </button>
            <button @click="activeTab = 'pages'"
                    type="button"
                    :class="activeTab === 'pages' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 focus:outline-none cursor-pointer">
                Pages
                <span :class="activeTab === 'pages' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-gray-100 text-gray-900 dark:bg-slate-800 dark:text-slate-300'"
                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold">
                    {{ $pages->count() }}
                </span>
            </button>
            <button @click="activeTab = 'comments'"
                    type="button"
                    :class="activeTab === 'comments' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 focus:outline-none cursor-pointer">
                Comments
                <span :class="activeTab === 'comments' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-gray-100 text-gray-900 dark:bg-slate-800 dark:text-slate-300'"
                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold">
                    {{ $comments->count() }}
                </span>
            </button>
        </nav>
    </div>

    {{-- Posts Tab Content --}}
    <div x-show="activeTab === 'posts'" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-150 dark:border-slate-700 text-xs font-bold text-gray-550 dark:text-slate-300 uppercase tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Deleted Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 dark:border-slate-600 flex-shrink-0" alt="">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-500 dark:text-indigo-400 font-bold text-xs uppercase flex items-center justify-center flex-shrink-0">
                                            Blog
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <span class="font-bold text-gray-900 dark:text-slate-100 block leading-snug truncate max-w-xs md:max-w-md">{{ $post->title }}</span>
                                        <span class="text-xs text-gray-400 font-mono">/{{ $post->category?->slug }}/{{ $post->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-650 dark:text-slate-300">
                                {{ $post->category?->name ?? 'Uncategorized' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                {{ $post->deleted_at ? $post->deleted_at->format('M d, Y H:i') : 'N/A' }}
                                <span class="text-xs text-gray-400 block mt-0.5">({{ $post->deleted_at ? $post->deleted_at->diffForHumans() : '' }})</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <form action="{{ route('admin.trash.restore', ['type' => 'post', 'id' => $post->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-indigo-650 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold text-sm cursor-pointer">
                                            Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.trash.force-delete', ['type' => 'post', 'id' => $post->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this post? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-semibold text-sm cursor-pointer">
                                            Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                                No deleted posts found in the trash.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pages Tab Content --}}
    <div x-show="activeTab === 'pages'" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden" style="display: none;">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 dark:bg-slate-700/50 border-b border-gray-150 dark:border-slate-700 text-xs font-bold text-gray-550 dark:text-slate-300 uppercase tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">URL Slug</th>
                        <th class="px-6 py-4">Deleted Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                    @forelse($pages as $page)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-slate-100">
                                {{ $page->title }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400 font-mono">
                                /{{ $page->slug }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                {{ $page->deleted_at ? $page->deleted_at->format('M d, Y H:i') : 'N/A' }}
                                <span class="text-xs text-gray-400 block mt-0.5">({{ $page->deleted_at ? $page->deleted_at->diffForHumans() : '' }})</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <form action="{{ route('admin.trash.restore', ['type' => 'page', 'id' => $page->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-indigo-650 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold text-sm cursor-pointer">
                                            Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.trash.force-delete', ['type' => 'page', 'id' => $page->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this page? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-semibold text-sm cursor-pointer">
                                            Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                                No deleted pages found in the trash.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Comments Tab Content --}}
    <div x-show="activeTab === 'comments'" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden" style="display: none;">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 dark:bg-slate-700/50 border-b border-gray-150 dark:border-slate-700 text-xs font-bold text-gray-550 dark:text-slate-300 uppercase tracking-wider">
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4">Comment</th>
                        <th class="px-6 py-4">Article</th>
                        <th class="px-6 py-4">Deleted Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                    @forelse($comments as $comment)
                        <tr class="hover:bg-gray-55/50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-slate-100 leading-tight">{{ $comment->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $comment->email }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-xs md:max-w-md" style="word-break: break-all; overflow-wrap: break-word;">
                                <p class="text-gray-700 dark:text-slate-300 whitespace-pre-line leading-relaxed" style="word-break: break-all; overflow-wrap: break-word;">{{ $comment->comment }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-xs leading-snug block text-gray-650 dark:text-slate-300 truncate max-w-[180px]">
                                    {{ $comment->post?->title ?? 'Deleted Post' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                {{ $comment->deleted_at ? $comment->deleted_at->format('M d, Y H:i') : 'N/A' }}
                                <span class="text-xs text-gray-400 block mt-0.5">({{ $comment->deleted_at ? $comment->deleted_at->diffForHumans() : '' }})</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <form action="{{ route('admin.trash.restore', ['type' => 'comment', 'id' => $comment->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-indigo-650 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold text-sm cursor-pointer">
                                            Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.trash.force-delete', ['type' => 'comment', 'id' => $comment->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this comment? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-semibold text-sm cursor-pointer">
                                            Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                                No deleted comments found in the trash.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
