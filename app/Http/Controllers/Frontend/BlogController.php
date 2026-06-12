<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Setting;
use App\Models\Page;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    protected $postRepository;
    protected $categoryRepository;
    protected $postService;

    public function __construct(
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        PostService $postService
    ) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        $settings = Setting::first();

        $posts = $this->postRepository->getPublishedPaginated($settings?->posts_per_page ?? 10);

        $popularPosts = Cache::remember('posts.popular.sidebar', now()->addHours(2), function () {
            return $this->postRepository->getPopular(5);
        });

        $categories = Cache::remember('categories.sidebar', now()->addHours(6), function () {
            return $this->categoryRepository->getActiveWithPostCount();
        });

        return view('frontend.blog.index', compact('posts', 'popularPosts', 'categories'));
    }

    public function showPost(string $categorySlug, string $postSlug)
    {
        $post = Cache::remember("post.{$postSlug}", now()->addHours(6), function () use ($categorySlug, $postSlug) {
            return $this->postRepository->findBySlug($categorySlug, $postSlug);
        });

        if ($post) {
            // Fetch live views directly from database to bypass cache
            $post->views = \DB::table('posts')->where('id', $post->id)->value('views');

            // Calculate read time dynamically
            $post->read_time = $this->postService->calculateReadTime($post);

            // Increment view count asynchronously
            $this->postService->incrementViewsAsync($post);

            $relatedPosts = Cache::remember("post.related.{$postSlug}", now()->addHours(3), function () use ($post) {
                return $this->postRepository->getRelated($post, 4);
            });

            $trendingPosts = Cache::remember("posts.trending.sidebar", now()->addHours(2), function () {
                return Post::trending()->take(5)->get();
            });

            if ($trendingPosts->isEmpty()) {
                $trendingPosts = Cache::remember("posts.popular.sidebar.fallback", now()->addHours(2), function () {
                    return Post::published()->orderByDesc('views')->take(5)->get();
                });
            }

            return view('frontend.blog.show', compact('post', 'relatedPosts', 'trendingPosts'));
        }

        abort(404);
    }

    public function showPage(string $slug)
    {
        $page = Cache::remember("page.{$slug}", now()->addHours(6), function () use ($slug) {
            return Page::published()->where('slug', $slug)->first();
        });

        if ($page) {
            return view('frontend.pages.show', compact('page'));
        }

        abort(404);
    }

    public function storeComment(Request $request, string $categorySlug, string $postSlug)
    {
        $post = Post::where('slug', $postSlug)
            ->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->firstOrFail();

        if (!$post->allow_comments) {
            return back()->with('error', 'Comments are disabled on this post.');
        }

        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:255',
            'website'   => 'nullable|url|max:255',
            'comment'   => 'required|string|min:5|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $validated['post_id']    = $post->id;
        $validated['ip_address'] = $request->ip();

        Comment::create($validated);

        return back()->with('success', 'Comment submitted! It will appear after moderation.');
    }
}
