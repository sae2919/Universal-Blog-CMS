<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    /**
     * Get or build cached sitemap XML.
     */
    public function getSitemapXml(): string
    {
        return Cache::remember('sitemap.xml', now()->addHours(12), function () {
            $posts = Post::published()->latest('published_at')->get();
            $categories = Category::active()->get();
            $tags = Tag::all();
            $pages = Page::published()->get();

            return view('frontend.sitemap', compact('posts', 'categories', 'tags', 'pages'))->render();
        });
    }
}
