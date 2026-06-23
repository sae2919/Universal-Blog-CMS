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
    {{-- Canonical URL: include page param for paginated URLs to avoid duplicate content issues --}}
    @php
        $_canonicalUrl = request()->url();
        if (request()->has('page') && request()->query('page') > 1) {
            $_canonicalUrl = request()->fullUrlWithQuery(['page' => request()->query('page')]);
        }
    @endphp
    <link rel="canonical" href="{{ $_canonicalUrl }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    <meta property="og:title" content="@yield('meta_title', \App\Models\Setting::getValue('site_name'))">
    <meta property="og:description" content="@yield('meta_description', '')">
    @php
        $ogImageFallback = \App\Models\Setting::getValue('default_og_image')
            ? asset('storage/' . \App\Models\Setting::getValue('default_og_image'))
            : asset('favicon.ico');
    @endphp
    <meta property="og:image" content="@yield('og_image', $ogImageFallback)">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="{{ \App\Models\Setting::getValue('site_name') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', '')">
    <meta name="twitter:description" content="@yield('meta_description', '')">
    <meta name="twitter:image" content="@yield('og_image', $ogImageFallback)">

    {{-- Schema JSON-LD --}}
    @stack('schema')

    {{-- Favicon --}}
    @if(\App\Models\Setting::getValue('site_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::getValue('site_favicon')) }}">
    @endif

    {{-- Fonts — preconnect to both Google APIs and font file CDN (prevents extra DNS round-trip) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')


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
<body class="bg-gray-50 text-gray-900 antialiased" style="font-family: 'DM Sans', sans-serif;">

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
