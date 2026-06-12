<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Clear all blog and navigation caches.
     */
    public static function clearAllBlogCaches(): void
    {
        Cache::forget('posts.popular.sidebar');
        Cache::forget('categories.sidebar');
        Cache::forget('nav.menu.main');
        Cache::forget('nav.menu.footer');
        Cache::forget('nav.categories');
    }
}
