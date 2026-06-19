@extends('layouts.admin')

@section('title', 'Notifications Center')
@section('header', 'Notifications Center')

@php
    $unreadNotificationsCount = \App\Models\Notification::unread()->count();
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-slate-100">System Notifications</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                View and manage all system updates, comment moderations, registration alerts, and SEO diagnostics warnings.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($unreadNotificationsCount > 0)
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/35 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900 rounded-lg text-sm font-semibold transition-all cursor-pointer">
                        ✓ Mark All Read
                    </button>
                </form>
            @endif

            @if($notifications->count() > 0)
                <form action="{{ route('admin.notifications.clear-all') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all notifications? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900 rounded-lg text-sm font-semibold transition-all cursor-pointer">
                        🗑 Clear All
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($notifications as $notification)
                <div class="p-5 transition-colors flex items-start gap-4 {{ is_null($notification->read_at) ? 'bg-indigo-50/20 dark:bg-indigo-950/5' : 'bg-white dark:bg-slate-800' }}">
                    {{-- Icon matching type --}}
                    <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center
                        @if($notification->type === 'comment') bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400
                        @elseif($notification->type === 'user') bg-green-50 text-green-600 dark:bg-green-950 dark:text-green-400
                        @elseif($notification->type === 'seo') bg-yellow-50 text-yellow-600 dark:bg-yellow-950 dark:text-yellow-400
                        @else bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-400
                        @endif">
                        @if($notification->type === 'comment')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        @elseif($notification->type === 'user')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        @elseif($notification->type === 'seo')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Notification Details --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-bold text-gray-900 dark:text-slate-100 text-sm leading-snug">
                                {{ $notification->title }}
                            </h4>
                            @if(is_null($notification->read_at))
                                <span class="w-2 h-2 bg-indigo-500 rounded-full" title="Unread"></span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-650 dark:text-slate-350 mt-1 leading-relaxed">
                            {{ $notification->message }}
                        </p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400 dark:text-slate-455">
                            <span>{{ $notification->created_at->format('M d, Y \a\t H:i') }}</span>
                            <span>•</span>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                            @if($notification->read_at)
                                <span>•</span>
                                <span class="text-green-600 dark:text-green-400">Read {{ $notification->read_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 flex-shrink-0 self-center">
                        @if($notification->link)
                            <a href="{{ route('admin.notifications.click', $notification->id) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm">
                                View
                            </a>
                        @endif

                        @if(is_null($notification->read_at))
                            <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-slate-200 rounded text-xs font-semibold cursor-pointer">
                                    Mark Read
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-650 rounded hover:text-red-900 dark:text-red-400 transition-colors cursor-pointer" title="Delete Notification">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center text-gray-500 dark:text-slate-400">
                    <svg class="w-12 h-12 text-gray-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="font-bold text-gray-700 dark:text-slate-300">All caught up!</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">No system notifications found in your inbox.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 dark:border-slate-700 bg-gray-50 dark:bg-slate-750/30">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
