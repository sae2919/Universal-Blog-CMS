<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'site_tagline', 'site_logo', 'site_favicon', 'contact_email', 'contact_phone', 'office_address',
        'facebook', 'twitter', 'linkedin', 'instagram', 'youtube',
        'posts_per_page', 'default_meta_title', 'default_meta_description',
        'google_analytics', 'google_tag_manager',
        'site_niche', 'site_accent_color', 'site_font', 'site_layout', 'default_og_image',
        'global_cta_title', 'global_cta_description', 'global_cta_button_text', 'global_cta_button_link', 'global_cta_bg_image',
        'ai_system_instruction',
    ];

    /**
     * Get a setting value by key — cached forever until manually cleared.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::first();
            $val = $setting?->{$key};
            return ($val !== null && $val !== '') ? $val : $default;
        });
    }

    /**
     * Clear all cached settings when settings are saved.
     */
    public static function clearCache(): void
    {
        $columns = (new self())->getFillable();
        foreach ($columns as $key) {
            Cache::forget("setting.{$key}");
        }
    }
}
