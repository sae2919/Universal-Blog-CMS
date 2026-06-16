<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Page;

class SeoController extends Controller
{
    /**
     * Display general SEO settings and diagnostic status.
     */
    public function index()
    {
        $posts = Post::published()->latest()->take(10)->get();
        $pages = Page::published()->latest()->take(10)->get();

        // Retrieve all published items for full diagnostics
        $allPublishedPosts = Post::published()->with('tags')->get();
        $allPublishedPages = Page::published()->get();
        $totalItems = $allPublishedPosts->count() + $allPublishedPages->count();

        // 1. Meta Title Check
        $metaTitlePassedCount = 0;
        $allPublishedItems = $allPublishedPosts->concat($allPublishedPages);
        
        foreach ($allPublishedItems as $item) {
            $title = $item->getRawOriginal('meta_title') ?? $item->title;
            if (!empty($title) && mb_strlen($title) <= 60) {
                $metaTitlePassedCount++;
            }
        }
        $metaTitleScore = $totalItems > 0 ? round(($metaTitlePassedCount / $totalItems) * 100) : 100;

        // 2. Meta Description Check
        $metaDescScoreSum = 0;
        foreach ($allPublishedItems as $item) {
            $desc = $item->getRawOriginal('meta_description') ?? '';
            $descLength = mb_strlen($desc);
            if ($descLength >= 120 && $descLength <= 160) {
                $metaDescScoreSum += 100;
            } elseif ($descLength > 0) {
                $metaDescScoreSum += 60; // suboptimal length but present
            } else {
                $metaDescScoreSum += 0;
            }
        }
        $metaDescScore = $totalItems > 0 ? round($metaDescScoreSum / $totalItems) : 100;

        // 3. Alt Text Check
        $altTextScoreSum = 0;
        $itemsWithImagesCount = 0;
        foreach ($allPublishedPosts as $post) {
            $content = $post->content;
            preg_match_all('/<img[^>]+>/i', $content, $matches);
            $imgCount = count($matches[0]);
            if ($imgCount > 0) {
                $itemsWithImagesCount++;
                $altCount = 0;
                foreach ($matches[0] as $img) {
                    if (preg_match('/alt=["\']([^"\']*)["\']/i', $img, $altMatch)) {
                        if (!empty(trim($altMatch[1]))) {
                            $altCount++;
                        }
                    }
                }
                $altTextScoreSum += ($altCount / $imgCount) * 100;
            }
        }
        $altTextScore = $itemsWithImagesCount > 0 ? round($altTextScoreSum / $itemsWithImagesCount) : 100;

        // 4. Internal Links Check
        $postsWithInternalLinks = 0;
        $appUrl = config('app.url');
        foreach ($allPublishedPosts as $post) {
            $content = $post->content;
            preg_match_all('/href=["\']([^"\']+)["\']/i', $content, $matches);
            $hasInternal = false;
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
            if ($hasInternal) {
                $postsWithInternalLinks++;
            }
        }
        $internalLinksScore = $allPublishedPosts->count() > 0 ? round(($postsWithInternalLinks / $allPublishedPosts->count()) * 100) : 100;

        // 5. Readability Check
        $readabilityScoreSum = 0;
        foreach ($allPublishedPosts as $post) {
            $text = strip_tags($post->content);
            $wordCount = str_word_count($text);
            if ($wordCount < 150) {
                $readabilityScoreSum += 100; // too short to penalize
            } else {
                $postScore = 0;
                // Has headings
                if (preg_match('/<h[2-4][^>]*>/i', $post->content)) {
                    $postScore += 50;
                }
                // Check paragraph word counts
                preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $post->content, $pMatches);
                $tooLongParagraphs = 0;
                foreach ($pMatches[1] as $pText) {
                    $pWordCount = str_word_count(strip_tags($pText));
                    if ($pWordCount > 100) {
                        $tooLongParagraphs++;
                    }
                }
                if ($tooLongParagraphs === 0) {
                    $postScore += 50;
                } elseif ($tooLongParagraphs === 1) {
                    $postScore += 30;
                } else {
                    $postScore += 10;
                }
                $readabilityScoreSum += $postScore;
            }
        }
        $readabilityScore = $allPublishedPosts->count() > 0 ? round($readabilityScoreSum / $allPublishedPosts->count()) : 100;

