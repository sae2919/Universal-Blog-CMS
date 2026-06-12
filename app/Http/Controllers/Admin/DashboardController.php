<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Visit;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posts'       => Post::count(),
            'published_posts'   => Post::where('status', 'published')->count(),
            'draft_posts'       => Post::where('status', 'draft')->count(),
            'total_categories'  => Category::count(),
            'pending_comments'  => Comment::pending()->count(),
            'total_views'       => Post::sum('views'),
            'today_visits'      => Visit::whereDate('visited_at', today())->count(),
        ];

        $recentPosts = Post::with('author', 'category')
            ->latest()
            ->take(5)
            ->get();

        $pendingComments = Comment::with('post')
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'pendingComments'));
    }

    public function stats()
    {
        return response()->json([
            'total_views'      => Post::sum('views'),
            'today_visits'     => Visit::whereDate('visited_at', today())->count(),
            'published_posts'  => Post::where('status', 'published')->count(),
            'pending_comments' => Comment::pending()->count(),
        ]);
    }

    public function analytics()
    {
        $data = collect(range(6, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);
            $visits = Visit::whereDate('visited_at', $date)->count();
            return [
                'date' => $date->format('M d'),
                'visits' => $visits,
            ];
        });

        return response()->json([
            'labels' => $data->pluck('date'),
            'visits' => $data->pluck('visits'),
        ]);
    }
}
