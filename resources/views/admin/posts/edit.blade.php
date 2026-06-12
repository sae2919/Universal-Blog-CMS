@extends('layouts.admin')

@section('title', 'Edit Post')
@section('header', 'Edit Post')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.posts.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Edit Article — {{ $post->title }}</h2>
    </div>

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        @method('PUT')

        {{-- Left Column (Post content, Body, SEO) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Main Form Fields --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700">Article Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" placeholder="e.g. 10 Best Practices for Laravel Developers"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="excerpt" class="block text-sm font-semibold text-gray-700">Excerpt / Short Description</label>
                    <textarea name="excerpt" id="excerpt" rows="3" placeholder="Brief summary of the article..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('excerpt') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700">Post Content</label>
                    <textarea name="content" id="content" rows="18" placeholder="Write post body in Markdown or HTML..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm @error('content') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- FAQ Section --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-md font-bold text-gray-800">Frequently Asked Questions (FAQs)</h3>
                    <button type="button" id="add-faq-btn" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add FAQ
                    </button>
                </div>

                <div id="faq-container" class="space-y-4">
                    {{-- FAQ rows will be appended here dynamically --}}
                    @php 
                        $faqs = old('faqs', $post->faqs ?? []);
                    @endphp
                    @if(is_array($faqs))
                        @foreach($faqs as $index => $faq)
                            <div class="faq-row bg-gray-50 dark:bg-slate-800/40 p-4 rounded-xl border border-gray-200 dark:border-slate-800 relative space-y-3" data-index="{{ $index }}">
                                <button type="button" class="remove-faq-btn absolute top-3 right-3 text-gray-400 hover:text-red-650 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Question</label>
                                    <input type="text" name="faqs[{{ $index }}][question]" value="{{ $faq['question'] ?? '' }}" required
                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Answer (Rich Text)</label>
                                    <textarea id="faq-answer-{{ $index }}" name="faqs[{{ $index }}][answer]" rows="3" required
                                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ $faq['answer'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div id="faq-empty-state" class="text-center py-6 text-gray-400 {{ is_array($faqs) && count($faqs) > 0 ? 'hidden' : '' }}">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium">No FAQs added yet.</p>
                    <p class="text-xs mt-1 text-gray-400">Add questions and answers relevant to this article to help reader engagement & SEO.</p>
                </div>
            </div>

            {{-- Custom CTA Section --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Custom Call-to-Action (CTA)</h3>
                
                <p class="text-xs text-gray-400">If you leave these fields blank, no CTA will be displayed for this post.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cta_title" class="block text-sm font-semibold text-gray-700">CTA Title</label>
                        <input type="text" name="cta_title" id="cta_title" value="{{ old('cta_title', $post->cta_title) }}" placeholder="e.g. Start Your Coding & Data Science Journey Today"
                               class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <div>
                        <label for="cta_button_text" class="block text-sm font-semibold text-gray-700">CTA Button Text</label>
                        <input type="text" name="cta_button_text" id="cta_button_text" value="{{ old('cta_button_text', $post->cta_button_text) }}" placeholder="e.g. Find Coding & Data Science Tutors"
                               class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cta_description" class="block text-sm font-semibold text-gray-700">CTA Description</label>
                        <textarea name="cta_description" id="cta_description" rows="3" placeholder="e.g. Connect with expert mentors who will guide you..."
                                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('cta_description', $post->cta_description) }}</textarea>
                    </div>

                    <div>
                        <label for="cta_button_link" class="block text-sm font-semibold text-gray-700">CTA Button Link</label>
                        <input type="text" name="cta_button_link" id="cta_button_link" value="{{ old('cta_button_link', $post->cta_button_link) }}" placeholder="e.g. /search/tutors"
                               class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        
                        <div class="mt-4">
                            <label for="cta_bg_image" class="block text-sm font-semibold text-gray-700">CTA Background Image</label>
                            @if($post->cta_bg_image)
                                <div class="mt-1 mb-2 text-xs text-gray-400">
                                    Current: <a href="{{ asset('storage/' . $post->cta_bg_image) }}" target="_blank" class="text-indigo-650 hover:underline">View Image</a>
                                </div>
                            @endif
                            <input type="file" name="cta_bg_image" id="cta_bg_image"
                                   class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-6">
                    <h4 class="font-bold text-gray-850 text-sm">Custom Links Directory (Optional)</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cta_directory_title" class="block text-sm font-semibold text-gray-700">Directory Section Title</label>
                            <input type="text" name="cta_directory_title" id="cta_directory_title" value="{{ old('cta_directory_title', $post->cta_directory_title) }}" placeholder="e.g. Explore Coding & Data Science Tutors by Location"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label for="cta_directory_subtitle" class="block text-sm font-semibold text-gray-700">Directory Section Subtitle</label>
                            <input type="text" name="cta_directory_subtitle" id="cta_directory_subtitle" value="{{ old('cta_directory_subtitle', $post->cta_directory_subtitle) }}" placeholder="e.g. Find Expert programming and data science mentors across India"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="cta_col1_title" class="block text-sm font-semibold text-gray-700">Column 1 Title</label>
                            <input type="text" name="cta_col1_title" id="cta_col1_title" value="{{ old('cta_col1_title', $post->cta_col1_title) }}" placeholder="e.g. Popular courses"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            
                            <label for="cta_col1_links" class="block text-xs font-semibold text-gray-500 mt-3">Column 1 Links (One per line: Title | URL)</label>
                            <textarea name="cta_col1_links" id="cta_col1_links" rows="6" placeholder="Python Programming | /courses/python&#10;Data Science | /courses/data-science"
                                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-mono">{{ old('cta_col1_links', $post->cta_col1_links) }}</textarea>
                        </div>

                        <div>
                            <label for="cta_col2_title" class="block text-sm font-semibold text-gray-700">Column 2 Title</label>
                            <input type="text" name="cta_col2_title" id="cta_col2_title" value="{{ old('cta_col2_title', $post->cta_col2_title) }}" placeholder="e.g. Popular Locations"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            
                            <label for="cta_col2_links" class="block text-xs font-semibold text-gray-500 mt-3">Column 2 Links (One per line: Title | URL)</label>
                            <textarea name="cta_col2_links" id="cta_col2_links" rows="6" placeholder="Bangalore | /tutors/bangalore&#10;Hyderabad | /tutors/hyderabad"
                                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-mono">{{ old('cta_col2_links', $post->cta_col2_links) }}</textarea>
                        </div>

                        <div>
                            <label for="cta_col3_title" class="block text-sm font-semibold text-gray-700">Column 3 Title</label>
                            <input type="text" name="cta_col3_title" id="cta_col3_title" value="{{ old('cta_col3_title', $post->cta_col3_title) }}" placeholder="e.g. Popular Searches"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            
                            <label for="cta_col3_links" class="block text-xs font-semibold text-gray-500 mt-3">Column 3 Links (One per line: Title | URL)</label>
                            <textarea name="cta_col3_links" id="cta_col3_links" rows="6" placeholder="Learn Python free | /search?q=python&#10;Data science tutorial | /search?q=ds"
                                      class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-mono">{{ old('cta_col3_links', $post->cta_col3_links) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Metadata --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Search Engine Optimization (SEO)</h3>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="meta_title" class="block text-sm font-semibold text-gray-700">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}" placeholder="Defaults to post title if blank"
                               class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-semibold text-gray-700">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3" placeholder="Brief search snippet description..."
                                  class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description', $post->meta_description) }}</textarea>
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-sm font-semibold text-gray-700">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}" placeholder="Comma-separated keywords"
                               class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                        <div>
                            <label for="og_title" class="block text-sm font-semibold text-gray-700">OG (Facebook/Twitter) Title</label>
                            <input type="text" name="og_title" id="og_title" value="{{ old('og_title', $post->og_title) }}" placeholder="OG Title"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="og_description" class="block text-sm font-semibold text-gray-700">OG Description</label>
                            <input type="text" name="og_description" id="og_description" value="{{ old('og_description', $post->og_description) }}" placeholder="OG Description"
                                   class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column (Taxonomy, Image, Publishing) --}}
        <div class="space-y-6">
            {{-- Publish Widget --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Publish Settings</h3>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ old('status', $post->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div>
                    <label for="published_at" class="block text-sm font-semibold text-gray-700">Publish Date / Time</label>
                    <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Feature this article</span>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_trending" value="1" {{ old('is_trending', $post->is_trending) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Mark as Trending</span>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', $post->allow_comments) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Allow Comments</span>
                    </label>
                </div>
            </div>

            {{-- Categories and Tags --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Taxonomy</h3>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Tags</label>
                    <div class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                        @php $postTags = $post->tags->pluck('id')->toArray(); @endphp
                        @foreach($tags as $tag)
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $postTags)) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">#{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-4">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Media</h3>

                <div>
                    <label for="featured_image" class="block text-sm font-semibold text-gray-700">Featured Image</label>
                    @if($post->featured_image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="">
                        </div>
                    @endif
                    <input type="file" name="featured_image" id="featured_image"
                           class="mt-3 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('featured_image')
                        <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 flex flex-col gap-3">
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
                    Save Changes
                </button>
                <a href="{{ route('admin.posts.index') }}" class="w-full py-2.5 text-center text-gray-750 font-semibold text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TinyMCE for main body content
        // Base config for all TinyMCE editors
        const baseConfig = {
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image insertslider | removeformat code',
            branding: false,
            promotion: false,
            link_target_list: [
                {title: 'Same page', value: '_self'},
                {title: 'New tab', value: '_blank'}
            ],
            rel_list: [
                {title: 'Default (Do Follow)', value: ''},
                {title: 'No Follow', value: 'nofollow'},
                {title: 'Do Follow', value: 'dofollow'}
            ],
            extended_valid_elements: 'a[href|target|rel|class],div[class]',
            setup: function (editor) {
                editor.ui.registry.addButton('insertslider', {
                    text: 'Insert Slider',
                    icon: 'gallery',
                    tooltip: 'Insert Image Slider Block',
                    onAction: function () {
                        editor.insertContent(
                            '<div class="post-slider bg-gray-50 dark:bg-slate-800 p-4 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 flex gap-4 overflow-x-auto min-h-[120px] items-center justify-start" style="display:flex; gap:10px; border:1px dashed #ccc; padding:10px; border-radius:8px;">' +
                            '  <img src="https://images.unsplash.com/photo-1516116211223-4c599701b844?w=500" style="width:150px; height:100px; object-fit:cover; border-radius:4px;" />' +
                            '  <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=500" style="width:150px; height:100px; object-fit:cover; border-radius:4px;" />' +
                            '</div><p>&nbsp;</p>'
                        );
                    }
                });
            }
        };

        function initEditor(selector, height = 500) {
            tinymce.init({
                ...baseConfig,
                selector: selector,
                height: height
            });
        }

        // Initialize main body editor
        initEditor('#content', 500);

        // FAQ interactive manager
        const faqContainer = document.getElementById('faq-container');
        const addFaqBtn = document.getElementById('add-faq-btn');
        const faqEmptyState = document.getElementById('faq-empty-state');
        let faqIndex = faqContainer.querySelectorAll('.faq-row').length;

        function toggleEmptyState() {
            if (faqContainer.querySelectorAll('.faq-row').length === 0) {
                faqEmptyState.classList.remove('hidden');
            } else {
                faqEmptyState.classList.add('hidden');
            }
        }

        // Init existing on page load
        faqContainer.querySelectorAll('.faq-row textarea').forEach(textarea => {
            initEditor(`#${textarea.id}`, 250);
        });

        addFaqBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'faq-row bg-gray-50 dark:bg-slate-800/40 p-4 rounded-xl border border-gray-200 dark:border-slate-800 relative space-y-3';
            row.dataset.index = faqIndex;
            row.innerHTML = `
                <button type="button" class="remove-faq-btn absolute top-3 right-3 text-gray-400 hover:text-red-650 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Question</label>
                    <input type="text" name="faqs[${faqIndex}][question]" required
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Answer (Rich Text)</label>
                    <textarea id="faq-answer-${faqIndex}" name="faqs[${faqIndex}][answer]" rows="3" required
                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                </div>
            `;
            faqContainer.appendChild(row);
            initEditor(`#faq-answer-${faqIndex}`, 250);
            faqIndex++;
            toggleEmptyState();
        });

        faqContainer.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-faq-btn');
            if (removeBtn) {
                const row = removeBtn.closest('.faq-row');
                const textarea = row.querySelector('textarea');
                if (textarea && textarea.id && tinymce.get(textarea.id)) {
                    tinymce.get(textarea.id).remove();
                }
                row.remove();
                toggleEmptyState();
            }
        });
    });
</script>
@endpush