        // 6. Keyword Density Check
        $keywordScoreSum = 0;
        $postsWithKeywordsCount = 0;
        foreach ($allPublishedPosts as $post) {
            $tags = $post->tags;
            if ($tags->count() > 0) {
                $postsWithKeywordsCount++;
                $text = strtolower(strip_tags($post->content));
                $wordCount = str_word_count($text);
                if ($wordCount > 0) {
                    $tagScores = [];
                    foreach ($tags as $tag) {
                        $keyword = strtolower($tag->name);
                        $count = substr_count($text, $keyword);
                        $density = ($count * 100) / $wordCount;
                        if ($density >= 1.0 && $density <= 2.5) {
                            $tagScores[] = 100;
                        } elseif ($density >= 0.5 && $density <= 3.5) {
                            $tagScores[] = 70;
                        } elseif ($density > 0) {
                            $tagScores[] = 40;
                        } else {
                            $tagScores[] = 0;
                        }
                    }
                    $keywordScoreSum += count($tagScores) > 0 ? (array_sum($tagScores) / count($tagScores)) : 100;
                } else {
                    $keywordScoreSum += 100;
                }
            }
        }
        $keywordDensityScore = $postsWithKeywordsCount > 0 ? round($keywordScoreSum / $postsWithKeywordsCount) : 100;

        // Overall Score (average of all 6)
        $overallScore = round(($metaTitleScore + $metaDescScore + $altTextScore + $internalLinksScore + $readabilityScore + $keywordDensityScore) / 6);

        // Health details
        if ($overallScore >= 80) {
            $healthStatus = 'Good Health';
            $healthClass = 'bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 border-green-200 dark:border-green-900';
        } elseif ($overallScore >= 50) {
            $healthStatus = 'Fair Health';
            $healthClass = 'bg-yellow-50 text-yellow-700 dark:bg-yellow-950/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-900';
        } else {
            $healthStatus = 'Poor Health';
            $healthClass = 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border-red-200 dark:border-red-900';
        }

        // Core checks array
        $checks = [
            [
                'id' => 'meta_title',
                'name' => 'Meta Title',
                'description' => 'All indexing pages contain a unique title tag under 60 characters.',
                'score' => $metaTitleScore,
                'passed' => $metaTitleScore >= 80,
            ],
            [
                'id' => 'meta_description',
                'name' => 'Meta Description',
                'description' => 'Articles have rich search descriptions between 120 and 160 characters.',
                'score' => $metaDescScore,
                'passed' => $metaDescScore >= 80,
            ],
            [
                'id' => 'alt_text',
                'name' => 'Alt Text',
                'description' => 'All uploaded media assets contain descriptive alternate tags for indexing.',
                'score' => $altTextScore,
                'passed' => $altTextScore >= 80,
            ],
            [
                'id' => 'internal_links',
                'name' => 'Internal Links',
                'description' => 'Articles link to other sections and categories of the blog.',
                'score' => $internalLinksScore,
                'passed' => $internalLinksScore >= 80,
            ],
            [
                'id' => 'readability',
                'name' => 'Readability',
                'description' => 'Structure uses proper heading hierarchies (H2, H3) and short paragraphs.',
                'score' => $readabilityScore,
                'passed' => $readabilityScore >= 80,
            ],
            [
                'id' => 'keyword_density',
                'name' => 'Keyword Density',
                'description' => 'Main tag keyword distribution ranges between 1% and 2.5% density.',
                'score' => $keywordDensityScore,
                'passed' => $keywordDensityScore >= 80,
            ],
        ];

        $passedChecksCount = collect($checks)->where('passed', true)->count();

        return view('admin.seo.index', compact('posts', 'pages', 'overallScore', 'checks', 'passedChecksCount', 'healthStatus', 'healthClass'));
    }
}
