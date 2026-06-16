@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?? $page->title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<!-- Page Header Banner -->
<div class="relative text-white py-16 px-4 sm:px-6 lg:px-8 text-center overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b, #0f172a, #0f172a);">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="relative max-w-4xl mx-auto space-y-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
            {{ $page->title }}
        </h1>
        <div class="w-20 h-1.5 mx-auto rounded-full" style="background: linear-gradient(to right, #38bdf8, #6366f1);"></div>
    </div>
</div>

<!-- Main Content Area -->
<div class="relative min-h-screen bg-gray-50 dark:bg-slate-950 py-16 overflow-hidden">
    <!-- Subtle Background Glows -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-50/50 dark:bg-indigo-950/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-sky-50/50 dark:bg-sky-950/10 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 sm:p-12 shadow-sm hover:shadow-md transition-shadow duration-300">
            {{-- Featured Image --}}
            @if($page->featured_image)
                <div class="mb-8 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-slate-800">
                    <img src="{{ asset('storage/' . $page->featured_image) }}" class="w-full h-[380px] object-cover" alt="{{ $page->title }}">
                </div>
            @endif

            {{-- Render Dynamic HTML Content directly from Database with relative paths sanitized --}}
            <div class="prose prose-indigo prose-lg max-w-none dark:prose-invert text-gray-650 dark:text-slate-350 leading-relaxed 
                        prose-headings:text-indigo-950 dark:prose-headings:text-white prose-headings:font-extrabold
                        prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4 prose-h2:pb-3 prose-h2:border-b prose-h2:border-gray-150 dark:prose-h2:border-slate-800
                        prose-p:mb-6 prose-li:mb-2 prose-ul:list-disc prose-ul:pl-6">
                {!! preg_replace('/(\.\.\/)+storage\//', '/storage/', $page->content) !!}
            </div>
        </div>
    </div>
</div>
@endsection
