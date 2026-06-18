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
    <body class="antialiased min-h-screen bg-slate-950 flex flex-col justify-between overflow-x-hidden relative text-slate-100 h-full">
        <!-- Background Radial Glows -->
        <div class="absolute inset-0 overflow-hidden -z-10 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-indigo-600/35 glow-orb" style="animation-delay: 0s;"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-purple-600/30 glow-orb" style="animation-delay: 2s;"></div>
            <div class="absolute top-[35%] right-[15%] w-[35%] h-[35%] rounded-full bg-blue-600/20 glow-orb" style="animation-delay: 4s;"></div>
        </div>

        <div class="flex-1 flex flex-col justify-center items-center px-4 py-16 sm:px-6 lg:px-8 relative">
            <div class="w-full max-w-md space-y-8">
                <!-- Brand/Logo -->
                <div class="text-center space-y-2">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-xl shadow-indigo-500/30 group-hover:scale-105 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5a2 2 0 00-2 2v10a2 2 0 002 2zM19 20h2M16 12h3M16 16h3M8 8h4M8 12h4"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-white group-hover:opacity-90 transition-opacity">
                            {{ config('app.name', 'Universal Blog CMS') }}
                        </span>
                    </a>
                </div>

                <!-- Main Card -->
                <div class="glass-card shadow-2xl rounded-3xl p-8 sm:p-10 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Minimal Footer -->
        <footer class="py-6 text-center text-xs text-slate-500 relative z-10 border-t border-slate-900/60 bg-slate-950">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </footer>
    </body>
</html>
