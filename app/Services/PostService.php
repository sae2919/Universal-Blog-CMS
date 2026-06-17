<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostService
{
    /**
     * Calculate read time dynamically (average reading speed: 200 words per minute).
     */
    public function calculateReadTime(Post $post): int
    {
        $wordCount = str_word_count(strip_tags($post->content));
        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Increment view count and update the post object immediately so the
     * current page render shows the correct (post-visit) count.
     * Cache busting for popular/trending listing pages runs after the
     * response is sent to keep the request fast.
     */
    public function incrementViewsAsync(Post $post): void
    {
        // Increment in DB and reflect on the model straight away
        $post->increment('views');
        $post->views = $post->views; // already updated by increment()

        // Clear view-count-sensitive listing caches after the response is sent
        $locales = ['en', 'fr', 'de', 'hi', 'te'];
        dispatch(function () use ($locales) {
            foreach ($locales as $locale) {
                Cache::forget("posts.popular.{$locale}");
                Cache::forget("posts.popular.sidebar.{$locale}");
                Cache::forget("posts.popular.sidebar.fallback.{$locale}");
                Cache::forget("posts.trending.{$locale}");
                Cache::forget("posts.trending.sidebar.{$locale}");
            }
        })->afterResponse();
    }
}
