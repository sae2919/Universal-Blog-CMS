@extends('layouts.frontend')

@section('meta_title', __('Access Denied') . ' (403) — ' . \App\Models\Setting::getValue('site_name'))

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16 bg-gray-50 dark:bg-slate-950">
    <div class="max-w-2xl w-full text-center space-y-8">
        {{-- Animated 403 Illustration --}}
        <div class="relative flex justify-center">
            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-400/5 rounded-full filter blur-3xl w-72 h-72 mx-auto"></div>
            
            <div class="relative space-y-2">
                <span class="text-9xl font-black tracking-widest text-indigo-600 dark:text-indigo-400 select-none animate-pulse">
                    403
                </span>
                <div class="absolute bottom-1 right-0 left-0 text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    {{ __('Access Forbidden') }}
                </div>
            </div>
        </div>

        {{-- Explanatory Text --}}
        <div class="space-y-3">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                {{ __('Access Denied') }}
            </h2>
            <p class="text-sm sm:text-base text-gray-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                {{ __('You do not have the required permissions to access this page or resource on this server.') }}
            </p>
        </div>

        {{-- Back Home --}}
        <div class="pt-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Return to Homepage') }}
            </a>
        </div>
    </div>
</div>
@endsection
