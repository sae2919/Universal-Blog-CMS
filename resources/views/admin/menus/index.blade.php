@extends('layouts.admin')

@section('title', 'Navigation Menus')
@section('header', 'Navigation Menus')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800 dark:text-slate-200">Manage Menu Locations</h3>
        <a href="{{ route('admin.menus.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors">
            + Create Menu Location
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase">
                    <th class="px-6 py-4">Menu Name</th>
                    <th class="px-6 py-4">Location Slug</th>
                    <th class="px-6 py-4">Total Items</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm text-gray-700 dark:text-slate-350">
                @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-950 dark:text-white">
                            {{ $menu->name }}
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            {{ $menu->location }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full bg-gray-150 dark:bg-slate-700 text-xs font-semibold">
                                {{ $menu->items_count }} items
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                            <a href="{{ route('admin.menus.builder', $menu->id) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                ⚙️ Edit Structure
                            </a>
                            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-slate-200">
                                Edit Info
                            </a>
                            <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this menu location and all its items?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 dark:hover:text-red-400">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                            No menu locations created yet. Get started by adding a menu location (e.g. "main" or "footer").
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
