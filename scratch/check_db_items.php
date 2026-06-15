<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PAGES ===\n";
foreach(App\Models\Page::all() as $p) {
    echo "- " . $p->title . " (slug: " . $p->slug . ", locale: " . $p->locale . ")\n";
}

echo "\n=== CATEGORIES ===\n";
foreach(App\Models\Category::all() as $c) {
    echo "- " . $c->name . " (slug: " . $c->slug . ", locale: " . $c->locale . ")\n";
}

echo "\n=== MENUS ===\n";
foreach(App\Models\Menu::with('items.children')->get() as $m) {
    echo "- Menu: " . $m->name . " (location: " . $m->location . ")\n";
    foreach($m->items as $item) {
        echo "  - " . $item->title . " (url: " . $item->url . ")\n";
        foreach($item->children as $child) {
            echo "    * " . $child->title . " (url: " . $child->url . ")\n";
        }
    }
}
