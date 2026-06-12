@extends('layouts.admin')

@section('title', 'Static Pages')
@section('header', 'Manage Pages')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Static Pages</h2>
            <p class="text-sm text-gray-500 mt-1">Manage pages like About Us, Contact, Privacy Policy, Terms, etc.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
            + Create New Page
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 border-b border-gray-150 text-xs font-bold text-gray-550 uppercase tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">URL Slug</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($pages as $page)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $page->title }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-mono">
                                /{{ $page->slug }}
                            </td>
                            <td class="px-6 py-4">
                                @if($page->status === 'published')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-150">
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-750 border border-gray-200">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $page->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-gray-600 hover:text-gray-900 font-semibold text-sm">View</a>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</a>
                                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No static pages created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 bg-gray-50">
                {{ $pages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
