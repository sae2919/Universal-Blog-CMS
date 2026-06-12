@extends('layouts.frontend')

@section('meta_title', $post->meta_title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $post->meta_description ?? $post->excerpt)
@section('meta_keywords', $post->meta_keywords)
@section('og_image', $post->og_image ? asset('storage/' . $post->og_image) : '')
@section('og_type', 'article')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" />
<style>
    /* Custom premium slider styling overrides */
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

    /* Premium Custom Scrollbar */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5); /* gray-400 with opacity */
        border-radius: 20px;
    }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: rgba(71, 85, 105, 0.5); /* slate-600 with opacity */
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.8);
    }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: rgba(71, 85, 105, 0.8);
    }
    /* Firefox support */
    .scrollbar-thin {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }
    .dark .scrollbar-thin {
        scrollbar-color: rgba(71, 85, 105, 0.5) transparent;
    }

</style>
@endpush

@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "{{ $post->schema_type ?? 'BlogPosting' }}",
  "headline": "{{ $post->title }}",
  "image": "{{ $post->featured_image ? asset('storage/' . $post->featured_image) : '' }}",
  "author": { "@type": "Person", "name": "{{ $post->author->name }}" },
  "publisher": {
    "@type": "Organization",
    "name": "{{ \App\Models\Setting::getValue('site_name') }}"
  },
  "datePublished": "{{ $post->published_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "description": "{{ $post->excerpt }}"
}
</script>
@endpush

@section('content')
{{-- Scroll Reading Progress Bar --}}
<div id="scrollProgressBar" class="fixed top-0 left-0 h-1 bg-indigo-600 transition-all duration-75 z-[9999]" style="width: 0%;"></div>

<article class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

        {{-- Article Body --}}
        <div class="md:col-span-2">

                    {{-- Breadcrumb --}}
                    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                        <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
                        <span>/</span>
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:opacity-85" style="color: {{ $post->category->accent_color }};">
                            {{ $post->category->icon_emoji }} {{ $post->category->name }}
                        </a>
                        <span>/</span>
                        <span class="text-gray-600 truncate">{{ $post->title }}</span>
                    </nav>

                    {{-- Category & Meta --}}
                    <div class="flex items-center gap-3 mb-4">
                        <a href="{{ route('blog.category', $post->category->slug) }}"
                           class="text-xs font-bold px-3 py-1 rounded-full border hover:opacity-80 transition-all"
                           style="background-color: {{ $post->category->accent_color }}33; color: {{ $post->category->accent_color }}; border-color: {{ $post->category->accent_color }}55;">
                            {{ $post->category->icon_emoji }} {{ $post->category->name }}
                        </a>
                        @if($post->is_featured)
                            <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full">⭐ Featured</span>
                        @endif
                        @if($post->is_trending)
                            <span class="bg-red-100 text-red-600 text-xs font-semibold px-3 py-1 rounded-full">🔥 Trending</span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight mb-4">
                        {{ $post->title }}
                    </h1>

                    {{-- Author & Date --}}
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6 pb-6 border-b border-gray-100">
                        <span>{{ $post->published_at->format('M d, Y') }}</span>
                        <span>·</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ number_format($post->views) }} views
                        </span>
                        <span>·</span>
                        <span class="flex items-center gap-1" title="Estimated reading time">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $post->read_time }} min read
                        </span>
                    </div>

                    {{-- Featured Image --}}
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                             class="w-full rounded-2xl mb-8 object-cover max-h-96">
                    @endif

                    {{-- Content --}}
                    <div class="prose prose-lg prose-indigo max-w-none text-gray-700 leading-relaxed">
                        {!! $post->content !!}
                    </div>

                    {{-- Tags --}}
                    @if($post->tags->count())
                        <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-2">
                            <span class="text-sm font-medium text-gray-500 mr-2">Tags:</span>
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag->slug) }}"
                                   class="bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 text-xs font-medium px-3 py-1.5 rounded-full transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif



                    {{-- Comments --}}
                    @if($post->allow_comments)
                        <div class="mt-12 pt-8 border-t border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">
                                Comments ({{ $post->approvedComments->count() }})
                            </h3>

                            {{-- Comments List --}}
                            @foreach($post->approvedComments as $comment)
                                <div class="flex gap-4 mb-6">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($comment->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 bg-gray-50 rounded-xl p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-semibold text-sm text-gray-800">{{ $comment->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Comment Form --}}
                            <div class="mt-8 bg-gray-50 rounded-2xl p-6">
                                <h4 class="font-bold text-gray-800 mb-4">Leave a Comment</h4>

                                @if(session('success'))
                                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('blog.comment.store', [$post->category->slug, $post->slug]) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                            <input type="text" name="name" value="{{ old('name') }}" required
                                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                            <input type="email" name="email" value="{{ old('email') }}" required
                                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Comment *</label>
                                        <textarea name="comment" rows="4" required
                                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('comment') }}</textarea>
                                        @error('comment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit"
                                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors">
                                        Post Comment
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- FAQ Section --}}
                    @if(!empty($post->faqs) && count($post->faqs) > 0)
                        <div class="mt-12 pt-8 border-t border-gray-150 dark:border-slate-800">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                                <svg class="w-7 h-7 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Frequently Asked Questions
                            </h3>
                            
                            <div x-data="{ activeIndex: null }" class="space-y-4">
                                @foreach($post->faqs as $index => $faq)
                                    <div class="border border-gray-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900 transition-all duration-200"
                                         :class="activeIndex === {{ $index }} ? 'shadow-md border-indigo-150 dark:border-indigo-950/50' : ''">
                                        <button type="button" 
                                                @click="activeIndex = activeIndex === {{ $index }} ? null : {{ $index }}"
                                                class="w-full flex justify-between items-center px-5 py-4 text-left font-semibold text-gray-805 dark:text-slate-205 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors focus:outline-none">
                                            <span>{{ $faq['question'] }}</span>
                                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                 :class="activeIndex === {{ $index }} ? 'transform rotate-180 text-indigo-500' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="activeIndex === {{ $index }}"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                                             x-transition:enter-end="opacity-100 transform translate-y-0"
                                             class="px-5 pb-4 text-sm text-gray-600 dark:text-slate-300 border-t border-gray-100 dark:border-slate-800/50 pt-3 leading-relaxed prose prose-sm dark:prose-invert">
                                            {!! $faq['answer'] !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>


        {{-- Sidebar --}}
        <aside class="relative">
            <div class="md:sticky md:top-24 space-y-8">
                @include('frontend.blog.partials.sidebar_widgets')
            </div>
        </aside>

    </div>
</article>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.getElementById('scrollProgressBar');
        if (progressBar) {
            window.addEventListener('scroll', function() {
                const scrollTop = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                if (docHeight > 0) {
                    const scrollPercent = (scrollTop / docHeight) * 100;
                    progressBar.style.width = scrollPercent + '%';
                } else {
                    progressBar.style.width = '0%';
                }
            });
        }

        // Parse and transform sliders
        document.querySelectorAll('.post-slider').forEach((slider, index) => {
            const images = Array.from(slider.querySelectorAll('img'));
            if (images.length === 0) return;

            // Create structured markup for Splide
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

            // Replace the original div with our Splide instance
            slider.parentNode.replaceChild(splideDiv, slider);

            // Mount Splide
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
