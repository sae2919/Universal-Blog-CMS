<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share menus dynamically in frontend layout
        view()->composer('frontend.partials.header', function ($view) {
            $mainMenu = \Illuminate\Support\Facades\Cache::remember('nav.menu.main', now()->addHours(6), function () {
                return \App\Models\Menu::where('location', 'main')->with('items.children')->first();
            });
            $view->with('mainMenu', $mainMenu);
        });

        view()->composer('frontend.partials.footer', function ($view) {
            $footerMenu = \Illuminate\Support\Facades\Cache::remember('nav.menu.footer', now()->addHours(6), function () {
                return \App\Models\Menu::where('location', 'footer')->with('items.children')->first();
            });
            $view->with('footerMenu', $footerMenu);
        });
    }
}
