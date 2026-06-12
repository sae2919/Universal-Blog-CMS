<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\TagRepository;
use App\Repositories\PostRepository;
use App\Models\Setting;

class TagController extends Controller
{
    protected $tagRepository;
    protected $postRepository;

    public function __construct(TagRepository $tagRepository, PostRepository $postRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Render tag posts list.
     */
    public function tag(string $slug)
    {
        $tag = $this->tagRepository->findBySlug($slug);
        
        if (!$tag) {
            abort(404);
        }

        $posts = $this->postRepository->getByTagPaginated(
            $slug,
            Setting::getValue('posts_per_page', 10)
        );

        return view('frontend.tag.index', compact('tag', 'posts'));
    }
}
