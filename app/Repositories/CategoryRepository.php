<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryRepository
{
    public function getActiveWithPostCount(): Collection
    {
        return Category::active()->withCount('posts')->orderBy('sort_order')->get();
    }

    public function findActiveBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->active()->first();
    }

    public function getActiveRoots(int $limit = 6): Collection
    {
        return Category::active()->roots()->orderBy('sort_order')->take($limit)->get();
    }
}
