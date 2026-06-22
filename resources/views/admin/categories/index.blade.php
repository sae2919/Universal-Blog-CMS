@extends('layouts.admin')

@section('title', 'Categories')
@section('header', 'Manage Categories')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">All Categories</h2>
            <p class="text-sm text-gray-500 mt-1">Organize articles hierarchically (e.g., Technology > Laravel).</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
            + Add New Category
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 border-b border-gray-150 text-xs font-bold text-gray-550 uppercase tracking-wider">
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Parent Category</th>
                        <th class="px-6 py-4">Sort Order</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" class="w-10 h-10 object-cover rounded-lg border border-gray-200" alt="">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold text-xs uppercase">
                                            Cat
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold text-gray-900 block leading-tight">{{ $category->name }}</span>
                                        <span class="text-xs text-gray-400 font-mono">/category/{{ $category->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-650">
                                {{ $category->parent ? $category->parent->name : 'None (Root Category)' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-bold">
                                {{ $category->sort_order }}
                            </td>
                            <td class="px-6 py-4">
                                @if($category->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-150">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-755 border border-red-150">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('blog.category', $category->slug) }}" target="_blank" class="text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50 p-1.5 rounded-lg transition-colors flex items-center justify-center" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50 p-1.5 rounded-lg transition-colors flex items-center justify-center" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No categories created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 bg-gray-50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
