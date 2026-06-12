@extends('layouts.admin')

@section('title', 'Tags')
@section('header', 'Manage Tags')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">All Tags</h2>
            <p class="text-sm text-gray-500 mt-1">Organize and structure blog topics with keywords.</p>
        </div>
        <a href="{{ route('admin.tags.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
            + Add New Tag
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 border-b border-gray-150 text-xs font-bold text-gray-550 uppercase tracking-wider">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Posts Count</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($tags as $tag)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                # {{ $tag->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-650">
                                {{ $tag->slug }}
                            </td>
                            <td class="px-6 py-4 text-gray-650">
                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold text-xs border border-indigo-100">
                                    {{ $tag->posts_count }} posts
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.tags.edit', $tag->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</a>
                                    <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                No tags created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tags->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 bg-gray-50">
                {{ $tags->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
