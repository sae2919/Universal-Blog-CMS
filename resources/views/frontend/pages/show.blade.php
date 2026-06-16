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
        <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
    </header>

    {{-- Featured Image --}}
    @if($page->featured_image)
        <div class="mb-12 rounded-2xl overflow-hidden shadow-lg border border-gray-100">
            <img src="{{ asset('storage/' . $page->featured_image) }}" class="w-full h-[380px] object-cover" alt="{{ $page->title }}">
        </div>
    @endif

    {{-- Main Rich Content --}}
    <div class="prose prose-indigo prose-lg max-w-none text-gray-700 leading-relaxed">
        {!! preg_replace('/(\.\.\/)+storage\//', '/storage/', $page->content) !!}
    </div>
</article>
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
