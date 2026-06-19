@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?? $page->title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<!-- Page Header -->
<div class="relative text-white py-16 px-4 sm:px-6 lg:px-8 text-center overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b, #0f172a, #0f172a);">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="relative max-w-4xl mx-auto space-y-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
            {{ $page->title }}
        </h1>
        <div class="w-20 h-1.5 mx-auto rounded-full" style="background: linear-gradient(to right, #38bdf8, #6366f1);"></div>
        <p class="max-w-2xl mx-auto text-indigo-200 text-lg font-medium">
            {{ __('Have questions or need assistance? We are here to support you 24/7 on your learning journey.') }}
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    @if(session('success'))
        <div class="mb-10 p-5 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-300 rounded-2xl flex items-start gap-4 shadow-sm animate-fade-in-down">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-bold text-sm leading-none">{{ __('Message Sent Successfully!') }}</p>
                <p class="text-xs mt-1">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($page->content)
        <div class="prose prose-indigo prose-lg max-w-none dark:prose-invert text-gray-650 dark:text-slate-350 leading-relaxed mb-12">
            {!! $page->content !!}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left Side: Contact Information Cards -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Address Card -->
            <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-6 group">
                <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-950/30 rounded-2xl flex items-center justify-center flex-shrink-0 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="font-extrabold text-gray-900 dark:text-white text-lg">{{ __('Office Address') }}</h3>
                    <p class="text-sm text-gray-650 dark:text-slate-300 leading-relaxed font-semibold">
                        {{ \App\Models\Setting::getValue('office_address', '501, Manjeera Majestic Commercial, KPHB, Hyderabad, India - 500072') }}
                    </p>
                </div>
            </div>

            <!-- Informational Grid (Phone & Email side-by-side) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                
                <!-- Phone Card -->
                <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 bg-sky-50 dark:bg-sky-950/30 rounded-xl flex items-center justify-center flex-shrink-0 text-sky-600 dark:text-sky-400 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-extrabold text-gray-900 dark:text-white text-md">{{ __('Contact Number') }}</h3>
                            <a href="tel:{{ \App\Models\Setting::getValue('contact_phone', '+917680097094') }}" class="block font-bold text-indigo-600 dark:text-indigo-400 hover:underline text-lg">
                                {{ \App\Models\Setting::getValue('contact_phone', '+917680097094') }}
                            </a>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-4 leading-normal font-medium">
                        {{ __('Assistance hours: Monday - Sunday By 24/7 Hours') }}
                    </p>
                </div>

                <!-- Email Card -->
                <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 bg-purple-50 dark:bg-purple-950/30 rounded-xl flex items-center justify-center flex-shrink-0 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-extrabold text-gray-900 dark:text-white text-md">{{ __('Email Address') }}</h3>
                            <a href="mailto:{{ \App\Models\Setting::getValue('contact_email', 'support@findmyguru.com') }}" class="block font-bold text-indigo-600 dark:text-indigo-400 hover:underline text-[16px] break-all">
                                {{ \App\Models\Setting::getValue('contact_email', 'support@findmyguru.com') }}
                            </a>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-4 leading-normal font-medium">
                        {{ __('Assistance hours: Monday - Sunday By 24/7 Hours') }}
                    </p>
                </div>

            </div>

            <!-- Social Media Profile Container -->
            @php
                $socials = [
                    'facebook'  => ['name' => 'Facebook',  'icon' => 'facebook',  'url' => \App\Models\Setting::getValue('facebook')],
                    'instagram' => ['name' => 'Instagram', 'icon' => 'instagram', 'url' => \App\Models\Setting::getValue('instagram')],
                    'linkedin'  => ['name' => 'LinkedIn',  'icon' => 'linkedin',  'url' => \App\Models\Setting::getValue('linkedin')],
                    'twitter'   => ['name' => 'Twitter/X', 'icon' => 'twitter',   'url' => \App\Models\Setting::getValue('twitter')],
                    'youtube'   => ['name' => 'YouTube',   'icon' => 'youtube',   'url' => \App\Models\Setting::getValue('youtube')],
                ];
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
                <h3 class="font-extrabold text-gray-900 dark:text-white text-md mb-5">{{ __('Connect with us') }}</h3>
                <div class="flex flex-wrap gap-4">
                    @foreach($socials as $key => $social)
                        @if($social['url'])
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="w-12 h-12 bg-gray-50 dark:bg-slate-850 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 text-gray-500 dark:text-slate-400 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105"
                               title="{{ $social['name'] }}">
                                @if($key === 'facebook')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/>
                                    </svg>
                                @elseif($key === 'instagram')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                @elseif($key === 'linkedin')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                @elseif($key === 'twitter')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                @elseif($key === 'youtube')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.108C19.528 3.5 12 3.5 12 3.5s-7.528 0-9.388.555A3.002 3.002 0 00.502 6.163C0 8.07 0 12 0 12s0 3.93.502 5.837a3.003 3.003 0 002.11 2.108c1.86.555 9.388.555 9.388.555s7.528 0 9.388-.555a3.002 3.002 0 002.11-2.108C24 15.93 24 12 24 12s0-3.93-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Side: Contact Form Card -->
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 lg:p-10 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 dark:bg-indigo-950/10 rounded-bl-full -z-10"></div>
                
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-2">
                    {{ __('Send Us a Message') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-8">
                    {{ __('Fill out the form below and our team will get in touch with you shortly.') }}
                </p>

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name Input -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">{{ __('Your Name') }}</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                   placeholder="John Doe"
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 focus:ring-red-500 @enderror">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">{{ __('Email Address') }}</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   placeholder="johndoe@example.com"
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-300 focus:ring-red-500 @enderror">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Subject Input -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">{{ __('Subject') }}</label>
                        <input type="text" name="subject" id="subject" required value="{{ old('subject') }}"
                               placeholder="How can we help you?"
                               class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('subject') border-red-300 focus:ring-red-500 @enderror">
                        @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Message Input -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">{{ __('Message') }}</label>
                        <textarea name="message" id="message" rows="5" required
                                  placeholder="Write your message here..."
                                  class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('message') border-red-300 focus:ring-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            {{ __('Send Message') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
