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

    /**
     * Run SEO diagnostics on a post and create/update notifications.
     */
    public function runSeoAuditAndNotify(\App\Models\Post $post): void
    {
        $warnings = [];

        // 1. Meta Title Check
        $title = $post->getRawOriginal('meta_title') ?? $post->title;
        $titleLength = mb_strlen($title);
        if (empty($title)) {
            $warnings[] = 'No meta title is set. A fallback title is used.';
        } elseif ($titleLength > 60) {
            $warnings[] = "Meta title is too long ({$titleLength} characters). Keep it under 60 characters for optimal Google search visibility.";
        }

        // 2. Meta Description Check
        $desc = $post->getRawOriginal('meta_description') ?? '';
        $descLength = mb_strlen($desc);
        if (empty($desc)) {
            $warnings[] = 'No meta description is set. Search engines will auto-generate text from the body, which might look unprofessional.';
        } elseif ($descLength < 120 || $descLength > 160) {
            $warnings[] = "Meta description length is suboptimal ({$descLength} characters). Recommended length is 120-160 characters for best display on search results.";
        }

        // 3. Alt Text Check
        $content = $post->content ?? '';
        preg_match_all('/<img[^>]+>/i', $content, $matches);
        $imgCount = count($matches[0]);
        if ($imgCount > 0) {
            $altCount = 0;
            foreach ($matches[0] as $img) {
                if (preg_match('/alt=["\']([^"\']*)["\']/i', $img, $altMatch)) {
                    if (!empty(trim($altMatch[1]))) {
                        $altCount++;
                    }
                }
            }
            if ($altCount < $imgCount) {
                $missing = $imgCount - $altCount;
                $warnings[] = "Missing alt tags on {$missing} of {$imgCount} image(s) in the content. Alt text helps search engines read your images and improves accessibility.";
            }
        }

        // 4. Internal Links Check
        preg_match_all('/href=["\']([^"\']+)["\']/i', $content, $matches);
        $hasInternal = false;
        $appUrl = config('app.url');
        foreach ($matches[1] as $link) {
            if (str_starts_with($link, '/') && !str_starts_with($link, '//')) {
                $hasInternal = true;
                break;
            }
            if (str_starts_with($link, $appUrl)) {
                $hasInternal = true;
                break;
            }
        }
        $text = strip_tags($content);
        $wordCount = str_word_count($text);
        if (!$hasInternal && $wordCount >= 150) {
            $warnings[] = 'No internal links found. Adding links to other posts, categories, or pages on your website boosts SEO health.';
        }

        // 5. Readability Check
        if ($wordCount >= 150) {
            $hasHeadings = preg_match('/<h[2-4][^>]*>/i', $content);
            if (!$hasHeadings) {
                $warnings[] = 'Missing heading tags (H2-H4). Use subheadings to make scanning easier.';
            }

            preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $pMatches);
            $tooLongParagraphs = 0;
            foreach ($pMatches[1] as $pText) {
                $pWordCount = str_word_count(strip_tags($pText));
                if ($pWordCount > 100) {
                    $tooLongParagraphs++;
                }
            }
            if ($tooLongParagraphs > 0) {
                $warnings[] = "{$tooLongParagraphs} paragraph(s) are too long (over 100 words). Break them up to improve reading flow.";
            }
        }

        // 6. Keyword Density Check
        $keywords = $post->tags->pluck('name')->toArray();
        if (empty($keywords) && !empty($post->meta_keywords)) {
            $keywords = array_filter(array_map('trim', explode(',', $post->meta_keywords)));
        }

        if (empty($keywords)) {
            $warnings[] = 'No tags or meta keywords defined. Keyword density calculations could not be run.';
        } else {
            $textLower = strtolower(strip_tags($content));
            if ($wordCount > 0) {
                $densityWarnings = [];
                foreach ($keywords as $keyword) {
                    $keywordLower = strtolower($keyword);
                    $count = substr_count($textLower, $keywordLower);
                    $density = ($count * 100) / $wordCount;
                    if ($density < 1.0 || $density > 2.5) {
                        $densityFormatted = number_format($density, 1);
                        $densityWarnings[] = "'{$keyword}' (density: {$densityFormatted}%)";
                    }
                }
                if (!empty($densityWarnings)) {
                    $warnings[] = 'Keyword density is outside the optimal range (1.0% to 2.5%) for: ' . implode(', ', $densityWarnings) . '.';
                }
            }
        }

        $editLink = '/admin/posts/' . $post->id . '/edit';

        if (!empty($warnings)) {
            $message = "Please review the following SEO diagnostics for your post \"{$post->title}\":\n\n" . implode("\n", array_map(fn($w) => "• {$w}", $warnings));

            $notification = \App\Models\Notification::where('link', $editLink)
                ->where('type', 'seo')
                ->first();

            if ($notification) {
                $notification->update([
                    'title' => "SEO Improvement Suggestions: {$post->title}",
                    'message' => $message,
                    'read_at' => null,
                ]);
            } else {
                \App\Models\Notification::create([
                    'title' => "SEO Improvement Suggestions: {$post->title}",
                    'message' => $message,
                    'type' => 'seo',
                    'link' => $editLink,
                ]);
            }
        } else {
            \App\Models\Notification::where('link', $editLink)
                ->where('type', 'seo')
                ->delete();
        }
    }
}
