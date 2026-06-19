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
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\TrashController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AiController;

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

        // Analytics Dashboard
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

        // Posts CRUD
        Route::resource('posts', PostController::class)->except(['show']);

        // Categories CRUD
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Menus & Menu Builder CRUD
        Route::get('menus/{menu}/builder', [App\Http\Controllers\Admin\MenuController::class, 'builder'])->name('menus.builder');
        Route::post('menus/{menu}/builder', [App\Http\Controllers\Admin\MenuController::class, 'updateBuilder'])->name('menus.builder.update');
        Route::resource('menus', App\Http\Controllers\Admin\MenuController::class)->except(['show']);

        // Media Library JSON/Async endpoints
        Route::get('media-json', [App\Http\Controllers\Admin\MediaController::class, 'jsonList'])->name('media.json-list');
        Route::post('media-json/upload', [App\Http\Controllers\Admin\MediaController::class, 'jsonUpload'])->name('media.json-upload');

        // SEO Diagnostics CRUD/Overview
        Route::get('seo', [App\Http\Controllers\Admin\SeoController::class, 'index'])->name('seo.index');
        Route::get('seo/audit/{type}/{id}', [App\Http\Controllers\Admin\SeoController::class, 'audit'])->name('seo.audit');

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
        Route::post('/settings/reset-analytics', [SettingController::class, 'resetAnalytics'])->name('settings.reset-analytics');

        // Trash Bin
        Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/{type}/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('trash.force-delete');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/click', [NotificationController::class, 'click'])->name('notifications.click')->whereNumber('id');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read')->whereNumber('id');
        Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy')->whereNumber('id');

        // AI Assistant
        Route::get('/ai-assistant', [AiController::class, 'dashboard'])->name('ai.dashboard');
        Route::post('/ai/generate-article', [AiController::class, 'generateArticle'])->name('ai.generate-article');
        Route::post('/ai/generate-summary', [AiController::class, 'generateSummary'])->name('ai.generate-summary');
        Route::post('/ai/generate-tags', [AiController::class, 'generateTags'])->name('ai.generate-tags');
        Route::post('/ai/generate-keywords', [AiController::class, 'generateKeywords'])->name('ai.generate-keywords');
        Route::post('/ai/generate-seo-desc', [AiController::class, 'generateSeoDesc'])->name('ai.generate-seo-desc');
        Route::post('/ai/check-grammar', [AiController::class, 'checkGrammar'])->name('ai.check-grammar');
        Route::post('/ai/correct-grammar', [AiController::class, 'correctGrammar'])->name('ai.correct-grammar');
        Route::post('/ai/generate-faqs', [AiController::class, 'generateFaqs'])->name('ai.generate-faqs');
        Route::post('/ai/generate-image', [AiController::class, 'generateImage'])->name('ai.generate-image');
        Route::post('/ai/translate-post', [AiController::class, 'translatePost'])->name('ai.translate-post');

        // Redirect to dashboard from /admin
        Route::redirect('/', '/admin/dashboard');
    });

