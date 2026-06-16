<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, Sluggable, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'locale', 'title', 'slug', 'excerpt', 'content', 'faqs', 'featured_image',
        'status', 'published_at', 'views', 'is_featured', 'is_trending', 'allow_comments',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image', 'schema_type',
        'cta_title', 'cta_description', 'cta_button_text', 'cta_button_link', 'cta_bg_image',
        'cta_directory_title', 'cta_directory_subtitle',
        'cta_col1_title', 'cta_col1_links',
        'cta_col2_title', 'cta_col2_links',
        'cta_col3_title', 'cta_col3_links',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured'  => 'boolean',
            'is_trending'  => 'boolean',
            'allow_comments' => 'boolean',
            'faqs'         => 'array',
        ];
    }

    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title'],
        ];
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->whereIn('status', ['published', 'scheduled'])
                     ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->published();
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true)->published();
    }

    public function scopeForCurrentLocale($query)
    {
        return $query->where('locale', app()->getLocale());
    }

    // SEO helpers
    public function getMetaTitleAttribute($value): string
    {
        return $value ?? $this->title;
    }

    public function getOgImageAttribute($value): ?string
    {
        return $value ?? $this->featured_image;
    }
}
