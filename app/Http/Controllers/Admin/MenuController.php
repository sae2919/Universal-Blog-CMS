<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('items')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'required|string|unique:menus,location|max:50',
        ]);

        Menu::create($validated);
        $this->clearMenuCaches();

        return redirect()->route('admin.menus.index')->with('success', 'Menu location created successfully!');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'required|string|unique:menus,location,' . $menu->id . '|max:50',
        ]);

        $menu->update($validated);
        $this->clearMenuCaches();

        return redirect()->route('admin.menus.index')->with('success', 'Menu location updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        $this->clearMenuCaches();

        return redirect()->route('admin.menus.index')->with('success', 'Menu location deleted successfully!');
    }

    public function builder(Menu $menu)
    {
        $menuItems = MenuItem::where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with('children')
            ->get();

        $pages = Page::published()->orderBy('title')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.menus.builder', compact('menu', 'menuItems', 'pages', 'categories'));
    }

    public function updateBuilder(Request $request, Menu $menu)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.title' => 'required|string|max:255',
            'items.*.url' => 'required|string|max:255',
            'items.*.target' => 'required|in:_self,_blank',
            'items.*.parent_index' => 'nullable|integer',
            'items.*.sort_order' => 'required|integer',
        ]);

        \DB::transaction(function () use ($menu, $request) {
            // Drop existing menu items under this menu
            MenuItem::where('menu_id', $menu->id)->delete();

            if ($request->has('items') && is_array($request->items)) {
                $insertedIds = [];

                // Sort items by sort_order
                $items = collect($request->items)->sortBy('sort_order')->toArray();

                // 1. Insert parent/root items (those that don't have parent_index)
                foreach ($items as $index => $item) {
                    if (!isset($item['parent_index']) || $item['parent_index'] === null || $item['parent_index'] === 'null' || $item['parent_index'] === '') {
                        $menuItem = MenuItem::create([
                            'menu_id' => $menu->id,
                            'parent_id' => null,
                            'title' => $item['title'],
                            'url' => $item['url'],
                            'target' => $item['target'] ?? '_self',
                            'sort_order' => $item['sort_order'] ?? 0,
                        ]);
                        $insertedIds[$index] = $menuItem->id;
                    }
                }

                // 2. Insert child items mapping to their correct parent's real ID
                foreach ($items as $index => $item) {
                    if (isset($item['parent_index']) && $item['parent_index'] !== null && $item['parent_index'] !== 'null' && $item['parent_index'] !== '') {
                        $parentKey = $item['parent_index'];
                        $realParentId = $insertedIds[$parentKey] ?? null;

                        MenuItem::create([
                            'menu_id' => $menu->id,
                            'parent_id' => $realParentId,
                            'title' => $item['title'],
                            'url' => $item['url'],
                            'target' => $item['target'] ?? '_self',
                            'sort_order' => $item['sort_order'] ?? 0,
                        ]);
                    }
                }
            }
        });

        $this->clearMenuCaches();

        return redirect()->route('admin.menus.builder', $menu->id)->with('success', 'Menu structure updated successfully!');
    }

    private function clearMenuCaches()
    {
        Cache::forget('nav.menu.main');
        Cache::forget('nav.menu.footer');
        Cache::forget('nav.categories');
    }
}
