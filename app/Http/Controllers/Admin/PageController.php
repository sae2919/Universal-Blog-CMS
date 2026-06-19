<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'locale'           => 'required|in:en,fr,de,hi,te',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|image|max:2048',
            'status'           => 'required|in:draft,published',
            'show_in_header'   => 'boolean',
            'show_in_footer'   => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'image_metadata'   => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = uniqid() . '.webp';
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file)
                ->cover(1200, 500)
                ->toWebp(75);
            \Storage::disk('public')->put('pages/' . $filename, $image);
            $validated['featured_image'] = 'pages/' . $filename;
        } elseif ($request->filled('generated_image_path')) {
            $validated['featured_image'] = $request->input('generated_image_path');
        }

        $validated['show_in_header'] = $request->boolean('show_in_header');
        $validated['show_in_footer'] = $request->boolean('show_in_footer');
        if ($request->has('image_metadata')) {
            $meta = $request->input('image_metadata');
            $validated['image_metadata'] = is_string($meta) ? json_decode($meta, true) : null;
        }

        Page::create($validated);

        Cache::forget('frontend_pages');
        foreach (['en', 'fr', 'de', 'hi', 'te'] as $l) {
            Cache::forget("nav.pages.footer.{$l}");
            Cache::forget("nav.pages.header.{$l}");
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'locale'           => 'required|in:en,fr,de,hi,te',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|image|max:2048',
            'status'           => 'required|in:draft,published',
            'show_in_header'   => 'boolean',
            'show_in_footer'   => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'image_metadata'   => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($page->featured_image && \Storage::disk('public')->exists($page->featured_image)) {
                \Storage::disk('public')->delete($page->featured_image);
            }
            $file = $request->file('featured_image');
            $filename = uniqid() . '.webp';
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file)
                ->cover(1200, 500)
                ->toWebp(75);
            \Storage::disk('public')->put('pages/' . $filename, $image);
            $validated['featured_image'] = 'pages/' . $filename;
        } elseif ($request->filled('generated_image_path')) {
            if ($page->featured_image && \Storage::disk('public')->exists($page->featured_image)) {
                \Storage::disk('public')->delete($page->featured_image);
            }
            $validated['featured_image'] = $request->input('generated_image_path');
        } elseif ($request->boolean('remove_featured_image')) {
            if ($page->featured_image && \Storage::disk('public')->exists($page->featured_image)) {
                \Storage::disk('public')->delete($page->featured_image);
            }
            $validated['featured_image'] = null;
        }

        $validated['show_in_header'] = $request->boolean('show_in_header');
        $validated['show_in_footer'] = $request->boolean('show_in_footer');
        if ($request->has('image_metadata')) {
            $meta = $request->input('image_metadata');
            $validated['image_metadata'] = is_string($meta) ? json_decode($meta, true) : null;
        }

        $page->update($validated);

        Cache::forget('frontend_pages');
        foreach (['en', 'fr', 'de', 'hi', 'te'] as $l) {
            Cache::forget("page.{$page->slug}.{$l}");
            Cache::forget("nav.pages.footer.{$l}");
            Cache::forget("nav.pages.header.{$l}");
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully!');
    }

    public function destroy(Page $page)
    {
        Cache::forget('frontend_pages');
        foreach (['en', 'fr', 'de', 'hi', 'te'] as $l) {
            Cache::forget("page.{$page->slug}.{$l}");
            Cache::forget("nav.pages.footer.{$l}");
            Cache::forget("nav.pages.header.{$l}");
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully!');
    }
}
