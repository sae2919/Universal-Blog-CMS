@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?? $page->title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <header class="text-center space-y-4 mb-10">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
            {{ $page->title }}
        </h1>
        <div class="w-16 h-1 bg-indigo-650 mx-auto rounded-full"></div>
    </header>

    {{-- Featured Image --}}
    @if($page->featured_image)
        <div class="mb-12 rounded-2xl overflow-hidden shadow-lg border border-gray-100">
            <img src="{{ asset('storage/' . $page->featured_image) }}" class="w-full h-[380px] object-cover" alt="{{ $page->title }}">
        </div>
    @endif

    {{-- Main Rich Content --}}
    <div class="prose prose-indigo prose-lg max-w-none text-gray-700 leading-relaxed">
        {!! $page->content !!}
    </div>
</article>
@endsection
