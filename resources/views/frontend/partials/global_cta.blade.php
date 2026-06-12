@php
    $globalCtaTitle = \App\Models\Setting::getValue('global_cta_title');
@endphp

@if(!empty($globalCtaTitle))
    @php
        $globalCtaDesc = \App\Models\Setting::getValue('global_cta_description');
        $globalCtaBtnText = \App\Models\Setting::getValue('global_cta_button_text');
        $globalCtaBtnLink = \App\Models\Setting::getValue('global_cta_button_link');
        $globalCtaBg = \App\Models\Setting::getValue('global_cta_bg_image');
        $bgImage = $globalCtaBg 
            ? asset('storage/' . $globalCtaBg) 
            : 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1600';
    @endphp
    <section class="w-full">
        <div class="relative bg-slate-900 py-16 sm:py-20 px-6 text-center overflow-hidden" 
             style="background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('{{ $bgImage }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 max-w-4xl mx-auto space-y-6">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    {{ $globalCtaTitle }}
                </h2>
                @if($globalCtaDesc)
                    <p class="text-gray-200 text-sm sm:text-base max-w-2xl mx-auto font-medium leading-relaxed">
                        {{ $globalCtaDesc }}
                    </p>
                @endif
                @if($globalCtaBtnText && $globalCtaBtnLink)
                    <div class="pt-4">
                        <a href="{{ $globalCtaBtnLink }}" class="inline-block bg-[#0070ba] hover:bg-[#005c9c] text-white font-bold text-sm sm:text-base px-8 py-3 rounded-full shadow-md transition-all duration-200 hover:-translate-y-0.5">
                            {{ $globalCtaBtnText }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
