@php
    $unreadNotificationsCount = \App\Models\Notification::unread()->count();
    $latestNotifications = \App\Models\Notification::unread()->latest()->take(5)->get();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">

    <title>@yield('title', 'Admin Panel') — {{ config('app.name') }}</title>

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
</head>
<body class="bg-gray-100 dark:bg-slate-900 dark:text-slate-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col">
        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white tracking-tight">
                 {{ config('app.name') }}
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.analytics') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.analytics') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>

            <a href="{{ route('admin.posts.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.posts.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Posts
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categories
            </a>

            <a href="{{ route('admin.tags.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.tags.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                Tags
            </a>

            <a href="{{ route('admin.pages.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.pages.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Pages
            </a>

            <a href="{{ route('admin.comments.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.comments.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Comments
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.menus.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.menus.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Navigation Menus
            </a>



            <a href="{{ route('admin.seo.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.seo.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                SEO Diagnostics
            </a>

            <a href="{{ route('admin.notifications.index') }}"
               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.notifications.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Notifications</span>
                </div>
                @if($unreadNotificationsCount > 0)
                    <span class="bg-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full">
                        {{ $unreadNotificationsCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.trash.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.trash.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Trash
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>




            {{-- Divider --}}
            <div class="pt-4 border-t border-gray-700">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Website</p>
                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    View Website
                </a>
            </div>
        </nav>

        {{-- User Info --}}
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Header --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
            <h1 class="text-lg font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
            <div class="flex items-center gap-4">
                {{-- Notifications Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 rounded-lg text-gray-500 hover:text-indigo-650 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors relative cursor-pointer" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        @endif
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-lg py-2 z-50 overflow-hidden"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-800/30">
                            <span class="font-bold text-sm text-gray-800 dark:text-slate-200">Notifications</span>
                            @if($unreadNotificationsCount > 0)
                                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold cursor-pointer">Mark all read</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($latestNotifications as $notification)
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center
                                        @if($notification->type === 'comment') bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400
                                        @elseif($notification->type === 'user') bg-green-50 text-green-600 dark:bg-green-950 dark:text-green-400
                                        @elseif($notification->type === 'seo') bg-yellow-50 text-yellow-600 dark:bg-yellow-950 dark:text-yellow-400
                                        @else bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-400
                                        @endif">
                                        @if($notification->type === 'comment')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        @elseif($notification->type === 'user')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        @elseif($notification->type === 'seo')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('admin.notifications.click', $notification->id) }}" class="font-bold text-xs text-gray-900 dark:text-slate-100 hover:underline truncate">{{ $notification->title }}</a>
                                            <span class="text-[9px] text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 truncate mt-0.5">{{ $notification->message }}</p>
                                        
                                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="mt-1">
                                            @csrf
                                            <button type="submit" class="text-[10px] text-indigo-650 dark:text-indigo-400 hover:underline cursor-pointer">Mark read</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-gray-500 dark:text-slate-400">
                                    No new notifications!
                                </div>
                            @endforelse
                        </div>

                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-slate-700 text-center bg-gray-50 dark:bg-slate-750/30">
                            <a href="{{ route('admin.notifications.index') }}" class="text-xs text-indigo-650 dark:text-indigo-400 font-semibold hover:underline block">View all notifications</a>
                        </div>
                    </div>
                </div>

                {{-- Toggle Theme --}}
                <button onclick="
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                " class="p-2 rounded-lg text-gray-500 hover:text-indigo-650 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Toggle Theme">
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z"/>
                    </svg>
                </button>
                <span class="text-sm text-gray-500">{{ now()->format('D, d M Y') }}</span>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm mb-4">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm mb-4">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    // Centralized Image Registry and Logging
    window.uploadedImagesMap = window.uploadedImagesMap || {};
    let globalImageCounter = 1;

    window.initializeImageRegistry = function(existingData) {
        if (existingData && typeof existingData === 'object') {
            window.uploadedImagesMap = { ...window.uploadedImagesMap, ...existingData };
            console.log('Image registry initialized with:', window.uploadedImagesMap);
        }
    };

    window.registerAndLogImageFile = function(file) {
        if (!file) return null;
        const id = 'img_' + Date.now() + '_' + globalImageCounter++;
        const info = {
            fileName: file.name,
            fileType: file.type,
            fileSize: file.size
        };
        window.uploadedImagesMap[id] = info;
        console.log(`images[${id}]`, info);
        return id;
    };

    window.registerAndLogImageDetails = function(fileName, fileType, fileSize) {
        const id = 'img_' + Date.now() + '_' + globalImageCounter++;
        const info = {
            fileName: fileName,
            fileType: fileType || 'image/jpeg',
            fileSize: fileSize || 0
        };
        window.uploadedImagesMap[id] = info;
        console.log(`images[${id}]`, info);
        return id;
    };

    document.addEventListener('change', function(e) {
        const input = e.target;
        if (input.type === 'file' && input.files && input.files[0]) {
            const file = input.files[0];
            if (file.type.startsWith('image/')) {
                // Register and log the file choice globally
                window.registerAndLogImageFile(file);

                let containerId = input.id + '-selected-preview-container';
                let previewContainer = document.getElementById(containerId);

                if (!previewContainer) {
                    previewContainer = document.createElement('div');
                    previewContainer.id = containerId;
                    previewContainer.className = 'mt-2 mb-2 relative max-w-sm';
                    input.parentNode.insertBefore(previewContainer, input);
                }
                
                const objectUrl = URL.createObjectURL(file);
                previewContainer.innerHTML = `
                    <div class="relative rounded-lg overflow-hidden border border-gray-200 shadow-sm max-h-40 bg-gray-50 flex items-center justify-center">
                        <img src="${objectUrl}" class="w-full h-32 object-cover" />
                        <button type="button" onclick="clearSelectedFileInput('${input.id}', '${containerId}')" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1.5 shadow-md transition-colors cursor-pointer" title="Remove Selected File">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                `;
                
                input.addEventListener('change', () => URL.revokeObjectURL(objectUrl), { once: true });
            }
        }
    });

    window.clearSelectedFileInput = function(inputId, containerId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = '';
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        const container = document.getElementById(containerId);
        if (container) {
            container.remove();
        }
    };
    
    window.clearMediaLibrarySelection = function() {
        const previewContainer = document.getElementById('image-preview-container');
        if (previewContainer) {
            previewContainer.innerHTML = '';
        }
        const removeInput = document.getElementById('remove_featured_image');
        if (removeInput) {
            removeInput.value = '1';
        }
    };
</script>

@stack('scripts')
</body>
</html>
