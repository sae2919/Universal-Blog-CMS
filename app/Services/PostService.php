<?php

namespace App\Services;

use App\Models\Post;

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
     * Increment view count asynchronously after response is sent.
     */
    public function incrementViewsAsync(Post $post): void
    {
        dispatch(function () use ($post) {
            $post->increment('views');
        })->afterResponse();
    }
}
