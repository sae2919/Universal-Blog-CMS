<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisits
{
    public function handle(Request $request, Closure $next): Response
    {
        // Don't log admin panel routes or debug routes
        if (!$request->is('admin*') && !$request->is('up')) {
            try {
                $userAgent = $request->userAgent() ?? '';
                
                // Simple device detection
                $device = 'desktop';
                if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
                    $device = 'tablet';
                } elseif (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
                    $device = 'mobile';
                }

                // Simple browser detection
                $browser = 'Unknown';
                if (preg_match('/chrome/i', $userAgent)) {
                    $browser = 'Chrome';
                } elseif (preg_match('/firefox/i', $userAgent)) {
                    $browser = 'Firefox';
                } elseif (preg_match('/safari/i', $userAgent)) {
                    $browser = 'Safari';
                } elseif (preg_match('/msie/i', $userAgent) || preg_match('/trident/i', $userAgent)) {
                    $browser = 'Internet Explorer';
                }

                Visit::create([
                    'url'        => $request->getRequestUri(),
                    'ip_address' => $request->ip(),
                    'country'    => 'Local',
                    'device'     => $device,
                    'browser'    => $browser,
                    'visited_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Fail silently to prevent site crash if logging fails
            }
        }

        return $next($request);
    }
}
