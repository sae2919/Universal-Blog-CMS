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

            $locale = app()->getLocale();
            $headerPages = \Illuminate\Support\Facades\Cache::remember("nav.pages.header.{$locale}", now()->addHours(6), function () use ($locale) {
                return \App\Models\Page::published()
                    ->where('locale', $locale)
                    ->where('show_in_header', true)
                    ->get();
            });

            $view->with('mainMenu', $mainMenu);
            $view->with('headerPages', $headerPages);
        });

        view()->composer('frontend.partials.footer', function ($view) {
            $footerMenu = \Illuminate\Support\Facades\Cache::remember('nav.menu.footer', now()->addHours(6), function () {
                return \App\Models\Menu::where('location', 'footer')->with('items.children')->first();
            });

            $locale = app()->getLocale();
            $footerPages = \Illuminate\Support\Facades\Cache::remember("nav.pages.footer.{$locale}", now()->addHours(6), function () use ($locale) {
                return \App\Models\Page::published()
                    ->where('locale', $locale)
                    ->where('show_in_footer', true)
                    ->get();
            });

            $view->with('footerMenu', $footerMenu);
            $view->with('footerPages', $footerPages);
        });
    }
}
