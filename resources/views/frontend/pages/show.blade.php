@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?? $page->title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<!-- Page Header Banner -->
<div class="relative py-16 px-4 sm:px-6 lg:px-8 text-center bg-gray-50 dark:bg-slate-900 border-b border-gray-150 dark:border-slate-800/80">
    <div class="max-w-4xl mx-auto space-y-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
            {{ $page->title }}
        </h1>
        <div class="w-16 h-1.5 mx-auto rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
    </div>
</div>

<!-- Main Content Area -->
<div class="min-h-screen bg-white dark:bg-slate-950 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Featured Image --}}
        @if($page->featured_image)
            <div class="mb-12 rounded-3xl overflow-hidden shadow-md border border-gray-150 dark:border-slate-800">
                <img src="{{ asset('storage/' . $page->featured_image) }}" class="w-full h-auto max-h-[500px] object-cover" alt="{{ $page->title }}">
            </div>
        @endif

        {{-- Main Rich Content --}}
        <div class="prose prose-indigo prose-lg max-w-none dark:prose-invert text-gray-650 dark:text-slate-350 leading-relaxed 
                    prose-headings:text-indigo-950 dark:prose-headings:text-white prose-headings:font-extrabold
                    prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4 prose-h2:pb-3 prose-h2:border-b prose-h2:border-gray-150 dark:prose-h2:border-slate-800
                    prose-p:mb-6 prose-li:mb-2 prose-ul:list-disc prose-ul:pl-6">
            {!! preg_replace('/(\.\.\/)+storage\//', '/storage/', $page->content) !!}
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" />
<style>
    .splide__arrow {
        background: rgba(255, 255, 255, 0.9) !important;
        color: #1e293b !important;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important;
        width: 2.5rem !important;
        height: 2.5rem !important;
    }
    .dark .splide__arrow {
        background: rgba(15, 23, 42, 0.9) !important;
        color: #f1f5f9 !important;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.3) !important;
    }
    .splide__pagination__page.is-active {
        background: #4f46e5 !important;
        transform: scale(1.4) !important;
    }
    .splide__pagination__page {
        background: #cbd5e1 !important;
        opacity: 0.8 !important;
    }
    .dark .splide__pagination__page {
        background: #475569 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Parse and transform sliders
        document.querySelectorAll('.post-slider').forEach((slider, index) => {
            const images = Array.from(slider.querySelectorAll('img'));
            if (images.length === 0) return;

            const splideId = 'splide-slider-' + index;
            const splideDiv = document.createElement('div');
            splideDiv.id = splideId;
            splideDiv.className = 'splide my-8 rounded-2xl overflow-hidden shadow-lg border border-gray-150 dark:border-slate-800';

            const track = document.createElement('div');
            track.className = 'splide__track';

            const list = document.createElement('ul');
            list.className = 'splide__list';

            images.forEach(img => {
                const slide = document.createElement('li');
                slide.className = 'splide__slide flex justify-center items-center bg-black';
                const slideImg = document.createElement('img');
                slideImg.src = img.src;
                slideImg.alt = img.alt || 'Slide';
                slideImg.className = 'w-full max-h-[480px] object-cover';
                slide.appendChild(slideImg);
                list.appendChild(slide);
            });

            track.appendChild(list);
            splideDiv.appendChild(track);

            slider.parentNode.replaceChild(splideDiv, slider);

            new Splide('#' + splideId, {
                type: 'loop',
                perPage: 1,
                autoplay: true,
                interval: 4000,
                speed: 800,
                arrows: images.length > 1,
                pagination: images.length > 1,
            }).mount();
        });
    });
</script>
@endpush
