<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Visit;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalVisits = Visit::count();
        $totalViews = Post::sum('views');
        $uniqueVisitors = Visit::distinct('ip_address')->count('ip_address');

        // Calculate Bounce Rate
        $singlePageIps = Visit::select('ip_address')
            ->groupBy('ip_address')
            ->havingRaw('count(*) = 1')
            ->get()
            ->count();
        
        $bounceRate = $uniqueVisitors > 0 ? round(($singlePageIps / $uniqueVisitors) * 100, 1) : 0;

        // Most Viewed Posts
        $mostViewed = Post::with('category')
            ->orderByDesc('views')
            ->take(5)
            ->get();

        // Top Articles (by visits count in the Visit log table)
        $topPagesRaw = Visit::select('url', \DB::raw('count(*) as count'))
            ->groupBy('url')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Map URL slug to Post titles
        $postsBySlug = Post::all()->keyBy('slug');
        $topArticles = $topPagesRaw->map(function ($page) use ($postsBySlug) {
            $parts = explode('/', trim($page->url, '/'));
            $slug = end($parts);
            $post = $postsBySlug->get($slug);
            
            $page->title = $post ? $post->title : ($page->url == '/' ? 'Home Page' : $page->url);
            $page->category = $post ? $post->category : null;
            return $page;
        });

        // Top Categories
        $topCategories = Category::withSum('posts', 'views')
            ->orderByDesc('posts_sum_views')
            ->take(5)
            ->get();

        // Traffic Sources (Google, Direct, LinkedIn, Twitter, Facebook, Other)
        $sourcesQuery = Visit::selectRaw("
            CASE 
                WHEN referer IS NULL OR referer = '' OR referer = 'direct' THEN 'Direct'
                WHEN referer LIKE '%google%' THEN 'Google'
                WHEN referer LIKE '%linkedin%' THEN 'LinkedIn'
                WHEN referer LIKE '%twitter%' OR referer LIKE '%t.co%' THEN 'Twitter'
                WHEN referer LIKE '%facebook%' OR referer LIKE '%fb.com%' THEN 'Facebook'
                ELSE 'Other'
            END as source_name,
            count(*) as count
        ")
        ->groupBy('source_name')
        ->orderByDesc('count')
        ->get();

        $sources = $sourcesQuery->map(function ($item) use ($totalVisits) {
            $item->percentage = $totalVisits > 0 ? round(($item->count / $totalVisits) * 100, 1) : 0;
            return $item;
        });

        // Top Countries
        $topCountries = Visit::select('country', \DB::raw('count(*) as count'))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->map(function ($item) use ($totalVisits) {
                $item->percentage = $totalVisits > 0 ? round(($item->count / $totalVisits) * 100, 1) : 0;
                return $item;
            });

        // Devices
        $devices = Visit::select('device', \DB::raw('count(*) as count'))
            ->whereNotNull('device')
            ->where('device', '!=', '')
            ->groupBy('device')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) use ($totalVisits) {
                $item->percentage = $totalVisits > 0 ? round(($item->count / $totalVisits) * 100, 1) : 0;
                return $item;
            });

        return view('admin.analytics', compact(
            'totalVisits',
            'totalViews',
            'uniqueVisitors',
            'bounceRate',
            'mostViewed',
            'topArticles',
            'topCategories',
            'sources',
            'topCountries',
            'devices'
        ));
    }
}
