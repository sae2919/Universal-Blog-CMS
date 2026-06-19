@extends('layouts.admin')

@section('title', 'SEO Diagnostics')
@section('header', 'SEO Audit & Diagnostics')

@section('content')
<div class="space-y-8" x-data="{ 
    modalOpen: false, 
    loading: false,
    auditData: null,
    fetchAudit(type, id) {
        this.modalOpen = true;
        this.loading = true;
        this.auditData = null;
        fetch('/admin/seo/audit/' + type + '/' + id)
            .then(res => res.json())
            .then(data => {
                this.auditData = data;
                this.loading = false;
            })
            .catch(err => {
                console.error(err);
                this.loading = false;
            });
    }
}">
    {{-- SEO Health Overview --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 dark:text-slate-200 text-lg mb-2">🔍 Search Engine Optimization Health</h3>
        <p class="text-sm text-gray-500">Ensure all your articles and landing pages contain proper Meta Titles, Meta Descriptions, and canonical structure for optimal ranking.</p>
    </div>

    {{-- SEO Audit Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- SEO Score Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 flex flex-col items-center justify-center text-center">
            <h3 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-4">Overall SEO Score</h3>
            
            <div class="relative w-36 h-36 flex items-center justify-center">
                {{-- Circular Progress Bar --}}
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f3f4f6" stroke-width="8" fill="transparent" class="dark:stroke-slate-700" />
                    <circle cx="50" cy="50" r="40" stroke="#4f46e5" stroke-width="8" fill="transparent"
                            stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * ($overallScore / 100)) }}" stroke-linecap="round" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $overallScore }}</span>
                    <span class="text-xs text-gray-400 font-semibold uppercase">of 100</span>
                </div>
            </div>
            
            <div class="mt-4">
                <span class="px-2.5 py-1 {{ $healthClass }} border rounded-full text-xs font-bold uppercase">{{ $healthStatus }}</span>
            </div>
        </div>

        {{-- SEO Checks Card --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-800 dark:text-slate-200 mb-4 border-b border-gray-100 dark:border-slate-700 pb-3 flex items-center gap-2">
                <span>📋 Core SEO Audits Run</span>
                <span class="text-xs bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 font-semibold px-2 py-0.5 rounded">{{ $passedChecksCount }} Passed</span>
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($checks as $check)
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-750/30 rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="{{ $check['passed'] ? 'text-green-500' : 'text-red-500' }} text-lg font-bold flex-shrink-0 leading-none">
                        {{ $check['passed'] ? '✓' : '✗' }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-sm text-gray-900 dark:text-slate-100">{{ $check['name'] }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $check['passed'] ? 'bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-900' : 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900' }}">
                                {{ $check['score'] }}%
                            </span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-slate-400 block mt-1 leading-relaxed">{{ $check['description'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Post SEO Status --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30">
                <h4 class="font-bold text-gray-800 dark:text-slate-250 text-base">Latest Articles SEO Status</h4>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($posts as $post)
                    @php
                        $hasTitle = !empty($post->meta_title);
                        $hasDesc = !empty($post->meta_description);
                    @endphp
                    <div class="p-4 flex items-center justify-between gap-4 text-sm">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-gray-900 dark:text-white truncate block">{{ $post->title }}</span>
                            <span class="text-xs text-gray-400 block truncate font-mono">/{{ $post->category->slug }}/{{ $post->slug }}</span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasTitle ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasTitle ? 'Title ✓' : 'Title ✗' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasDesc ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasDesc ? 'Desc ✓' : 'Desc ✗' }}
                            </span>
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-xs text-indigo-650 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2 font-semibold">Edit</a>
                            <button @click="fetchAudit('post', {{ $post->id }})" class="text-xs text-indigo-650 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2 cursor-pointer font-semibold">Audit</button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">No posts found.</div>
                @endforelse
            </div>
        </div>

        {{-- Page SEO Status --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30">
                <h4 class="font-bold text-gray-800 dark:text-slate-250 text-base">Static Pages SEO Status</h4>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($pages as $page)
                    @php
                        $hasTitle = !empty($page->meta_title);
                        $hasDesc = !empty($page->meta_description);
                    @endphp
                    <div class="p-4 flex items-center justify-between gap-4 text-sm">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-gray-900 dark:text-white truncate block">{{ $page->title }}</span>
                            <span class="text-xs text-gray-400 block truncate font-mono">/{{ $page->slug }}</span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasTitle ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasTitle ? 'Title ✓' : 'Title ✗' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $hasDesc ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $hasDesc ? 'Desc ✓' : 'Desc ✗' }}
                            </span>
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-xs text-indigo-650 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2 font-semibold">Edit</a>
                            <button @click="fetchAudit('page', {{ $page->id }})" class="text-xs text-indigo-650 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2 cursor-pointer font-semibold">Audit</button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">No static pages found.</div>
                @endforelse
            </div>
     {{-- SEO Detail Audit Modal --}}
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         style="display: none;">
         
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative w-full max-w-4xl bg-white dark:bg-slate-800 rounded-2xl border border-gray-150 dark:border-slate-700 shadow-2xl overflow-hidden flex flex-col my-8 text-left"
                 @click.away="modalOpen = false">
                 
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 rounded" x-text="auditData ? auditData.type + ' Audit' : 'SEO Audit'"></span>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg mt-1" x-text="auditData ? auditData.title : 'Loading details...'"></h3>
                    </div>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 overflow-y-auto flex-1">
                    {{-- Loading Spinner --}}
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                        <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="mt-4 text-sm text-gray-500 font-semibold">Running page diagnostics...</span>
                    </div>

                    {{-- Loaded Content --}}
                    <div x-show="!loading && auditData" class="space-y-6" x-cloak>
                        {{-- Score Summary and Health status --}}
                        <div class="flex flex-col sm:flex-row items-center gap-6 p-5 bg-gray-50 dark:bg-slate-750/30 rounded-xl border border-gray-150 dark:border-slate-700">
                            <div class="relative w-24 h-24 flex items-center justify-center flex-shrink-0">
                                {{-- Circular Progress Bar inside modal --}}
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" stroke="#f3f4f6" stroke-width="8" fill="transparent" class="dark:stroke-slate-700" />
                                    <circle cx="50" cy="50" r="40" stroke="#4f46e5" stroke-width="8" fill="transparent"
                                            :stroke-dasharray="251.2" :stroke-dashoffset="auditData ? 251.2 - (251.2 * (auditData.overallScore / 100)) : 251.2" stroke-linecap="round" />
                                </svg>
                                <div class="absolute flex flex-col items-center">
                                    <span class="text-xl font-black text-gray-900 dark:text-white" x-text="auditData ? auditData.overallScore : 0"></span>
                                    <span class="text-[9px] text-gray-400 font-semibold uppercase">score</span>
                                </div>
                            </div>
                            <div class="text-center sm:text-left">
                                <h4 class="font-bold text-gray-800 dark:text-slate-200 text-base flex flex-col sm:flex-row items-center gap-2">
                                    <span>SEO Health Score:</span>
                                    <span :class="auditData ? auditData.healthClass : ''" class="px-2.5 py-0.5 border rounded-full text-xs font-bold uppercase" x-text="auditData ? auditData.healthStatus : ''"></span>
                                </h4>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    This health score shows search readiness for this individual page. Check the detailed audit rules below to improve your content and score.
                                </p>
                                <div class="mt-2 text-xs font-mono text-gray-400" x-text="'Word Count: ' + (auditData ? auditData.wordCount : 0) + ' words'"></div>
                            </div>
                        </div>

                        {{-- Core Audits Details --}}
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-800 dark:text-slate-200 text-sm border-b border-gray-100 dark:border-slate-700 pb-2">Individual Audit Checkpoints</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="check in (auditData ? auditData.checks : [])" :key="check.id">
                                    <div class="flex items-start gap-3 p-4 bg-white dark:bg-slate-750/10 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm">
                                        <span :class="check.passed ? 'text-green-500' : 'text-red-500'" class="text-xl font-bold flex-shrink-0 leading-none mt-0.5" x-text="check.passed ? '✓' : '✗'"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-bold text-sm text-gray-900 dark:text-slate-100" x-text="check.name"></span>
                                                <span :class="check.passed ? 'bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-900' : 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900'" 
                                                      class="text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="check.score + '%'"></span>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-slate-400 block mt-1 leading-relaxed" x-text="check.description"></span>
                                            
                                            {{-- Diagnostic Feedback --}}
                                            <div class="mt-2.5 p-2 bg-gray-50 dark:bg-slate-800/40 rounded border border-gray-100 dark:border-slate-700/50">
                                                <span class="text-[11px] font-semibold text-gray-700 dark:text-slate-300 block">Diagnostic Feedback:</span>
                                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-0.5 leading-relaxed" x-text="check.feedback"></p>
                                            </div>
                                            
                                            {{-- Current Value --}}
                                            <div class="mt-2" x-show="check.detail">
                                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Found Value:</span>
                                                <code class="text-[11px] font-mono text-indigo-600 dark:text-indigo-400 break-all block mt-0.5" x-text="check.detail"></code>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30 flex justify-end">
                    <button @click="modalOpen = false" class="px-4 py-2 bg-indigo-650 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors cursor-pointer">
                        Close Audit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
