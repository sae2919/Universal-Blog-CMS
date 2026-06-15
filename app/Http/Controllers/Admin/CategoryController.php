<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('sort_order')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'locale'      => 'required|in:en,fr,de,hi,te',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'integer',
            'status'      => 'required|in:active,inactive',
            'accent_color'=> 'nullable|string|max:7',
            'icon_emoji'  => 'nullable|string|max:10',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category created!');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'locale'      => 'required|in:en,fr,de,hi,te',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'integer',
            'status'      => 'required|in:active,inactive',
            'accent_color'=> 'nullable|string|max:7',
            'icon_emoji'  => 'nullable|string|max:10',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted!');
    }
}
