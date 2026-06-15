<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        $locale = app()->getLocale();

        $featuredPosts = Cache::remember("posts.featured.{$locale}", now()->addHours(2), function () {
            return Post::with('author', 'category')->featured()->forCurrentLocale()->latest('published_at')->take(5)->get();
        });

        $trendingPosts = Cache::remember("posts.trending.{$locale}", now()->addHours(2), function () {
            return Post::with('author', 'category')->trending()->forCurrentLocale()->latest('published_at')->take(6)->get();
        });

        $latestPosts = Cache::remember("posts.latest.{$locale}", now()->addMinutes(30), function () use ($settings) {
            return Post::with('author', 'category')->published()->forCurrentLocale()->latest('published_at')
                ->take($settings?->posts_per_page ?? 10)->get();
        });

        $popularPosts = Cache::remember("posts.popular.{$locale}", now()->addHours(1), function () {
            return Post::published()->forCurrentLocale()->orderByDesc('views')->take(5)->get();
        });

        $categories = Cache::remember("categories.main.{$locale}", now()->addHours(6), function () {
            return Category::active()->roots()
                ->withCount(['posts' => function ($query) {
                    $query->published()->forCurrentLocale();
                }])
                ->orderBy('sort_order')
                ->take(8)
                ->get();
        });

        return view('frontend.home', compact(
            'featuredPosts', 'trendingPosts', 'latestPosts', 'popularPosts', 'categories', 'settings'
        ));
    }
}
