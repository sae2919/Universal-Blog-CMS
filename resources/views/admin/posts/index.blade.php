@extends('layouts.admin')

@section('title', 'Manage Posts')
@section('header', 'Manage Posts')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">All Blog Posts</h2>
            <p class="text-sm text-gray-500 mt-1">Manage, write, schedule, and edit all your articles.</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
            + Write Post
        </a>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-row gap-4 items-center justify-between w-full">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..."
                       class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full">
            </div>

            <div class="flex items-center gap-3 justify-end flex-shrink-0">
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold whitespace-nowrap mr-2">
                        Clear Filters
                    </a>
                @endif

                <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-sm rounded-lg transition-colors whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4 text-gray-650" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z"/>
                    </svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table Panel --}}
    <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 border-b border-gray-150 text-xs font-bold text-gray-550 uppercase tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4">Published Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 flex-shrink-0" alt="">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-500 font-bold text-xs uppercase flex items-center justify-center flex-shrink-0">
                                            Blog
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <span class="font-bold text-gray-900 block leading-snug truncate max-w-xs md:max-w-md">{{ $post->title }}</span>
                                        <span class="text-xs text-gray-400 font-mono">/{{ $post->category->slug }}/{{ $post->slug }}</span>
                                        @if($post->is_featured)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 ml-1">Featured</span>
                                        @endif
                                        @if($post->is_trending)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 ml-1">Trending</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-650">
                                {{ $post->category->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-650">
                                {{ $post->author->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase
                                    @if($post->status === 'published') bg-green-50 text-green-700 border border-green-100
                                    @elseif($post->status === 'draft') bg-gray-100 text-gray-700 border border-gray-200
                                    @elseif($post->status === 'scheduled') bg-blue-50 text-blue-700 border border-blue-100
                                    @else bg-red-50 text-red-700 border border-red-100
                                    @endif">
                                    {{ $post->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-bold">
                                {{ number_format($post->views) }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($post->status === 'published')
                                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" target="_blank" class="text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50 p-1.5 rounded-lg transition-colors flex items-center justify-center" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50 p-1.5 rounded-lg transition-colors flex items-center justify-center" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-red-650 hover:bg-red-50 p-1.5 rounded-lg transition-colors flex items-center justify-center cursor-pointer" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                No posts found. Get started by writing a new post!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 bg-gray-50">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
