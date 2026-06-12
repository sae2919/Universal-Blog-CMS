<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>@yield('meta_title', \App\Models\Setting::getValue('default_meta_title', config('app.name')))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::getValue('default_meta_description', ''))">
    <meta name="keywords" content="@yield('meta_keywords', '')">
    <link rel="canonical" href="{{ request()->url() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('meta_title', \App\Models\Setting::getValue('site_name'))">
    <meta property="og:description" content="@yield('meta_description', '')">
    <meta property="og:image" content="@yield('og_image', asset(\App\Models\Setting::getValue('default_og_image', '')))">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="{{ \App\Models\Setting::getValue('site_name') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', '')">
    <meta name="twitter:description" content="@yield('meta_description', '')">
    <meta name="twitter:image" content="@yield('og_image', '')">

    {{-- Schema JSON-LD --}}
    @stack('schema')

    {{-- Favicon --}}
    @if(\App\Models\Setting::getValue('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::getValue('site_favicon')) }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode(\App\Models\Setting::getValue('site_font', 'Inter')) }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    {{-- Dark Mode Init Script --}}
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    {{-- Google Analytics --}}
    @if(\App\Models\Setting::getValue('google_analytics'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::getValue('google_analytics') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ \App\Models\Setting::getValue('google_analytics') }}');
    </script>
    @endif
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-slate-900 dark:text-slate-100 antialiased" style="font-family: '{{ \App\Models\Setting::getValue('site_font', 'Inter') }}', sans-serif;">

    {{-- Header --}}
    @include('frontend.partials.header')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Call To Action & Directory Links --}}
    @include('frontend.partials.global_cta')

    {{-- Footer --}}
    @include('frontend.partials.footer')

    @stack('scripts')
</body>
</html>
