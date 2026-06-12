<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(12), function () {
            $posts = Post::published()->latest('published_at')->get();
            $categories = Category::active()->get();
            $tags = Tag::all();
            $pages = Page::published()->get();

            return view('frontend.sitemap', compact('posts', 'categories', 'tags', 'pages'))->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
