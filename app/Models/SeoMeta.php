<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $fillable = [
        'model_type', 'model_id',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image',
        'twitter_title', 'twitter_description', 'twitter_image', 'twitter_card',
        'schema_json', 'robots',
    ];

    protected $casts = [
        'schema_json' => 'array',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
