<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('author', 'category')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'excerpt'          => 'nullable|string',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|image|max:2048',
            'status'           => 'required|in:draft,published,scheduled,archived',
            'published_at'     => 'nullable|date',
            'is_featured'      => 'boolean',
            'is_trending'      => 'boolean',
            'allow_comments'   => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
            'faqs'             => 'nullable|array',
            'faqs.*.question'  => 'required|string',
            'faqs.*.answer'    => 'required|string',
            'cta_title'              => 'nullable|string|max:255',
            'cta_description'        => 'nullable|string',
            'cta_button_text'        => 'nullable|string|max:255',
            'cta_button_link'        => 'nullable|string|max:255',
            'cta_bg_image'           => 'nullable|image|max:2048',
            'cta_directory_title'    => 'nullable|string|max:255',
            'cta_directory_subtitle' => 'nullable|string|max:255',
            'cta_col1_title'         => 'nullable|string|max:255',
            'cta_col1_links'         => 'nullable|string',
            'cta_col2_title'         => 'nullable|string|max:255',
            'cta_col2_links'         => 'nullable|string',
            'cta_col3_title'         => 'nullable|string|max:255',
            'cta_col3_links'         => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('posts', 'public');
        }

        if ($request->hasFile('cta_bg_image')) {
            $validated['cta_bg_image'] = $request->file('cta_bg_image')
                ->store('ctas', 'public');
        }

        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);
        $post->tags()->sync($request->tags ?? []);

        $this->clearPostCaches();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = Category::active()->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'excerpt'          => 'nullable|string',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|image|max:2048',
            'status'           => 'required|in:draft,published,scheduled,archived',
            'published_at'     => 'nullable|date',
            'is_featured'      => 'boolean',
            'is_trending'      => 'boolean',
            'allow_comments'   => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'faqs'             => 'nullable|array',
            'faqs.*.question'  => 'required|string',
            'faqs.*.answer'    => 'required|string',
            'cta_title'              => 'nullable|string|max:255',
            'cta_description'        => 'nullable|string',
            'cta_button_text'        => 'nullable|string|max:255',
            'cta_button_link'        => 'nullable|string|max:255',
            'cta_bg_image'           => 'nullable|image|max:2048',
            'cta_directory_title'    => 'nullable|string|max:255',
            'cta_directory_subtitle' => 'nullable|string|max:255',
            'cta_col1_title'         => 'nullable|string|max:255',
            'cta_col1_links'         => 'nullable|string',
            'cta_col2_title'         => 'nullable|string|max:255',
            'cta_col2_links'         => 'nullable|string',
            'cta_col3_title'         => 'nullable|string|max:255',
            'cta_col3_links'         => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('posts', 'public');
        }

        if ($request->hasFile('cta_bg_image')) {
            $validated['cta_bg_image'] = $request->file('cta_bg_image')
                ->store('ctas', 'public');
        }

        $post->update($validated);
        $post->tags()->sync($request->tags ?? []);

        $this->clearPostCaches($post);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $this->clearPostCaches($post);
        $post->delete();
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    private function clearPostCaches(?Post $post = null): void
    {
        Cache::forget('posts.featured');
        Cache::forget('posts.trending');
        Cache::forget('posts.latest');
        Cache::forget('homepage_posts');

        if ($post) {
            Cache::forget("post.{$post->slug}");
        }
    }
}
