@extends('layouts.admin')

@section('title', 'AI Assistant')
@section('header', 'AI Writing & Design Suite')

@section('content')
<div x-data="{ activeTab: 'writer' }" class="space-y-6 max-w-6xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-slate-100">✨ AI Assistant</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                Automate your workflow with AI content writing, instant grammar audits, and stock image asset generation.
            </p>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200 dark:border-slate-700">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'writer'"
                    type="button"
                    :class="activeTab === 'writer' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 focus:outline-none cursor-pointer">
                📝 AI Content Generator
            </button>
            <button @click="activeTab = 'grammar'"
                    type="button"
                    :class="activeTab === 'grammar' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 focus:outline-none cursor-pointer">
                🔍 AI Grammar Checker
            </button>
            <button @click="activeTab = 'designer'"
                    type="button"
                    :class="activeTab === 'designer' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 focus:outline-none cursor-pointer">
                🎨 AI Image Generator
            </button>
        </nav>
    </div>

    {{-- 1. AI Content Generator Tab --}}
    <div x-show="activeTab === 'writer'"
         x-data="{ 
             title: '', 
             loading: false, 
             result: null,
             generate() {
                 if(!this.title) { alert('Please enter a topic title!'); return; }
                 this.loading = true;
                 this.result = null;
                 fetch('{{ route('admin.ai.generate-article') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({ title: this.title })
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.loading = false;
                     if(data.success) { this.result = data; }
                 })
                 .catch(() => { this.loading = false; alert('Failed to contact AI generator.'); });
             }
         }"
         class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Inputs Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 h-fit space-y-4">
            <h3 class="font-bold text-gray-800 dark:text-slate-200 text-base">Generate Article Draft</h3>
            <p class="text-xs text-gray-400">Specify an article title, and our AI writer will generate a full structured post body, excerpt, keywords, and description.</p>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Target Topic / Title</label>
                <input x-model="title" type="text" placeholder="e.g. Beginners Guide to Learning Python"
                       class="block w-full rounded-lg border-gray-300 dark:border-slate-650 dark:bg-slate-700 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>

            <button @click="generate()" :disabled="loading"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                <span x-show="!loading">⚡ Generate Article</span>
                <span x-show="loading" class="flex items-center gap-1.5">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Writing content...
                </span>
            </button>
        </div>

        {{-- Results --}}
        <div class="lg:col-span-2 space-y-6">
            <div x-show="!result && !loading" class="bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 p-12 text-center text-gray-400">
                <p class="text-sm">Input a title on the left and click generate to invoke the AI Content Suite.</p>
            </div>

            <div x-show="loading" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-12 text-center flex flex-col items-center justify-center space-y-4">
                <svg class="animate-spin h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold text-gray-700 dark:text-slate-300">Generating structured content draft...</p>
                <p class="text-xs text-gray-400">Mapping headings, creating descriptions, and extracting SEO tags.</p>
            </div>

            <div x-show="result" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 space-y-6" style="display: none;">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-3">
                    <h4 class="font-bold text-gray-800 dark:text-slate-200 text-md">AI Generated Draft</h4>
                    <span class="text-xs bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 px-2 py-0.5 rounded font-bold uppercase">Ready</span>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Generated Title</h5>
                    <p class="text-base font-bold text-gray-900 dark:text-white mt-1" x-text="result ? result.title : ''"></p>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Generated Summary / Excerpt</h5>
                    <p class="text-sm text-gray-750 dark:text-slate-300 mt-1 italic" x-text="result ? result.excerpt : ''"></p>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Generated Body (HTML Preview)</h5>
                    <div class="mt-2 p-4 bg-gray-50 dark:bg-slate-900/30 border border-gray-200 dark:border-slate-800 rounded-lg text-sm prose dark:prose-invert font-mono max-h-80 overflow-y-auto" x-html="result ? result.content : ''"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-150 dark:border-slate-700">
                    <div>
                        <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Tags</h5>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <template x-for="tag in (result ? result.tags : [])">
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400 rounded text-xs font-semibold" x-text="'#' + tag"></span>
                            </template>
                        </div>
                    </div>
                    <div>
                        <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Keywords</h5>
                        <p class="text-xs text-gray-650 dark:text-slate-300 font-mono mt-2" x-text="result ? result.keywords : ''"></p>
                    </div>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">SEO Description</h5>
                    <p class="text-xs text-gray-650 dark:text-slate-300 font-sans mt-2" x-text="result ? result.seo_description : ''"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. AI Grammar Checker Tab --}}
    <div x-show="activeTab === 'grammar'"
         x-data="{ 
             content: '', 
             loading: false, 
             result: null,
             audit() {
                 if(!this.content) { alert('Please enter some text to check!'); return; }
                 this.loading = true;
                 this.result = null;
                 fetch('{{ route('admin.ai.check-grammar') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({ content: this.content })
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.loading = false;
                     if(data.success) { this.result = data; }
                 })
                 .catch(() => { this.loading = false; alert('Failed to audit text.'); });
             }
         }"
         class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         style="display: none;">
        
        {{-- Inputs Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 h-fit space-y-4">
            <h3 class="font-bold text-gray-800 dark:text-slate-200 text-base">Auditor & Tone Enhancer</h3>
            <p class="text-xs text-gray-400">Paste any blog paragraph or content block below to check for spelling, grammar improvements, and tone reviews.</p>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Source Paragraph</label>
                <textarea x-model="content" rows="6" placeholder="Paste your article paragraph here..."
                          class="block w-full rounded-lg border-gray-300 dark:border-slate-650 dark:bg-slate-700 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
            </div>

            <button @click="audit()" :disabled="loading"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                <span x-show="!loading">🔍 Audit Grammar & Spelling</span>
                <span x-show="loading" class="flex items-center gap-1.5">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Auditing text...
                </span>
            </button>
        </div>

        {{-- Results --}}
        <div class="lg:col-span-2 space-y-6">
            <div x-show="!result && !loading" class="bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 p-12 text-center text-gray-400">
                <p class="text-sm">Input your paragraph on the left and click audit to check for spelling and grammar suggestions.</p>
            </div>

            <div x-show="loading" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-12 text-center flex flex-col items-center justify-center space-y-4">
                <svg class="animate-spin h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold text-gray-700 dark:text-slate-300">Auditing syntax, style, and spelling flow...</p>
            </div>

            <div x-show="result" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 space-y-6" style="display: none;">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-3">
                    <h4 class="font-bold text-gray-800 dark:text-slate-200 text-md">AI Grammar & Tone suggestions</h4>
                    <span class="text-xs bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400 px-2 py-0.5 rounded font-bold">Analysis Complete</span>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Spelling Checks</h5>
                    <p class="text-sm text-gray-750 dark:text-slate-350 mt-1" x-text="result ? result.spelling : ''"></p>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Grammar Insights</h5>
                    <p class="text-sm text-gray-750 dark:text-slate-350 mt-1" x-text="result ? result.grammar : ''"></p>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Tone & Style Recommendations</h5>
                    <ul class="list-disc pl-5 text-sm text-gray-750 dark:text-slate-300 space-y-1 mt-2">
                        <template x-for="item in (result ? result.tone_suggestions : [])">
                            <li x-text="item"></li>
                        </template>
                    </ul>
                </div>

                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Suggested Corrections</h5>
                    <div class="mt-2 overflow-hidden border border-gray-250 dark:border-slate-700 rounded-lg">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700 text-xs font-bold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2">Found Text</th>
                                    <th class="px-4 py-2">Suggested Replacement</th>
                                    <th class="px-4 py-2">Type</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700 text-gray-700 dark:text-slate-300">
                                <template x-for="corr in (result ? result.corrections : [])">
                                    <tr>
                                        <td class="px-4 py-2 text-red-650 font-mono" x-text="corr.original text-strike"></td>
                                        <td class="px-4 py-2 text-green-700 font-mono font-bold" x-text="corr.suggested"></td>
                                        <td class="px-4 py-2"><span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700" x-text="corr.type"></span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. AI Image Generator Tab --}}
    <div x-show="activeTab === 'designer'"
         x-data="{ 
             prompt: '', 
             loading: false, 
             result: null,
             generate() {
                 if(!this.prompt) { alert('Please enter an image prompt!'); return; }
                 this.loading = true;
                 this.result = null;
                 fetch('{{ route('admin.ai.generate-image') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({ prompt: this.prompt })
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.loading = false;
                     if(data.success) { this.result = data; }
                 })
                 .catch(() => { this.loading = false; alert('Failed to generate image.'); });
             }
         }"
         class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         style="display: none;">
        
        {{-- Inputs Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 h-fit space-y-4">
            <h3 class="font-bold text-gray-800 dark:text-slate-200 text-base">Generate Visual Assets</h3>
            <p class="text-xs text-gray-400">Describe an image prompt. The AI will download a beautiful matching high-res image and save it in your CMS Media Library.</p>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Image Prompt</label>
                <input x-model="prompt" type="text" placeholder="e.g. clean code workspace workspace, programming background"
                       class="block w-full rounded-lg border-gray-300 dark:border-slate-650 dark:bg-slate-700 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>

            <button @click="generate()" :disabled="loading"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                <span x-show="!loading">🎨 Generate Stock Asset</span>
                <span x-show="loading" class="flex items-center gap-1.5">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Generating image...
                </span>
            </button>
        </div>

        {{-- Results --}}
        <div class="lg:col-span-2 space-y-6">
            <div x-show="!result && !loading" class="bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 p-12 text-center text-gray-400">
                <p class="text-sm">Input an image prompt on the left and click generate to download stock assets.</p>
            </div>

            <div x-show="loading" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-12 text-center flex flex-col items-center justify-center space-y-4">
                <svg class="animate-spin h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold text-gray-700 dark:text-slate-300">Retrieving royalty-free HD asset...</p>
                <p class="text-xs text-gray-400">Saving to storage and registering media log in the database.</p>
            </div>

            <div x-show="result" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 space-y-4" style="display: none;">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-3">
                    <h4 class="font-bold text-gray-800 dark:text-slate-200 text-md">AI Generated Asset</h4>
                    <span class="text-xs bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 px-2 py-0.5 rounded font-bold uppercase">Downloaded</span>
                </div>

                <div class="aspect-video w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:border-slate-700">
                    <img :src="result ? result.url : ''" class="w-full h-full object-cover" alt="Generated stock asset">
                </div>

                <div class="bg-gray-50 dark:bg-slate-900/30 p-4 rounded-xl border border-gray-200 dark:border-slate-800 text-xs font-mono space-y-2 text-gray-650 dark:text-slate-350">
                    <p><strong class="text-gray-900 dark:text-slate-200">Public URL:</strong> <span x-text="result ? result.url : ''"></span></p>
                    <p><strong class="text-gray-900 dark:text-slate-200">Storage Path:</strong> <span x-text="result ? result.path : ''"></span></p>
                    <p><strong class="text-gray-900 dark:text-slate-200">Filename:</strong> <span x-text="result ? result.fileName : ''"></span></p>
                    <p><strong class="text-gray-900 dark:text-slate-200">Media ID:</strong> <span x-text="result ? result.media_id : 'N/A'"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
