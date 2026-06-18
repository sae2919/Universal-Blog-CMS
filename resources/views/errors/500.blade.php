@extends('layouts.frontend')

@section('meta_title', __('Internal Server Error') . ' (500) — ' . \App\Models\Setting::getValue('site_name'))

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16 bg-gray-50 dark:bg-slate-950">
    <div class="max-w-2xl w-full text-center space-y-8">
        {{-- Animated 500 Illustration --}}
        <div class="relative flex justify-center">
            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-400/5 rounded-full filter blur-3xl w-72 h-72 mx-auto"></div>
            
            <div class="relative space-y-2">
                <span class="text-9xl font-black tracking-widest text-indigo-600 dark:text-indigo-400 select-none animate-pulse">
                    500
                </span>
                <div class="absolute bottom-1 right-0 left-0 text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    {{ __('Internal Server Error') }}
                </div>
            </div>
        </div>

        {{-- Explanatory Text --}}
        <div class="space-y-3">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                {{ __('Oops! Something Went Wrong') }}
            </h2>
            <p class="text-sm sm:text-base text-gray-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                {{ __('We experienced an unexpected error on our servers while processing this request. Our technical team has been notified.') }}
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto pt-4">
            <button onclick="window.location.reload();" 
                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-150 shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3"/>
                </svg>
                {{ __('Reload Page') }}
            </button>
            <a href="{{ route('home') }}" 
               class="w-full sm:w-auto bg-white hover:bg-gray-105 border border-gray-200 dark:bg-slate-900 dark:border-slate-800 dark:hover:bg-slate-800 text-gray-750 dark:text-slate-205 px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-150 shadow-sm flex items-center justify-center gap-2">
                {{ __('Return Home') }}
            </a>
        </div>
    </div>
</div>
@endsection
