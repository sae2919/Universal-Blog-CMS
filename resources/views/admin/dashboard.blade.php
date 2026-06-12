@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    {{-- Welcome Widget --}}
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold tracking-tight">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="mt-2 text-indigo-100 max-w-xl text-lg">Here's what's happening with your universal blog site today. Check post views, approve pending comments, or publish new articles.</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Stat Card: Total Views --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-sm font-semibold text-gray-500 uppercase">Total Views</span>
                <h3 id="stat-total-views" class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_views']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>

        {{-- Stat Card: Today Visits --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-sm font-semibold text-gray-500 uppercase">Today's Visits</span>
                <h3 id="stat-today-visits" class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['today_visits']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>

        {{-- Stat Card: Published Posts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-sm font-semibold text-gray-500 uppercase">Published Posts</span>
                <h3 id="stat-published-posts" class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['published_posts']) }}</h3>
                <span class="text-xs text-gray-400 mt-1 block">{{ $stats['draft_posts'] }} drafts pending</span>
            </div>
            <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Stat Card: Pending Comments --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-sm font-semibold text-gray-500 uppercase">Pending Comments</span>
                <h3 id="stat-pending-comments" class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['pending_comments']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Analytics Chart Section --}}
    <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-4">📈 Visitor Traffic (Last 7 Days)</h3>
        <div class="h-64 relative w-full">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    {{-- Bottom Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Posts --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-150 flex items-center justify-between">
                <h4 class="font-bold text-gray-800 text-lg">Recent Articles</h4>
                <a href="{{ route('admin.posts.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">View All</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentPosts as $post)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" alt="">
                        @else
                            <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-lg flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                Blog
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-gray-900 truncate text-base hover:text-indigo-600">
                                <a href="{{ route('admin.posts.edit', $post->id) }}">{{ $post->title }}</a>
                            </h5>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                <span>By {{ $post->author->name }}</span>
                                <span>&bull;</span>
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">{{ $post->category->name }}</span>
                                <span>&bull;</span>
                                <span>{{ $post->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-sm font-bold text-gray-900">{{ number_format($post->views) }}</span>
                            <p class="text-xs text-gray-400">Views</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">No posts found. Get started by writing your first article!</div>
                @endforelse
            </div>
        </div>

        {{-- Pending Comments --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-150 flex items-center justify-between">
                <h4 class="font-bold text-gray-800 text-lg">Pending Moderation</h4>
                <a href="{{ route('admin.comments.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">View All</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingComments as $comment)
                    <div class="p-5 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 text-sm truncate max-w-[150px]">{{ $comment->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 truncate">On: <span class="font-semibold text-gray-700">{{ $comment->post->title }}</span></p>
                        <p class="text-sm text-gray-600 mt-2 bg-gray-50 p-3 rounded-lg border border-gray-100 italic">
                            "{{ Str::limit($comment->comment, 80) }}"
                        </p>
                        <div class="flex items-center gap-2 mt-3 justify-end">
                            <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-semibold text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg border border-green-200">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg border border-red-200">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-48">
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>No comments pending moderation.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalViewsEl = document.getElementById('stat-total-views');
        const todayVisitsEl = document.getElementById('stat-today-visits');
        const publishedPostsEl = document.getElementById('stat-published-posts');
        const pendingCommentsEl = document.getElementById('stat-pending-comments');

        function fetchStats() {
            fetch("{{ route('admin.dashboard.stats') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (totalViewsEl && data.total_views !== undefined) {
                    totalViewsEl.textContent = Number(data.total_views).toLocaleString();
                }
                if (todayVisitsEl && data.today_visits !== undefined) {
                    todayVisitsEl.textContent = Number(data.today_visits).toLocaleString();
                }
                if (publishedPostsEl && data.published_posts !== undefined) {
                    publishedPostsEl.textContent = Number(data.published_posts).toLocaleString();
                }
                if (pendingCommentsEl && data.pending_comments !== undefined) {
                    pendingCommentsEl.textContent = Number(data.pending_comments).toLocaleString();
                }
            })
            .catch(error => {
                console.error('Error fetching dashboard stats:', error);
            });
        }

        // Poll stats every 5 seconds (5000ms)
        setInterval(fetchStats, 5000);

        // Render Analytics Chart
        const ctx = document.getElementById('analyticsChart');
        if (ctx) {
            fetch("{{ route('admin.dashboard.analytics') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Visits',
                            data: data.visits,
                            borderColor: 'rgb(79, 70, 229)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(79, 70, 229)',
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading analytics chart:', error);
            });
        }
    });
</script>
@endpush
