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
        <input type="hidden" name="image_metadata" id="image_metadata_input">

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
                                    @error("faqs.{$index}.question")
                                        <p class="mt-1 text-xs text-red-650">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Answer (Rich Text)</label>
                                    <textarea id="faq-answer-{{ $index }}" name="faqs[{{ $index }}][answer]" rows="3"
                                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ $faq['answer'] ?? '' }}</textarea>
                                    @error("faqs.{$index}.answer")
                                        <p class="mt-1 text-xs text-red-650">{{ $message }}</p>
                                    @enderror
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

            @php
                $hasCtaPlaceholder = strpos($post->content, 'post-cta') !== false || strpos(old('content', ''), 'post-cta') !== false;
            @endphp
            {{-- Custom CTA Section --}}
            <div id="custom-cta-section" class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6 {{ $hasCtaPlaceholder ? '' : 'hidden' }}">
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
            {{-- AI Copilot Widget --}}
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/20 dark:to-purple-950/20 rounded-xl border border-indigo-150 dark:border-indigo-900 shadow-sm p-6 space-y-4"
                 x-data="{ 
                     aiPrompt: '', 
                     loading: false,
                     targetLang: 'hi',
                     generateAll() {
                          const titleVal = document.getElementById('title').value;
                          const excerptVal = document.getElementById('excerpt').value;
                          const contentVal = window.tiptapInstances && window.tiptapInstances['content'] 
                              ? window.tiptapInstances['content'].getHTML() 
                              : document.getElementById('content').value;
                          
                          const promptVal = this.aiPrompt;
                          
                          if (!promptVal && !titleVal && !excerptVal && !contentVal) {
                              alert('Please enter a Title, Excerpt, Content or AI Prompt first!');
                              return;
                          }

                          this.loading = true;
                          fetch('{{ route('admin.ai.generate-article') }}', {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                              },
                              body: JSON.stringify({ 
                                  title: titleVal || promptVal, 
                                  excerpt: excerptVal, 
                                  content: contentVal 
                              })
                          })
                          .then(res => res.json())
                          .then(data => {
                              this.loading = false;
                              if(data.success) {
                                  if (data.title) document.getElementById('title').value = data.title;
                                  if (data.excerpt) document.getElementById('excerpt').value = data.excerpt;
                                  if (data.title) document.getElementById('meta_title').value = data.title;
                                  if (data.seo_description) document.getElementById('meta_description').value = data.seo_description;
                                  if (data.keywords) document.getElementById('meta_keywords').value = data.keywords;
                                  
                                  if (data.content) {
                                      const contentEditor = window.tiptapInstances && window.tiptapInstances['content'];
                                      if (contentEditor) {
                                          contentEditor.commands.setContent(data.content);
                                      } else if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                          tinymce.get('content').setContent(data.content);
                                      } else {
                                          document.getElementById('content').value = data.content;
                                      }
                                  }

                                  if (data.faqs && data.faqs.length > 0) {
                                      const faqContainer = document.getElementById('faq-container');
                                      if (faqContainer) {
                                          const rows = faqContainer.querySelectorAll('.faq-row');
                                          rows.forEach(row => {
                                              const textarea = row.querySelector('textarea');
                                              if (textarea && textarea.id && window.tiptapInstances && window.tiptapInstances[textarea.id]) {
                                                  window.tiptapInstances[textarea.id].destroy();
                                                  delete window.tiptapInstances[textarea.id];
                                              }
                                              row.remove();
                                          });
                                          data.faqs.forEach(faq => {
                                              if (window.addFaqRow) {
                                                  window.addFaqRow(faq.question, faq.answer);
                                              }
                                          });
                                      }
                                  }

                                  alert('AI completion successfully finished!');
                              } else {
                                  alert(data.message || 'Failed to auto-complete fields.');
                              }
                          })
                          .catch(() => { this.loading = false; alert('Failed to contact AI Assistant.'); });
                      },
                      correctGrammar() {
                          const contentVal = window.tiptapInstances && window.tiptapInstances['content'] 
                              ? window.tiptapInstances['content'].getHTML() 
                              : document.getElementById('content').value;
                          if(!contentVal || contentVal.length < 10) { alert('Write some content first to correct grammar!'); return; }
                          this.loading = true;
                          fetch('{{ route('admin.ai.correct-grammar') }}', {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                              },
                              body: JSON.stringify({ content: contentVal })
                          })
                          .then(res => res.json())
                          .then(data => {
                              this.loading = false;
                              if(data.success && data.corrected_content) {
                                  const contentEditor = window.tiptapInstances && window.tiptapInstances['content'];
                                  if (contentEditor) {
                                      contentEditor.commands.setContent(data.corrected_content);
                                  } else if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                      tinymce.get('content').setContent(data.corrected_content);
                                  } else {
                                      document.getElementById('content').value = data.corrected_content;
                                  }
                                  alert('Spelling and grammar successfully corrected!');
                              } else {
                                  alert('Failed to correct spelling and grammar.');
                              }
                          })
                          .catch(() => { this.loading = false; alert('Failed to auto-correct grammar.'); });
                      },
                     translatePost() {
                         const title = document.getElementById('title').value;
                         const excerpt = document.getElementById('excerpt').value;
                         const content = window.tiptapInstances && window.tiptapInstances['content'] 
                             ? window.tiptapInstances['content'].getHTML() 
                             : document.getElementById('content').value;
                         const metaTitle = document.getElementById('meta_title').value;
                         const metaDescription = document.getElementById('meta_description').value;

                         if (!title || !content) {
                             alert('Please write or generate some content first before translating!');
                             return;
                         }

                         this.loading = true;
                         fetch('{{ route('admin.ai.translate-post') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                             },
                             body: JSON.stringify({
                                 title: title,
                                 excerpt: excerpt,
                                 content: content,
                                 meta_title: metaTitle,
                                 meta_description: metaDescription,
                                 target_lang: this.targetLang
                             })
                         })
                         .then(res => res.json())
                         .then(data => {
                             this.loading = false;
                             if (data.success) {
                                 document.getElementById('title').value = data.title;
                                 document.getElementById('excerpt').value = data.excerpt;
                                 document.getElementById('meta_title').value = data.meta_title;
                                 document.getElementById('meta_description').value = data.meta_description;
                                 
                                 const contentEditor = window.tiptapInstances && window.tiptapInstances['content'];
                                 if (contentEditor) {
                                     contentEditor.commands.setContent(data.content);
                                 } else if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                     tinymce.get('content').setContent(data.content);
                                 } else {
                                     document.getElementById('content').value = data.content;
                                 }

                                 document.getElementById('locale').value = this.targetLang;
                                 alert('Article successfully translated via AI and filled in form fields!');
                             } else {
                                 alert(data.message || 'Failed to translate content.');
                             }
                         })
                         .catch(() => { this.loading = false; alert('Failed to translate content.'); });
                     }
                 }">
                <div class="flex items-center gap-2 border-b border-indigo-100 dark:border-indigo-900 pb-2">
                    <span class="text-lg">✨</span>
                    <h3 class="font-bold text-gray-800 dark:text-slate-100 text-sm">AI Copilot</h3>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase">AI Prompt / Topic Title</label>
                        <input x-model="aiPrompt" type="text" placeholder="Topic details or leave blank to use title..."
                               class="mt-1 block w-full rounded-lg border-indigo-200 dark:border-indigo-900 dark:bg-slate-700 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 gap-2 pt-1">
                        <button type="button" @click="generateAll()" :disabled="loading"
                                class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                            Generate / Complete Fields & FAQs
                        </button>
                        
                        <button type="button" @click="correctGrammar()" :disabled="loading"
                                class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                            Auto-Correct Grammar & Spelling
                        </button>
                    </div>
                </div>
                
                <div x-show="loading" class="flex items-center justify-center gap-2 pt-1" style="display: none;">
                    <svg class="animate-spin h-3.5 w-3.5 text-indigo-650" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-[10px] font-semibold text-gray-500">AI is processing request...</span>
                </div>
            </div>

            {{-- Publish Widget --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6" x-data="{ status: '{{ old('status', $post->status) }}' }">
                <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3">Publish Settings</h3>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                    <select name="status" id="status" x-model="status" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div x-show="status === 'scheduled'">
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

                <input type="hidden" name="locale" id="locale" value="{{ old('locale', $post->locale) }}">

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
                    <div id="image-preview-container" x-data="{ hasImage: @json(!!$post->featured_image), removeImage: false }">
                        @if($post->featured_image)
                            <div class="mt-2 relative" x-show="hasImage && !removeImage">
                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="">
                                <button type="button" @click="removeImage = true; document.getElementById('remove_featured_image').value = '1'" 
                                        class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1.5 shadow-md transition-colors cursor-pointer" title="Remove Image">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="remove_featured_image" id="remove_featured_image" value="0">
                            <div x-show="removeImage" class="mt-2 text-xs text-red-650 font-semibold" style="display: none;">
                                Image will be removed upon saving.
                                <button type="button" @click="removeImage = false; document.getElementById('remove_featured_image').value = '0'" class="text-indigo-600 hover:underline ml-2 cursor-pointer">Undo</button>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="featured_image" id="featured_image"
                           class="mt-3 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
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
    if (window.initializeImageRegistry) {
        window.initializeImageRegistry(@json($post->image_metadata ?? (object)[]));
    }

    document.addEventListener('DOMContentLoaded', function() {
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

        window.addFaqRow = function(questionText = '', answerText = '') {
            const row = document.createElement('div');
            row.className = 'faq-row bg-gray-50 dark:bg-slate-800/40 p-4 rounded-xl border border-gray-200 dark:border-slate-800 relative space-y-3';
            row.dataset.index = faqIndex;
            row.innerHTML = `
                <button type="button" class="remove-faq-btn absolute top-3 right-3 text-gray-400 hover:text-red-650 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Question</label>
                    <input type="text" name="faqs[${faqIndex}][question]" value="${questionText.replace(/"/g, '&quot;')}" required
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Answer (Rich Text)</label>
                    <textarea id="faq-answer-${faqIndex}" name="faqs[${faqIndex}][answer]" rows="3"
                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">${answerText}</textarea>
                </div>
            `;
            faqContainer.appendChild(row);
            initEditor(`#faq-answer-${faqIndex}`, 250);
            
            // Set content in Tiptap instance if successfully loaded
            const instId = `faq-answer-${faqIndex}`;
            if (window.tiptapInstances && window.tiptapInstances[instId] && answerText) {
                window.tiptapInstances[instId].commands.setContent(answerText);
            }
            
            faqIndex++;
            toggleEmptyState();
        };

        addFaqBtn.addEventListener('click', function() {
            window.addFaqRow('', '');
        });

        faqContainer.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-faq-btn');
            if (removeBtn) {
                const row = removeBtn.closest('.faq-row');
                const textarea = row.querySelector('textarea');
                if (textarea && textarea.id) {
                    if (window.tiptapInstances && window.tiptapInstances[textarea.id]) {
                        window.tiptapInstances[textarea.id].destroy();
                        delete window.tiptapInstances[textarea.id];
                    } else if (typeof tinymce !== 'undefined' && tinymce.get(textarea.id)) {
                        tinymce.get(textarea.id).remove();
                    }
                }
                row.remove();
                toggleEmptyState();
            }
        });

        // Ensure Tiptap / TinyMCE contents are saved into their respective textareas before form submit
        const form = document.querySelector('form[action*="posts"]');
        if (form) {
            form.addEventListener('submit', function() {
                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }
                const imageMetadataInput = document.getElementById('image_metadata_input');
                if (imageMetadataInput) {
                    imageMetadataInput.value = JSON.stringify(window.uploadedImagesMap || {});
                }
            });
        }
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

                if (window.registerAndLogImageFile) {
                    window.registerAndLogImageFile(file);
                }

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
                        this.currentCallback(data.location, { 
                            alt: file.name, 
                            path: data.path,
                            mime_type: data.mime_type,
                            file_size: data.file_size
                        });
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
                    this.currentCallback(img.url, { 
                        alt: img.file_name, 
                        path: img.file_path,
                        mime_type: img.mime_type,
                        file_size: img.file_size
                    });
                }
                this.close();
            }
        };
    }

    function selectFeaturedImage() {
        if (window.openMediaPicker) {
            window.openMediaPicker(function(url, meta) {
                if (window.registerAndLogImageDetails) {
                    window.registerAndLogImageDetails(meta.alt, meta.mime_type, meta.file_size);
                }
                const previewContainer = document.getElementById('image-preview-container');
                const html = `
                    <div class="relative rounded-lg overflow-hidden border border-gray-200 shadow-sm max-h-40 bg-gray-50 flex items-center justify-center">
                        <img src="${url}" class="w-full h-32 object-cover" />
                        <input type="hidden" name="generated_image_path" value="${meta.path}" />
                        <button type="button" onclick="clearMediaLibrarySelection()" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1.5 shadow-md transition-colors cursor-pointer" title="Remove Selection">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>`;
                if (previewContainer) {
                    previewContainer.innerHTML = html;
                } else {
                    const parent = document.getElementById('featured_image').parentElement;
                    const preview = document.createElement('div');
                    preview.id = 'image-preview-container';
                    preview.className = 'mb-2';
                    preview.innerHTML = html;
                    parent.insertBefore(preview, parent.firstChild);
                }
            });
        }
    }
</script>
@endpush
