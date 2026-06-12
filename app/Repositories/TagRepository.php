<?php

namespace App\Repositories;

use App\Models\Tag;

class TagRepository
{
    public function findBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }
}
