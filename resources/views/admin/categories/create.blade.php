@extends('layouts.admin')

@section('title', 'Create Category')
@section('header', 'Create Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Add New Category</h2>
    </div>

    <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700">Category Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Technology"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="locale" class="block text-sm font-semibold text-gray-700">Language (Locale)</label>
                <select name="locale" id="locale" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="en" {{ old('locale', 'en') === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                    <option value="fr" {{ old('locale') === 'fr' ? 'selected' : '' }}>🇫🇷 French</option>
                    <option value="de" {{ old('locale') === 'de' ? 'selected' : '' }}>🇩🇪 German</option>
                    <option value="hi" {{ old('locale') === 'hi' ? 'selected' : '' }}>🇮🇳 हिन्दी</option>
                    <option value="te" {{ old('locale') === 'te' ? 'selected' : '' }}>🇮🇳 తెలుగు</option>
                </select>
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-semibold text-gray-700">Parent Category</label>
                <select name="parent_id" id="parent_id" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">None (Make it a Root Category)</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" placeholder="Brief description of the category..."
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-gray-700">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('sort_order')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="accent_color" class="block text-sm font-semibold text-gray-700">Accent Color</label>
                    <div class="mt-2 flex items-center gap-3">
                        <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', '#4f46e5') }}"
                               class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <span class="text-xs text-gray-500">Pick a custom color for category badges</span>
                    </div>
                    @error('accent_color')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="icon_emoji" class="block text-sm font-semibold text-gray-700">Icon Emoji</label>
                    <input type="text" name="icon_emoji" id="icon_emoji" value="{{ old('icon_emoji') }}" placeholder="e.g. 💻, ⚽, 📚"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('icon_emoji')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-semibold text-gray-700">Category Cover Image</label>
                <input type="file" name="image" id="image"
                       class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('image')
                    <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-750 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm hover:shadow transition-all">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
