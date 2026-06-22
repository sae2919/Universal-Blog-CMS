@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?? $page->title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<!-- Page Header Banner -->
<div class="relative text-white py-20 px-4 sm:px-6 lg:px-8 text-center overflow-hidden bg-cover bg-center" 
     style="min-height: 380px; display: flex; align-items: center; justify-content: center; {{ $page->featured_image ? 'background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url(\'' . asset('storage/' . $page->featured_image) . '\');' : 'background: linear-gradient(135deg, #1e1b4b, #0f172a, #0f172a);' }}">
    @if(!$page->featured_image)
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px]"></div>
    @endif
    <div class="relative max-w-4xl mx-auto space-y-4 w-full">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight">
            {{ $page->title }}
        </h1>
        <div class="w-20 h-1.5 mx-auto rounded-full" style="background: linear-gradient(to right, #38bdf8, #6366f1);"></div>
        @if($page->meta_description)
            <p class="max-w-2xl mx-auto text-indigo-200 text-lg font-medium">
                {{ $page->meta_description }}
            </p>
        @endif
    </div>
</div>

<!-- Main Content Area -->
<div class="relative min-h-screen bg-white dark:bg-slate-950 py-16 overflow-hidden">
    <!-- Subtle Background Glows -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-50/50 dark:bg-indigo-950/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-sky-50/50 dark:bg-sky-950/10 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Render Dynamic HTML Content directly from Database with relative paths sanitized --}}
        <div class="prose prose-indigo prose-lg max-w-none dark:prose-invert text-gray-650 dark:text-slate-350 leading-relaxed 
                    prose-headings:text-indigo-950 dark:prose-headings:text-white prose-headings:font-extrabold
                    prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                    prose-p:mb-6 prose-li:mb-2 prose-ul:list-disc prose-ul:pl-6">
            {!! preg_replace('/(\.\.\/)+storage\//', '/storage/', $page->content) !!}
        </div>
    </div>
</div>
@endsection
