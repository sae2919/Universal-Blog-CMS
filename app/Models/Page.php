<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, Sluggable, SoftDeletes;

    protected static function booted()
    {
        static::saving(function ($model) {
            $rawContent = $model->getAttributes()['content'] ?? '';
            if ($model->image_metadata && is_array($model->image_metadata)) {
                $model->image_metadata = app(\App\Services\ImageMetadataService::class)->optimizeMetadata($model->image_metadata, $rawContent, $model->featured_image);
            }
            $model->attributes['content'] = app(\App\Services\ImageMetadataService::class)->stripSrc($rawContent, $model->image_metadata ?? []);
        });
    }

    protected $fillable = [
        'title', 'slug', 'locale', 'content', 'featured_image',
        'meta_title', 'meta_description', 'meta_keywords', 'status',
        'show_in_header', 'show_in_footer', 'image_metadata',
    ];

    protected function casts(): array
    {
        return [
            'show_in_header' => 'boolean',
            'show_in_footer' => 'boolean',
            'image_metadata' => 'array',
        ];
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'       => 'title',
                'unique'       => true,
                'uniqueSuffix' => null,
                'onUpdate'     => false,
                'separator'    => '-',
            ],
        ];
    }

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForCurrentLocale($query)
    {
        return $query->where('locale', app()->getLocale());
    }

    public function getOptimizedContentAttribute(): string
    {
        return app(\App\Services\ImageMetadataService::class)->enhanceContent($this->getRawOriginal('content') ?? '', $this->image_metadata ?? []);
    }

    public function getContentAttribute($value)
    {
        return app(\App\Services\ImageMetadataService::class)->restoreSrc($value ?? '', $this->image_metadata ?? []);
    }
}
