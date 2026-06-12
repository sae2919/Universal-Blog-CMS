<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Cache::remember("page.{$slug}", now()->addHours(6), function () use ($slug) {
            return Page::published()->where('slug', $slug)->firstOrFail();
        });

        return view('frontend.pages.show', compact('page'));
    }
}
