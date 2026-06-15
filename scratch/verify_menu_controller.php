<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menu = App\Models\Menu::firstOrCreate(['location' => 'main'], ['name' => 'Main Navigation']);

// Clear existing items
App\Models\MenuItem::where('menu_id', $menu->id)->delete();

// Build sample post data
$requestData = [
    'items' => [
        0 => [
            'title' => 'Blog',
            'url' => '#',
            'target' => '_self',
            'parent_index' => '',
            'sort_order' => 0,
        ],
        1 => [
            'title' => 'Laravel',
            'url' => '/category/technology',
            'target' => '_self',
            'parent_index' => '0',
            'sort_order' => 1,
        ],
        2 => [
            'title' => 'Python',
            'url' => '/category/programming',
            'target' => '_self',
            'parent_index' => '0',
            'sort_order' => 2,
        ],
        3 => [
            'title' => 'AI',
            'url' => '/category/it-courses',
            'target' => '_self',
            'parent_index' => '0',
            'sort_order' => 3,
        ],
        4 => [
            'title' => 'About Us',
            'url' => '/about-us',
            'target' => '_self',
            'parent_index' => '',
            'sort_order' => 4,
        ]
    ]
];

$request = Illuminate\Http\Request::create('/admin/menus/' . $menu->id . '/builder', 'POST', $requestData);

// Instantiate the controller
$controller = new App\Http\Controllers\Admin\MenuController();
$response = $controller->updateBuilder($request, $menu);

echo "Update executed. Status Code: " . $response->getStatusCode() . "\n\n";

echo "=== VERIFYING DATABASE MENU ITEMS ===\n";
$insertedItems = App\Models\MenuItem::where('menu_id', $menu->id)
    ->whereNull('parent_id')
    ->orderBy('sort_order')
    ->with('children')
    ->get();

foreach($insertedItems as $item) {
    echo "- Root item: " . $item->title . " (URL: " . $item->url . ")\n";
    if ($item->children->isNotEmpty()) {
        foreach($item->children as $child) {
            echo "  - Child item: " . $child->title . " (URL: " . $child->url . ")\n";
        }
    }
}
