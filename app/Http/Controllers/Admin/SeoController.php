<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Page;

class SeoController extends Controller
{
    /**
     * Display general SEO settings and diagnostic status.
     */
    public function index()
    {
        $posts = Post::published()->latest()->take(10)->get();
        $pages = Page::published()->latest()->take(10)->get();
        return view('admin.seo.index', compact('posts', 'pages'));
    }
}
