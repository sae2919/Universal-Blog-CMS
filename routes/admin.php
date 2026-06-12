<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes — Protected by auth + admin middleware
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

        // Posts CRUD
        Route::resource('posts', PostController::class)->except(['show']);

        // Categories CRUD
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Menus & Menu Builder CRUD
        Route::get('menus/{menu}/builder', [App\Http\Controllers\Admin\MenuController::class, 'builder'])->name('menus.builder');
        Route::post('menus/{menu}/builder', [App\Http\Controllers\Admin\MenuController::class, 'updateBuilder'])->name('menus.builder.update');
        Route::resource('menus', App\Http\Controllers\Admin\MenuController::class)->except(['show']);

        // Media Library CRUD
        Route::resource('media', App\Http\Controllers\Admin\MediaController::class)->except(['show', 'edit', 'update']);

        // SEO Diagnostics CRUD/Overview
        Route::get('seo', [App\Http\Controllers\Admin\SeoController::class, 'index'])->name('seo.index');

        // Tags CRUD
        Route::resource('tags', TagController::class)->except(['show']);

        // Pages CRUD
        Route::resource('pages', PageController::class)->except(['show']);

        // Users CRUD
        Route::resource('users', UserController::class)->except(['show']);

        // Comments moderation
        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Redirect to dashboard from /admin
        Route::redirect('/', '/admin/dashboard');
    });

