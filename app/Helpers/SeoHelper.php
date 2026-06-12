<?php

namespace App\Helpers;

use App\Models\Setting;

class SeoHelper
{
    public static function getTitle($model = null): string
    {
        if ($model) {
            return $model->meta_title ?? $model->title ?? $model->name ?? self::getDefaultTitle();
        }
        return self::getDefaultTitle();
    }

    public static function getDescription($model = null): string
    {
        if ($model) {
            return $model->meta_description ?? $model->excerpt ?? self::getDefaultDescription();
        }
        return self::getDefaultDescription();
    }

    public static function getKeywords($model = null): string
    {
        if ($model) {
            return $model->meta_keywords ?? '';
        }
        return '';
    }

    public static function getOgImage($model = null): string
    {
        $image = null;
        if ($model) {
            $image = $model->og_image ?? $model->featured_image;
        }
        $defaultImage = Setting::getValue('default_og_image');
        return $image ? asset('storage/' . $image) : ($defaultImage ? asset($defaultImage) : '');
    }

    private static function getDefaultTitle(): string
    {
        return Setting::getValue('default_meta_title', Setting::getValue('site_name', config('app.name')));
    }

    private static function getDefaultDescription(): string
    {
        return Setting::getValue('default_meta_description', '');
    }
}
