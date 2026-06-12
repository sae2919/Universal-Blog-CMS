<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache remember wrapper.
     */
    public function remember(string $key, \DateTimeInterface $ttl, \Closure $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear specific cache key.
     */
    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Clear all blog and menu caches.
     */
    public function clearBlogCaches(): void
    {
        CacheHelper::clearAllBlogCaches();
    }
}
