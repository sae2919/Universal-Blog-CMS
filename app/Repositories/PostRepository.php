<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PostRepository
{
    public function getPublishedPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Post::with('author', 'category', 'tags')
            ->published()
            ->forCurrentLocale()
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getPopular(int $limit = 5): Collection
    {
        return Post::published()
            ->forCurrentLocale()
            ->orderByDesc('views')
            ->take($limit)
            ->get();
    }

    public function getRelated(Post $post, int $limit = 4): Collection
    {
        $related = Post::published()
            ->forCurrentLocale()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take($limit)
            ->get();

        if ($related->isEmpty()) {
            return Post::published()
                ->forCurrentLocale()
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->take($limit)
                ->get();
        }

        return $related;
    }

    public function findBySlug(string $categorySlug, string $postSlug): ?Post
    {
        return Post::with(['author', 'category', 'tags', 'approvedComments.replies'])
            ->published()
            ->where('slug', $postSlug)
            ->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->first();
    }

    public function getByCategoryPaginated(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with('author', 'category')
            ->published()
            ->forCurrentLocale()
            ->where('category_id', $categoryId)
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getByTagPaginated(string $tagSlug, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with('author', 'category')
            ->published()
            ->forCurrentLocale()
            ->whereHas('tags', fn($q) => $q->where('slug', $tagSlug))
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function searchPaginated(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with('author', 'category')
            ->published()
            ->forCurrentLocale()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate($perPage);
    }
}
