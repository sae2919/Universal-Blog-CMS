@extends('layouts.admin')

@section('title', 'Edit Menu Structure')
@section('header')
    Edit Menu: <span class="text-indigo-650 dark:text-indigo-405 font-bold">{{ $menu->name }}</span>
@endsection

@section('content')
@php
    $flatItems = [];
    $index = 0;
    foreach($menuItems as $item) {
        $parentIndex = $index;
        $flatItems[] = [
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
            'parent_index' => null,
            'sort_order' => $item->sort_order
        ];
        $index++;
        foreach($item->children as $child) {
            $flatItems[] = [
                'title' => $child->title,
                'url' => $child->url,
                'target' => $child->target,
                'parent_index' => $parentIndex,
                'sort_order' => $child->sort_order
            ];
            $index++;
        }
    }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="menu-builder-app">
    {{-- Left Panel: Add Items (Accordion) --}}
    <div class="space-y-4 h-fit" x-data="{ activeSection: 'pages' }">
        <h3 class="font-bold text-gray-800 dark:text-slate-200 text-lg border-b border-gray-100 dark:border-slate-700 pb-3">Add Menu Items</h3>
        
        {{-- Section 1: Pages --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <button type="button" @click="activeSection = activeSection === 'pages' ? '' : 'pages'"
                    class="w-full flex justify-between items-center px-5 py-3 font-bold text-sm text-gray-850 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-750 transition-colors">
                <span class="flex items-center gap-2"> {{ __('Pages') }}</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': activeSection === 'pages' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="activeSection === 'pages'" x-transition class="p-5 border-t border-gray-100 dark:border-slate-700 space-y-4">
                @if($pages->isNotEmpty())
                    <label class="flex items-center gap-2 text-xs font-bold text-indigo-650 dark:text-indigo-400 cursor-pointer">
                        <input type="checkbox" id="page-select-all" onchange="toggleSelectAll('page-select-all', '.page-checkbox')" class="rounded border-gray-300 text-indigo-650 focus:ring-indigo-500">
                        <span>Select All Pages</span>
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-150 dark:border-slate-700 rounded-lg p-3 space-y-2 bg-gray-50/50 dark:bg-slate-900/40">
                        @foreach($pages as $page)
                            <label class="flex items-center gap-2 text-sm text-gray-750 dark:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="selected_pages[]" value="{{ $page->slug }}" data-title="{{ $page->title }}" class="rounded border-gray-300 text-indigo-655 focus:ring-indigo-550 page-checkbox">
                                <span class="truncate">{{ $page->title }}</span>
                                <span class="text-[9px] px-1 py-0.5 rounded bg-gray-100 dark:bg-slate-750 text-gray-500 font-mono flex-shrink-0">{{ strtoupper($page->locale) }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase">Open Target</label>
                        <select id="page-target" class="w-full px-3 py-2 border border-gray-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-sm">
                            <option value="_self">Same Window (_self)</option>
                            <option value="_blank">New Tab (_blank)</option>
                        </select>
                    </div>
                    <button type="button" onclick="addCheckedPages()"
                            class="w-full py-2 bg-indigo-650 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors">
                        Add Pages to Menu
                    </button>
                @else
                    <p class="text-xs text-gray-400">No published pages found.</p>
                @endif
            </div>
        </div>

        {{-- Section 2: Categories --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <button type="button" @click="activeSection = activeSection === 'categories' ? '' : 'categories'"
                    class="w-full flex justify-between items-center px-5 py-3 font-bold text-sm text-gray-850 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-750 transition-colors">
                <span class="flex items-center gap-2"> {{ __('Categories') }}</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': activeSection === 'categories' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="activeSection === 'categories'" x-transition class="p-5 border-t border-gray-100 dark:border-slate-700 space-y-4">
                @if($categories->isNotEmpty())
                    <label class="flex items-center gap-2 text-xs font-bold text-indigo-650 dark:text-indigo-400 cursor-pointer">
                        <input type="checkbox" id="category-select-all" onchange="toggleSelectAll('category-select-all', '.category-checkbox')" class="rounded border-gray-300 text-indigo-650 focus:ring-indigo-500">
                        <span>Select All Categories</span>
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-150 dark:border-slate-700 rounded-lg p-3 space-y-2 bg-gray-50/50 dark:bg-slate-900/40">
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2 text-sm text-gray-755 dark:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="selected_categories[]" value="{{ $cat->slug }}" data-title="{{ $cat->name }}" class="rounded border-gray-300 text-indigo-655 focus:ring-indigo-550 category-checkbox">
                                <span class="truncate">{{ $cat->name }}</span>
                                <span class="text-[9px] px-1 py-0.5 rounded bg-gray-100 dark:bg-slate-750 text-gray-500 font-mono flex-shrink-0">{{ strtoupper($cat->locale) }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase">Open Target</label>
                        <select id="category-target" class="w-full px-3 py-2 border border-gray-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-sm">
                            <option value="_self">Same Window (_self)</option>
                            <option value="_blank">New Tab (_blank)</option>
                        </select>
                    </div>
                    <button type="button" onclick="addCheckedCategories()"
                            class="w-full py-2 bg-indigo-655 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors">
                        Add Categories to Menu
                    </button>
                @else
                    <p class="text-xs text-gray-400">No active categories found.</p>
                @endif
            </div>
        </div>

        {{-- Section 3: Custom Links --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm overflow-hidden">
            <button type="button" @click="activeSection = activeSection === 'custom' ? '' : 'custom'"
                    class="w-full flex justify-between items-center px-5 py-3 font-bold text-sm text-gray-850 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-755 transition-colors">
                <span class="flex items-center gap-2">🔗 Custom Links / Dropdowns</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': activeSection === 'custom' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="activeSection === 'custom'" x-transition class="p-5 border-t border-gray-100 dark:border-slate-700 space-y-4">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Link Label</label>
                    <input type="text" id="add-title" placeholder="e.g. Services"
                           class="w-full px-3 py-2 border border-gray-250 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-sm">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Link URL</label>
                    <input type="text" id="add-url" placeholder="e.g. /services or https://google.com"
                           class="w-full px-3 py-2 border border-gray-250 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-sm">
                    <p class="text-[10px] text-gray-400">Use "#" to create a non-clickable Dropdown Menu header.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Open Target</label>
                    <select id="add-target" class="w-full px-3 py-2 border border-gray-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-sm">
                        <option value="_self">Same Window (_self)</option>
                        <option value="_blank">New Tab (_blank)</option>
                    </select>
                </div>

                <button type="button" onclick="addMenuItem()"
                        class="w-full py-2 bg-indigo-650 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors">
                    Add Custom Link
                </button>
            </div>
        </div>
    </div>

    {{-- Right Panel: Menu Structure --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
            <div>
                <h3 class="font-bold text-gray-800 dark:text-slate-200 text-lg">Menu Structure</h3>
                <p class="text-xs text-gray-400 mt-1">Reorder items, delete them, or nest them to create submenu dropdowns.</p>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-slate-350">
                Cancel
            </a>
        </div>

        <form action="{{ route('admin.menus.builder.update', $menu->id) }}" method="POST" id="builder-form">
            @csrf
            
            {{-- Hidden input list populated dynamically --}}
            <div id="hidden-inputs-container"></div>

            {{-- Visual list structure --}}
            <div id="menu-items-list" class="space-y-3 min-h-64">
                {{-- Loaded dynamically by JS --}}
            </div>

            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-700 flex justify-end">
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md transition-colors">
                    Save Menu Structure
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Initial data from controller
    let items = @json($flatItems);

    function renderList() {
        const listContainer = document.getElementById('menu-items-list');
        const inputsContainer = document.getElementById('hidden-inputs-container');
        listContainer.innerHTML = '';
        inputsContainer.innerHTML = '';

        if (items.length === 0) {
            listContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-250 dark:border-slate-750 rounded-xl h-48 text-gray-400 text-sm">
                    <span>Menu is empty. Add links using the panel on the left!</span>
                </div>
            `;
            return;
        }

        items.forEach((item, index) => {
            // Check if nested child (has parent_index)
            const isChild = item.parent_index !== null;

            // Generate HTML for the row item
            const row = document.createElement('div');
            row.className = `flex items-center justify-between bg-gray-50 dark:bg-slate-700/30 border border-gray-200 dark:border-slate-700 rounded-xl p-4 transition-all ${isChild ? 'ml-12 border-l-4 border-l-indigo-500 bg-indigo-50/10' : ''}`;

            row.innerHTML = `
                <div class="flex items-center gap-3 min-w-0">
                    <span class="text-gray-400 select-none flex-shrink-0">${isChild ? '↳' : '☰'}</span>
                    <div class="min-w-0">
                        <span class="font-bold text-gray-900 dark:text-white truncate text-sm block">${item.title}</span>
                        <span class="text-xs text-gray-400 font-mono block truncate">${item.url}</span>
                    </div>
                    ${item.target === '_blank' ? '<span class="px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold">New Tab</span>' : ''}
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <!-- Up / Down Arrow buttons -->
                    <button type="button" onclick="moveItem(${index}, -1)" class="p-1 hover:bg-gray-200 dark:hover:bg-slate-750 rounded text-gray-500 dark:text-slate-350" title="Move Up">▲</button>
                    <button type="button" onclick="moveItem(${index}, 1)" class="p-1 hover:bg-gray-200 dark:hover:bg-slate-750 rounded text-gray-500 dark:text-slate-350" title="Move Down">▼</button>
                    
                    <!-- Indent / Outdent Nesting buttons -->
                    ${!isChild ? `
                        <button type="button" onclick="indentItem(${index})" class="px-2 py-1 hover:bg-indigo-100 dark:hover:bg-slate-750 rounded text-xs text-indigo-600 font-bold" title="Nest Menu Item">→ Nest</button>
                    ` : `
                        <button type="button" onclick="outdentItem(${index})" class="px-2 py-1 hover:bg-gray-200 dark:hover:bg-slate-750 rounded text-xs text-gray-600 font-bold" title="Make Root Item">← Root</button>
                    `}

                    <!-- Delete button -->
                    <button type="button" onclick="deleteItem(${index})" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/30 rounded text-red-500" title="Remove Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;

            listContainer.appendChild(row);

            // Generate inputs for form serialization
            inputsContainer.innerHTML += `
                <input type="hidden" name="items[${index}][title]" value="${escapeHtml(item.title)}">
                <input type="hidden" name="items[${index}][url]" value="${escapeHtml(item.url)}">
                <input type="hidden" name="items[${index}][target]" value="${item.target}">
                <input type="hidden" name="items[${index}][parent_index]" value="${item.parent_index !== null ? item.parent_index : ''}">
                <input type="hidden" name="items[${index}][sort_order]" value="${index}">
            `;
        });
    }

    function toggleSelectAll(selectAllId, checkboxClass) {
        const selectAll = document.getElementById(selectAllId);
        const checkboxes = document.querySelectorAll(checkboxClass);
        checkboxes.forEach(cb => {
            cb.checked = selectAll.checked;
        });
    }

    function addCheckedPages() {
        const checkboxes = document.querySelectorAll('.page-checkbox:checked');
        const target = document.getElementById('page-target').value;
        
        if (checkboxes.length === 0) {
            alert('Please select at least one page to add.');
            return;
        }

        checkboxes.forEach(cb => {
            const title = cb.getAttribute('data-title');
            const slug = cb.value;
            const url = '/' + slug;

            items.push({
                title: title,
                url: url,
                target: target,
                parent_index: null,
                sort_order: items.length
            });

            cb.checked = false;
        });

        const selectAll = document.getElementById('page-select-all');
        if (selectAll) selectAll.checked = false;

        renderList();
    }

    function addCheckedCategories() {
        const checkboxes = document.querySelectorAll('.category-checkbox:checked');
        const target = document.getElementById('category-target').value;

        if (checkboxes.length === 0) {
            alert('Please select at least one category to add.');
            return;
        }

        checkboxes.forEach(cb => {
            const title = cb.getAttribute('data-title');
            const slug = cb.value;
            const url = '/category/' + slug;

            items.push({
                title: title,
                url: url,
                target: target,
                parent_index: null,
                sort_order: items.length
            });

            cb.checked = false;
        });

        const selectAll = document.getElementById('category-select-all');
        if (selectAll) selectAll.checked = false;

        renderList();
    }

    function addMenuItem() {
        const titleEl = document.getElementById('add-title');
        const urlEl = document.getElementById('add-url');
        const targetEl = document.getElementById('add-target');

        const title = titleEl.value.trim();
        const url = urlEl.value.trim();
        const target = targetEl.value;

        if (!title || !url) {
            alert('Please enter both a link label and link URL.');
            return;
        }

        // Add item as a root item at the end
        items.push({
            title: title,
            url: url,
            target: target,
            parent_index: null,
            sort_order: items.length
        });

        // Clear inputs
        titleEl.value = '';
        urlEl.value = '';
        targetEl.value = '_self';

        renderList();
    }

    function deleteItem(index) {
        const parentOfDeleted = items[index].parent_index;

        // If it's a root item, we must adjust the parent_index of its children to null
        if (parentOfDeleted === null) {
            items.forEach(item => {
                if (item.parent_index === index) {
                    item.parent_index = null;
                } else if (item.parent_index > index) {
                    // Adjust indices because we are shifting indices in the array
                    item.parent_index--;
                }
            });
        } else {
            // Adjust indices for general shift
            items.forEach(item => {
                if (item.parent_index > index) {
                    item.parent_index--;
                }
            });
        }

        items.splice(index, 1);
        renderList();
    }

    function moveItem(index, direction) {
        const targetIndex = index + direction;
        if (targetIndex < 0 || targetIndex >= items.length) return;

        // Perform swap
        const temp = items[index];
        items[index] = items[targetIndex];
        items[targetIndex] = temp;

        // Re-align parent references
        items.forEach(item => {
            if (item.parent_index === index) {
                item.parent_index = targetIndex;
            } else if (item.parent_index === targetIndex) {
                item.parent_index = index;
            }
        });

        renderList();
    }

    function indentItem(index) {
        // To indent, find the closest preceding root item (parent_index is null)
        let closestPrecedingRootIndex = -1;
        for (let i = index - 1; i >= 0; i--) {
            if (items[i].parent_index === null) {
                closestPrecedingRootIndex = i;
                break;
            }
        }

        if (closestPrecedingRootIndex !== -1) {
            items[index].parent_index = closestPrecedingRootIndex;
            renderList();
        } else {
            alert('Cannot nest this item because there is no preceding main menu item.');
        }
    }

    function outdentItem(index) {
        items[index].parent_index = null;
        renderList();
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', renderList);
</script>
@endsection
