<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Models\Setting;

class CategoryController extends Controller
{
    protected $categoryRepository;
    protected $postRepository;

    public function __construct(CategoryRepository $categoryRepository, PostRepository $postRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Render category posts list.
     */
    public function category(string $slug)
    {
        $category = $this->categoryRepository->findActiveBySlug($slug);
        
        if (!$category) {
            abort(404);
        }

        $posts = $this->postRepository->getByCategoryPaginated(
            $category->id,
            Setting::getValue('posts_per_page', 10)
        );

        return view('frontend.category.index', compact('category', 'posts'));
    }
}
