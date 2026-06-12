<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\SitemapController;

/*
|--------------------------------------------------------------------------
| Public Website Routes (SEO Friendly URLs)
|--------------------------------------------------------------------------
*/

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Blog listing
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

// Search
Route::get('/search', [App\Http\Controllers\Frontend\SearchController::class, 'search'])->name('blog.search');
Route::get('/search/autocomplete', [App\Http\Controllers\Frontend\SearchController::class, 'autocomplete'])->name('blog.search.autocomplete');

// Category pages  (e.g. /category/technology)
Route::get('/category/{slug}', [App\Http\Controllers\Frontend\CategoryController::class, 'category'])->name('blog.category');

// Tag pages  (e.g. /tag/laravel)
Route::get('/tag/{slug}', [App\Http\Controllers\Frontend\TagController::class, 'tag'])->name('blog.tag');

// Authentication Routes (Laravel Breeze)
require __DIR__.'/auth.php';

// Redirect default Breeze /dashboard to /admin/dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Single blog post (category-prefixed URL)
Route::get('/{categorySlug}/{postSlug}', [BlogController::class, 'showPost'])
    ->name('blog.show')
    ->where([
        'categorySlug' => '^(?!admin|login|register|logout|sitemap\.xml|blog|category|tag|search).*$',
        'postSlug' => '^[a-zA-Z0-9-_]+$'
    ]);

// Comment submission (rate-limited: 5 attempts per minute)
Route::post('/{categorySlug}/{postSlug}/comment', [BlogController::class, 'storeComment'])
    ->middleware('throttle:5,1')
    ->name('blog.comment.store')
    ->where([
        'categorySlug' => '^(?!admin|login|register|logout|sitemap\.xml|blog|category|tag|search).*$',
        'postSlug' => '^[a-zA-Z0-9-_]+$'
    ]);

// Single static page (Catch-all - MUST be at the bottom)
Route::get('/{slug}', [BlogController::class, 'showPage'])
    ->name('page.show')
    ->where('slug', '^(?!admin|login|register|logout|sitemap\.xml|blog|category|tag|search).*$');
