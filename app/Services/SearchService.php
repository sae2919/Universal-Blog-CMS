<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Perform general full-text like searches.
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->searchPaginated($query, $perPage);
    }

    /**
     * Perform AJAX autocomplete searches.
     */
    public function autocomplete(string $query): Collection
    {
        if (empty($query) || strlen($query) < 2) {
            return collect();
        }

        return \App\Models\Post::with('category')
            ->published()
            ->forCurrentLocale()
            ->where('title', 'like', "%{$query}%")
            ->latest('published_at')
            ->take(5)
            ->get()
            ->map(function ($post) {
                return [
                    'title'          => $post->title,
                    'category_name'  => $post->category->name,
                    'category_emoji' => $post->category->icon_emoji,
                    'url'            => route('blog.show', [$post->category->slug, $post->slug]),
                ];
            });
    }
}
