@php
    $hasCustomCta = false;
    $cta = null;

    if (isset($post) && !empty($post->cta_title)) {
        $hasCustomCta = true;
        $cta = $post;
    }

    // Parse column links helper function
    $parseLinks = function($linksText) {
        if (empty($linksText)) {
            return [];
        }
        $parsed = [];
        $lines = explode("\n", str_replace("\r", "", $linksText));
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('|', $line, 2);
            if (count($parts) === 2) {
                $parsed[] = [
                    'text' => trim($parts[0]),
                    'link' => trim($parts[1])
                ];
            } else {
                // If no pipe is provided, use the text as link target too or default to '#'
                $parsed[] = [
                    'text' => $line,
                    'link' => '#'
                ];
            }
        }
        return $parsed;
    };
@endphp

<style>
    /* Prevent Tailwind Typography (.prose) from overriding CTA Banner and CTA Directory styles */
    section.not-prose h2,
    .prose section.not-prose h2 {
        color: #ffffff !important;
        border-left: none !important;
        padding-left: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        font-weight: 800 !important;
    }
    section.not-prose p,
    .prose section.not-prose p {
        color: #e2e8f0 !important; /* text-gray-200 */
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    section.not-prose a,
    .prose section.not-prose a {
        text-decoration: none !important;
    }
    section.not-prose a.inline-block,
    .prose section.not-prose a.inline-block {
        color: #ffffff !important;
    }
    section.not-prose h3,
    .prose section.not-prose h3 {
        color: #005691 !important;
        margin-top: 0 !important;
        margin-bottom: 0.5rem !important;
        font-weight: 800 !important;
    }
    .dark section.not-prose h3,
    .dark .prose section.not-prose h3 {
        color: #38bdf8 !important;
    }
    section.not-prose h4,
    .prose section.not-prose h4 {
        color: #111827 !important; /* text-gray-900 */
        margin-top: 0 !important;
        margin-bottom: 1.25rem !important;
        border-bottom: 1px solid #f3f4f6 !important;
        font-weight: 700 !important;
    }
    .dark section.not-prose h4,
    .dark .prose section.not-prose h4 {
        color: #ffffff !important;
        border-bottom-color: #1e293b !important;
    }
    section.not-prose ul,
    .prose section.not-prose ul {
        list-style-type: none !important;
        padding-left: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    section.not-prose li,
    .prose section.not-prose li {
        list-style-type: none !important;
        margin-top: 0 !important;
        margin-bottom: 0.875rem !important;
        padding-left: 0 !important;
    }
    section.not-prose li a,
    .prose section.not-prose li a {
        color: #0369a1 !important; /* text-sky-700 */
        text-decoration: none !important;
    }
    section.not-prose li a:hover,
    .prose section.not-prose li a:hover {
        color: #0c4a6e !important; /* text-sky-900 */
        text-decoration: underline !important;
    }
    .dark section.not-prose li a,
    .dark .prose section.not-prose li a {
        color: #38bdf8 !important; /* text-sky-400 */
    }
    .dark section.not-prose li a:hover,
    .dark .prose section.not-prose li a:hover {
        color: #7dd3fc !important; /* text-sky-300 */
    }
</style>

@if($hasCustomCta && $cta)
    <section class="w-full not-prose">
        {{-- Custom CTA Banner --}}
        @php
            $bgImage = $cta->cta_bg_image 
                ? asset('storage/' . $cta->cta_bg_image) 
                : 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1600';
        @endphp
        <div class="relative bg-slate-900 py-16 sm:py-20 px-6 text-center overflow-hidden" 
             style="background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('{{ $bgImage }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 max-w-4xl mx-auto space-y-6">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    {{ $cta->cta_title }}
                </h2>
                @if($cta->cta_description)
                    <p class="text-gray-200 text-sm sm:text-base max-w-2xl mx-auto font-medium leading-relaxed">
                        {{ $cta->cta_description }}
                    </p>
                @endif
                @if($cta->cta_button_text && $cta->cta_button_link)
                    <div class="pt-4">
                        <a href="{{ $cta->cta_button_link }}" class="inline-block bg-[#0070ba] hover:bg-[#005c9c] !text-white !no-underline font-bold text-sm sm:text-base px-8 py-3 rounded-full shadow-md transition-all duration-200 hover:-translate-y-0.5">
                            {{ $cta->cta_button_text }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Custom Links Directory (Only if directory title is provided) --}}
        @if($cta->cta_directory_title)
            <div class="bg-white dark:bg-slate-950 py-16 border-b border-gray-100 dark:border-slate-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-12">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-[#005691] dark:text-[#38bdf8] tracking-tight mb-2">
                        {{ $cta->cta_directory_title }}
                    </h3>
                    @if($cta->cta_directory_subtitle)
                        <p class="text-gray-500 dark:text-slate-400 text-xs sm:text-sm font-semibold tracking-wide">
                            {{ $cta->cta_directory_subtitle }}
                        </p>
                    @endif
                </div>

                {{-- 3 Columns Grid --}}
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        {{-- Column 1 Links --}}
                        @if($cta->cta_col1_title)
                            <div class="bg-gray-50/50 dark:bg-slate-900/40 rounded-2xl border border-gray-100 dark:border-slate-900 p-6 sm:p-8 shadow-sm">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-slate-800 pb-3 mb-5">
                                    {{ $cta->cta_col1_title }}
                                </h4>
                                @php $col1Links = $parseLinks($cta->cta_col1_links); @endphp
                                @if(count($col1Links) > 0)
                                    <ul class="space-y-3.5">
                                        @foreach($col1Links as $linkItem)
                                            <li class="flex items-start gap-2.5">
                                                <span class="w-2 h-2 rounded-full bg-sky-600 mt-2 flex-shrink-0"></span>
                                                <a href="{{ $linkItem['link'] }}" class="text-sky-700 hover:text-sky-900 dark:text-sky-400 dark:hover:text-sky-300 font-medium hover:underline text-sm leading-snug">
                                                    {{ $linkItem['text'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-400">No links added.</p>
                                @endif
                            </div>
                        @endif

                        {{-- Column 2 Links --}}
                        @if($cta->cta_col2_title)
                            <div class="bg-gray-50/50 dark:bg-slate-900/40 rounded-2xl border border-gray-100 dark:border-slate-900 p-6 sm:p-8 shadow-sm">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-slate-800 pb-3 mb-5">
                                    {{ $cta->cta_col2_title }}
                                </h4>
                                @php $col2Links = $parseLinks($cta->cta_col2_links); @endphp
                                @if(count($col2Links) > 0)
                                    <ul class="space-y-3.5">
                                        @foreach($col2Links as $linkItem)
                                            <li class="flex items-start gap-2.5">
                                                <span class="w-2 h-2 rounded-full bg-sky-600 mt-2 flex-shrink-0"></span>
                                                <a href="{{ $linkItem['link'] }}" class="text-sky-700 hover:text-sky-900 dark:text-sky-400 dark:hover:text-sky-300 font-medium hover:underline text-sm leading-snug">
                                                    {{ $linkItem['text'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-400">No links added.</p>
                                @endif
                            </div>
                        @endif

                        {{-- Column 3 Links --}}
                        @if($cta->cta_col3_title)
                            <div class="bg-gray-50/50 dark:bg-slate-900/40 rounded-2xl border border-gray-100 dark:border-slate-900 p-6 sm:p-8 shadow-sm">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-slate-800 pb-3 mb-5">
                                    {{ $cta->cta_col3_title }}
                                </h4>
                                @php $col3Links = $parseLinks($cta->cta_col3_links); @endphp
                                @if(count($col3Links) > 0)
                                    <ul class="space-y-3.5">
                                        @foreach($col3Links as $linkItem)
                                            <li class="flex items-start gap-2.5">
                                                <span class="w-2 h-2 rounded-full bg-sky-600 mt-2 flex-shrink-0"></span>
                                                <a href="{{ $linkItem['link'] }}" class="text-sky-700 hover:text-sky-900 dark:text-sky-400 dark:hover:text-sky-300 font-medium hover:underline text-sm leading-snug">
                                                    {{ $linkItem['text'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-400">No links added.</p>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif
    </section>
@endif
