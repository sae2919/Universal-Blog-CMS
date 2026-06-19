<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.4);
            }
            .dark .glass-card {
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            .glow-orb {
                filter: blur(140px);
                opacity: 0.45;
                animation: float-glow 10s ease-in-out infinite alternate;
            }
            @keyframes float-glow {
                0% { transform: translateY(0) scale(1); }
                100% { transform: translateY(-20px) scale(1.15); }
            }
        </style>
    </head>
    <body class="antialiased min-h-screen bg-slate-950 overflow-x-hidden relative text-slate-100 h-full flex flex-col lg:flex-row">

        <!-- Left Side: Brand Banner (Hidden on Mobile/Tablet) -->
        <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 relative flex-col justify-between p-12 overflow-hidden border-r border-slate-800">
            <!-- Glow elements inside banner -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[80%] h-[80%] rounded-full bg-indigo-600/20 glow-orb" style="animation-delay: 0s;"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[80%] h-[80%] rounded-full bg-purple-600/15 glow-orb" style="animation-delay: 3s;"></div>
            </div>

            <!-- Header logo -->
            <div class="relative z-10">
                <a href="/" class="inline-flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5a2 2 0 00-2 2v10a2 2 0 002 2zM19 20h2M16 12h3M16 16h3M8 8h4M8 12h4"/>
                        </svg>
                    </div>
                    <span class="text-xl font-black tracking-tight text-white group-hover:opacity-90 transition-opacity">
                        {{ config('app.name', 'Universal Blog CMS') }}
                    </span>
                </a>
            </div>

            <!-- Value propositions / Highlights -->
            <div class="relative z-10 my-auto space-y-8 max-w-md">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        ✨ Professional Blog Platform
                    </span>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Powering Your Content Hub
                    </h2>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Sign in to publish high-performance SEO optimized articles, track reader analytics, and utilize our smart AI assistant.
                    </p>
                </div>

                <div class="space-y-4 border-t border-slate-800/80 pt-6">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 flex-shrink-0">
                            🤖
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">AI Writing Assistant</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Generate articles, correct grammar, and suggest tags instantly.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 flex-shrink-0">
                            📈
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Advanced Analytics</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Monitor visitor devices, referrers, and page views in real-time.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 flex-shrink-0">
                            🔍
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">SEO Diagnostics</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Automatic schema generation, sitemap updates, and meta audits.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer links -->
            <div class="relative z-10 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side: Content Area (Full screen on mobile, 7/12 width on desktop) -->
        <div class="flex-1 flex flex-col min-h-screen relative overflow-hidden bg-slate-950">
            <!-- Background Radial Glows on Mobile -->
            <div class="absolute inset-0 overflow-hidden -z-10 pointer-events-none lg:hidden">
                <div class="absolute top-[-10%] left-[-10%] w-[70%] h-[70%] rounded-full bg-indigo-600/25 glow-orb" style="animation-delay: 0s;"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[70%] h-[70%] rounded-full bg-purple-600/20 glow-orb" style="animation-delay: 2s;"></div>
            </div>

            <!-- Top Header for Mobile/Tablet -->
            <div class="lg:hidden p-6 flex justify-between items-center w-full relative z-10">
                <a href="/" class="inline-flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-md">
                        {{ config('app.name', 'Universal Blog CMS') }}
                    </span>
                </a>
            </div>

            <!-- Centered Card Slot -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-10 lg:py-16 relative z-10">
                <div class="w-full max-w-md space-y-6">
                    <div class="glass-card shadow-2xl rounded-3xl p-8 sm:p-10 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <!-- Mobile Footer -->
            <footer class="lg:hidden py-6 text-center text-xs text-slate-500 border-t border-slate-900/60 bg-slate-950 relative z-10">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </footer>
        </div>
    </body>
</html>
