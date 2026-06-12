<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use Artesaos\SEOTools\Facades\SEOTools;

class SeoService
{
    /**
     * Map model meta fields to Spatie SEOTools dynamically.
     */
    public function setMetaForModel($model): void
    {
        SEOTools::setTitle(SeoHelper::getTitle($model));
        SEOTools::setDescription(SeoHelper::getDescription($model));
        SEOTools::metatags()->addMeta('keywords', SeoHelper::getKeywords($model));
        
        $ogImage = SeoHelper::getOgImage($model);
        if ($ogImage) {
            SEOTools::opentargets()->addImage($ogImage);
        }
    }
}
