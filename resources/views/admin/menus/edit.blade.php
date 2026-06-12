@extends('layouts.admin')

@section('title', 'Edit Menu Location')
@section('header', 'Edit Menu Location')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800 dark:text-slate-200">Edit Menu Location Info</h3>
        <a href="{{ route('admin.menus.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Back to List
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6">
        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-350">Menu Name</label>
                <input type="text" name="name" id="name" required placeholder="e.g. Main Navigation Menu" value="{{ old('name', $menu->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400">
                @error('name')
                    <p class="text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-slate-350">Location Slug</label>
                <input type="text" name="location" id="location" required placeholder="e.g. main, footer" value="{{ old('location', $menu->location) }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 font-mono text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400">
                <p class="text-xs text-gray-400">Unique identifier for rendering this menu in specific layout positions.</p>
                @error('location')
                    <p class="text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-slate-350 hover:bg-gray-50 dark:hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors">
                    Update Location
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
