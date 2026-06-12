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
    <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-5 flex flex-wrap gap-4 items-center justify-between">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..."
                   class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64">

            <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-sm rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                    Clear Filters
                </a>
            @endif
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
                                <div class="flex items-center justify-end gap-3">
                                    @if($post->status === 'published')
                                        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" target="_blank" class="text-gray-600 hover:text-gray-900 font-semibold text-sm">View</a>
                                    @endif
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</a>
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Delete</button>
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
