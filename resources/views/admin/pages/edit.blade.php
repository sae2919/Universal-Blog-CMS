@extends('layouts.admin')

@section('title', 'Edit Page')
@section('header', 'Edit Page')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Edit Page — {{ $page->title }}</h2>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        @method('PUT')

        {{-- Left column (Main content) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700">Page Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" placeholder="e.g. About Us"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700">Page Content</label>
                    <textarea name="content" id="content" rows="15" placeholder="Write page content in HTML or plain text..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm @error('content') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('content', $page->content) }}</textarea>
                    @error('content')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- SEO Fields --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Search Engine Optimization (SEO)</h3>

                <div>
                    <label for="meta_title" class="block text-sm font-semibold text-gray-700">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}" placeholder="Default matches page title"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-semibold text-gray-700">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" placeholder="Brief summary of the page for search result snippets..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>

                <div>
                    <label for="meta_keywords" class="block text-sm font-semibold text-gray-700">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="e.g. about, company, blog, niche"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Right column (Settings & Sidebar) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Publish Settings</h3>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $page->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label for="locale" class="block text-sm font-semibold text-gray-700">Language (Locale)</label>
                    <select name="locale" id="locale" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="en" {{ old('locale', $page->locale) === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                        <option value="fr" {{ old('locale', $page->locale) === 'fr' ? 'selected' : '' }}>🇫🇷 French</option>
                        <option value="de" {{ old('locale', $page->locale) === 'de' ? 'selected' : '' }}>🇩🇪 German</option>
                        <option value="hi" {{ old('locale', $page->locale) === 'hi' ? 'selected' : '' }}>🇮🇳 हिन्दी</option>
                        <option value="te" {{ old('locale', $page->locale) === 'te' ? 'selected' : '' }}>🇮🇳 తెలుగు</option>
                    </select>
                </div>

                <div>
                    <label for="featured_image" class="block text-sm font-semibold text-gray-700">Featured Image</label>
                    @if($page->featured_image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $page->featured_image) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="">
                        </div>
                    @endif
                    <input type="file" name="featured_image" id="featured_image"
                           class="mt-3 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('featured_image')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 flex flex-col gap-3">
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
                    Save Changes
                </button>
                <a href="{{ route('admin.pages.index') }}" class="w-full py-2.5 text-center text-gray-750 font-semibold text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
