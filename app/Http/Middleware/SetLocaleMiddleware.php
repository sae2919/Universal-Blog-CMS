<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'fr', 'de', 'hi', 'te'];

        // 1. Check query parameter
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, $supportedLocales)) {
                session(['locale' => $lang]);
            }
        } elseif ($request->has('locale')) {
            $lang = $request->query('locale');
            if (in_array($lang, $supportedLocales)) {
                session(['locale' => $lang]);
            }
        }

        // 2. Set application locale from session or fallback
        $locale = session('locale', config('app.locale', 'en'));
        
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
