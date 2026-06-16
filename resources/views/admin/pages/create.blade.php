@extends('layouts.admin')

@section('title', 'Create Page')
@section('header', 'Create Page')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Create New Static Page</h2>
    </div>

    <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        {{-- Left column (Main content) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700">Page Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="e.g. About Us"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700">Page Content</label>
                    <textarea name="content" id="content" rows="15" placeholder="Write page content in HTML or plain text..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm @error('content') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('content') }}</textarea>
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
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" placeholder="Default matches page title"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-semibold text-gray-700">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" placeholder="Brief summary of the page for search result snippets..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description') }}</textarea>
                </div>

                <div>
                    <label for="meta_keywords" class="block text-sm font-semibold text-gray-700">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="e.g. about, company, blog, niche"
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
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
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

                <div class="pt-4 border-t border-gray-100 space-y-4">
                    <label class="block text-sm font-semibold text-gray-700">Page Navigation Visibility</label>
                    
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="show_in_header" value="1" {{ old('show_in_header', 0) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Show in Header Navigation</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', 1) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Show in Footer Quick Links</span>
                    </label>
                </div>

                <div>
                    <label for="featured_image" class="block text-sm font-semibold text-gray-700">Featured Image</label>
                    <input type="file" name="featured_image" id="featured_image"
                           class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <div class="flex items-center gap-2 mt-3">
                        <button type="button" onclick="selectFeaturedImage()" 
                                class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 font-semibold text-xs rounded-lg border border-indigo-100 dark:border-indigo-900/50 transition-colors flex items-center gap-1.5 cursor-pointer">
                            🖼️ Select from Media Library
                        </button>
                    </div>
                    @error('featured_image')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 flex flex-col gap-3">
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
                    Create Page
                </button>
                <a href="{{ route('admin.pages.index') }}" class="w-full py-2.5 text-center text-gray-750 font-semibold text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
                    Cancel
                </a>
            </div>
        </div>
    </form>

    {{-- Custom Media Picker Modal (Alpine.js) --}}
    <div x-data="mediaPicker()" 
         x-show="isOpen" 
         @open-media-picker.window="open($event.detail.callback)"
         class="fixed inset-0 z-[99999] overflow-y-auto" 
         style="display: none;">
         
        {{-- Dark Backdrop --}}
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" @click="close()"></div>
        
        {{-- Modal Box --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-150 dark:border-slate-800 flex flex-col max-h-[85vh]">
                
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-150 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🖼️</span>
                        <h3 class="text-base font-bold text-gray-800 dark:text-slate-100">Select Image from Media Library</h3>
                    </div>
                    <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-350 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Toolbar: Search & Async Upload --}}
                <div class="px-6 py-4 bg-gray-50/50 dark:bg-slate-905/30 border-b border-gray-150 dark:border-slate-800 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    {{-- Search --}}
                    <div class="relative w-full sm:max-w-xs">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchImages()" placeholder="Search gallery images..." 
                               class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-gray-850 dark:text-slate-200 focus:ring-2 focus:ring-indigo-300">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Direct Upload --}}
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <label class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 border border-indigo-100 dark:border-indigo-900/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Upload New Image</span>
                            <input type="file" class="hidden" @change="uploadImage($event)" accept="image/*">
                        </label>
                    </div>
                </div>

                {{-- Content / Grid --}}
                <div class="flex-1 overflow-y-auto p-6 min-h-[300px]">
                    {{-- Loading State --}}
                    <div x-show="loading" class="flex flex-col items-center justify-center h-full space-y-3 py-12">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-500">Loading gallery images...</span>
                    </div>

                    {{-- Image Grid --}}
                    <div x-show="!loading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <template x-for="img in images" :key="img.id">
                            <div @click="selectImage(img)" class="group bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 overflow-hidden shadow-sm hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all cursor-pointer relative flex flex-col">
                                <div class="h-28 bg-gray-100 dark:bg-slate-900 overflow-hidden flex items-center justify-center relative">
                                    <img :src="img.url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" :alt="img.file_name">
                                    <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="bg-indigo-600 text-white font-bold text-[10px] px-2.5 py-1 rounded-full shadow-md">Select</span>
                                    </div>
                                </div>
                                <div class="p-2 min-w-0 flex-1">
                                    <span class="block text-[10px] font-semibold text-gray-800 dark:text-slate-200 truncate" x-text="img.file_name" :title="img.file_name"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="!loading && images.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="text-4xl">📸</span>
                        <h4 class="mt-2 text-sm font-bold text-gray-700 dark:text-slate-300">No images found</h4>
                        <p class="text-xs text-gray-400 mt-1">Upload images to get started!</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-150 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex justify-end">
                    <button type="button" @click="close()" class="px-4 py-2 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-350 rounded-lg text-xs font-bold transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.tiptap')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize main body editor
        initEditor('#content', 500);
    });

    function mediaPicker() {
        return {
            isOpen: false,
            loading: false,
            images: [],
            searchQuery: '',
            currentCallback: null,

            init() {
                window.openMediaPicker = (callback) => {
                    this.currentCallback = callback;
                    this.isOpen = true;
                    this.searchQuery = '';
                    this.fetchImages();
                };
            },

            open(callback) {
                this.currentCallback = callback;
                this.isOpen = true;
                this.searchQuery = '';
                this.fetchImages();
            },

            close() {
                this.isOpen = false;
                this.currentCallback = null;
            },

            fetchImages() {
                this.loading = true;
                fetch('{{ route('admin.media.json-list', [], false) }}?search=' + encodeURIComponent(this.searchQuery))
                    .then(res => res.json())
                    .then(data => {
                        this.images = data;
                        this.loading = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.loading = false;
                    });
            },

            uploadImage(event) {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                this.loading = true;
                fetch('{{ route('admin.media.json-upload', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) throw new Error('Upload failed');
                    return res.json();
                })
                .then(data => {
                    this.fetchImages();
                    if (this.currentCallback) {
                        this.currentCallback(data.location, { alt: file.name, path: data.path });
                        this.close();
                    }
                })
                .catch(err => {
                    alert('Upload failed: ' + err.message);
                    this.loading = false;
                });
            },

            selectImage(img) {
                if (this.currentCallback) {
                    this.currentCallback(img.url, { alt: img.file_name, path: img.file_path });
                }
                this.close();
            }
        };
    }

    function selectFeaturedImage() {
        if (window.openMediaPicker) {
            window.openMediaPicker(function(url, meta) {
                const previewContainer = document.getElementById('image-preview-container');
                if (previewContainer) {
                    previewContainer.innerHTML = `<img src='${url}' class='w-full h-32 object-cover rounded-lg border shadow-sm' />
                                                  <input type='hidden' name='generated_image_path' value='${meta.path}' />`;
                } else {
                    const parent = document.getElementById('featured_image').parentElement;
                    const preview = document.createElement('div');
                    preview.id = 'image-preview-container';
                    preview.className = 'mb-2';
                    preview.innerHTML = `<img src='${url}' class='w-full h-32 object-cover rounded-lg border shadow-sm' />
                                         <input type='hidden' name='generated_image_path' value='${meta.path}' />`;
                    parent.insertBefore(preview, parent.firstChild);
                }
            });
        }
    }
</script>
@endpush
