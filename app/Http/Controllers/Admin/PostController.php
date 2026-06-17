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
            'locale'           => 'required|in:en,fr,de,hi,te',
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
            'image_metadata'         => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('posts', 'public');
        } elseif ($request->filled('generated_image_path')) {
            $validated['featured_image'] = $request->input('generated_image_path');
        }

        if ($request->hasFile('cta_bg_image')) {
            $validated['cta_bg_image'] = $request->file('cta_bg_image')
                ->store('ctas', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_trending'] = $request->boolean('is_trending');
        $validated['allow_comments'] = $request->boolean('allow_comments');
        if ($request->has('image_metadata')) {
            $meta = $request->input('image_metadata');
            $validated['image_metadata'] = is_string($meta) ? json_decode($meta, true) : null;
        }

        // Handle published_at logic for immediately published posts
        if ($validated['status'] === 'published') {
            if (empty($validated['published_at']) || \Carbon\Carbon::parse($validated['published_at'])->isFuture()) {
                $validated['published_at'] = now();
            }
        }

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
            'locale'           => 'required|in:en,fr,de,hi,te',
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
            'image_metadata'         => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image && \Storage::disk('public')->exists($post->featured_image)) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('posts', 'public');
        } elseif ($request->filled('generated_image_path')) {
            if ($post->featured_image && \Storage::disk('public')->exists($post->featured_image)) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->input('generated_image_path');
        } elseif ($request->boolean('remove_featured_image')) {
            if ($post->featured_image && \Storage::disk('public')->exists($post->featured_image)) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = null;
        }

        if ($request->hasFile('cta_bg_image')) {
            if ($post->cta_bg_image && \Storage::disk('public')->exists($post->cta_bg_image)) {
                \Storage::disk('public')->delete($post->cta_bg_image);
            }
            $validated['cta_bg_image'] = $request->file('cta_bg_image')
                ->store('ctas', 'public');
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_trending'] = $request->boolean('is_trending');
        $validated['allow_comments'] = $request->boolean('allow_comments');
        if ($request->has('image_metadata')) {
            $meta = $request->input('image_metadata');
            $validated['image_metadata'] = is_string($meta) ? json_decode($meta, true) : null;
        }

        // Handle published_at logic for immediately published posts
        if ($validated['status'] === 'published') {
            if (empty($validated['published_at']) || \Carbon\Carbon::parse($validated['published_at'])->isFuture()) {
                $validated['published_at'] = now();
            }
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
        $locales = ['en', 'fr', 'de', 'hi', 'te'];
        foreach ($locales as $locale) {
            Cache::forget("posts.featured.{$locale}");
            Cache::forget("posts.trending.{$locale}");
            Cache::forget("posts.latest.{$locale}");
            Cache::forget("posts.popular.{$locale}");
            Cache::forget("posts.popular.sidebar.{$locale}");
            Cache::forget("categories.main.{$locale}");
            Cache::forget("categories.sidebar.{$locale}");
            if ($post) {
                Cache::forget("post.{$post->slug}.{$locale}");
                Cache::forget("post.related.{$post->slug}.{$locale}");
            }
        }
    }
}
