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
        $locale = app()->getLocale();

        $posts = $this->postRepository->getPublishedPaginated($settings?->posts_per_page ?? 10);

        $popularPosts = Cache::remember("posts.popular.sidebar.{$locale}", now()->addHours(2), function () {
            return $this->postRepository->getPopular(5);
        });

        $categories = Cache::remember("categories.sidebar.{$locale}", now()->addHours(6), function () {
            return $this->categoryRepository->getActiveWithPostCount();
        });

        return view('frontend.blog.index', compact('posts', 'popularPosts', 'categories'));
    }

    public function showPost(string $categorySlug, string $postSlug)
    {
        $locale = app()->getLocale();

        $post = Cache::remember("post.{$postSlug}.{$locale}", now()->addHours(6), function () use ($categorySlug, $postSlug) {
            return $this->postRepository->findBySlug($categorySlug, $postSlug);
        });

        if ($post) {
            // Calculate read time dynamically
            $post->read_time = $this->postService->calculateReadTime($post);

            // Increment view count (updates DB + model immediately, clears listing caches after response)
            $this->postService->incrementViewsAsync($post);

            $relatedPosts = Cache::remember("post.related.{$postSlug}.{$locale}", now()->addHours(3), function () use ($post) {
                return $this->postRepository->getRelated($post, 4);
            });

            $trendingPosts = Cache::remember("posts.trending.sidebar.{$locale}", now()->addHours(2), function () {
                return Post::trending()->forCurrentLocale()->take(5)->get();
            });

            if ($trendingPosts->isEmpty()) {
                $trendingPosts = Cache::remember("posts.popular.sidebar.fallback.{$locale}", now()->addHours(2), function () {
                    return Post::published()->forCurrentLocale()->orderByDesc('views')->take(5)->get();
                });
            }

            return view('frontend.blog.show', compact('post', 'relatedPosts', 'trendingPosts'));
        }

        abort(404);
    }

    public function showPage(string $slug)
    {
        $locale = app()->getLocale();

        $page = Cache::remember("page.{$slug}.{$locale}", now()->addHours(6), function () use ($slug) {
            return Page::published()->where('slug', $slug)->first();
        });

        if ($slug === 'contact-us') {
            if (!$page) {
                $page = new Page([
                    'title' => 'Contact Us',
                    'slug' => 'contact-us',
                    'status' => 'published',
                    'meta_title' => 'Contact Us — ' . Setting::getValue('site_name'),
                    'meta_description' => 'Contact Us page description.'
                ]);
            }
            return view('frontend.pages.contact', compact('page'));
        }

        if ($slug === 'about-us') {
            if (!$page) {
                $page = new Page([
                    'title' => 'About Us',
                    'slug' => 'about-us',
                    'status' => 'published',
                    'meta_title' => 'About Us — ' . Setting::getValue('site_name'),
                    'meta_description' => 'Learn more about our mission, vision, values, and team.'
                ]);
            }
            return view('frontend.pages.about', compact('page'));
        }

        if ($page) {
            return view('frontend.pages.show', compact('page'));
        }

        abort(404);
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        \App\Models\Notification::create([
            'title'   => 'New Contact Form Submission: ' . $validated['subject'],
            'message' => "Name: {$validated['name']}\nEmail: {$validated['email']}\n\nMessage:\n{$validated['message']}",
            'type'    => 'contact',
            'link'    => '/admin/notifications',
        ]);

        return back()->with('success', 'Thank you for contacting us! We have received your message and will get back to you shortly.');
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

        $comment = Comment::create($validated);

        // Clear cached post content to reflect comment updates immediately
        Cache::forget("post.{$post->slug}.en");
        Cache::forget("post.{$post->slug}.fr");
        Cache::forget("post.{$post->slug}.de");
        Cache::forget("post.{$post->slug}.hi");
        Cache::forget("post.{$post->slug}.te");

        \App\Models\Notification::create([
            'title' => 'New Comment Awaiting Approval',
            'message' => "Comment submitted by {$comment->name} on \"{$post->title}\".",
            'type' => 'comment',
            'link' => '/admin/comments'
        ]);

        return back()->with('success', 'Comment submitted! It will appear after moderation.');
    }
}
