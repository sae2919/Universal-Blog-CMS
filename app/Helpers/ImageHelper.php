<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Resolve the public storage URL or return a default placeholder.
     */
    public static function getUrl(?string $path, string $default = 'https://placehold.co/600x400?text=No+Image'): string
    {
        if ($path) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            return asset('storage/' . $path);
        }
        return $default;
    }
}
