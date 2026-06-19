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

    /**
     * Audit a single post or page and return the detailed JSON results.
     */
    public function audit($type, $id)
    {
        if ($type === 'post') {
            $item = Post::with('tags')->findOrFail($id);
        } elseif ($type === 'page') {
            $item = Page::findOrFail($id);
        } else {
            return response()->json(['error' => 'Invalid content type.'], 400);
        }

        $checks = [];

        // 1. Meta Title Check
        $title = $item->getRawOriginal('meta_title') ?? $item->title;
        $titleLength = mb_strlen($title);
        if (empty($title)) {
            $metaTitleScore = 0;
            $metaTitleFeedback = 'No meta title is set. A fallback title is used.';
        } elseif ($titleLength > 60) {
            $metaTitleScore = 40;
            $metaTitleFeedback = "Meta title is too long ({$titleLength} characters). Keep it under 60 characters for optimal Google search visibility.";
        } else {
            $metaTitleScore = 100;
            $metaTitleFeedback = "Perfect! Meta title is {$titleLength} characters, which is within the recommended 60-character limit.";
        }
        $checks[] = [
            'id' => 'meta_title',
            'name' => 'Meta Title',
            'description' => 'Ensure the meta title tag is unique and under 60 characters.',
            'score' => $metaTitleScore,
            'passed' => $metaTitleScore >= 80,
            'feedback' => $metaTitleFeedback,
            'detail' => $title ? "\"{$title}\"" : 'Not set',
        ];

        // 2. Meta Description Check
        $desc = $item->getRawOriginal('meta_description') ?? '';
        $descLength = mb_strlen($desc);
        if (empty($desc)) {
            $metaDescScore = 0;
            $metaDescFeedback = 'No meta description is set. Search engines will auto-generate text from the body, which might look unprofessional.';
        } elseif ($descLength >= 120 && $descLength <= 160) {
            $metaDescScore = 100;
            $metaDescFeedback = "Perfect! Meta description is {$descLength} characters, which fits ideally in the search engine snippet (120-160 characters).";
        } else {
            $metaDescScore = 60;
            $metaDescFeedback = "Suboptimal length ({$descLength} characters). Recommended length is 120-160 characters for best display on search results.";
        }
        $checks[] = [
            'id' => 'meta_description',
            'name' => 'Meta Description',
            'description' => 'Ensure search description is present and between 120 and 160 characters.',
            'score' => $metaDescScore,
            'passed' => $metaDescScore >= 80,
            'feedback' => $metaDescFeedback,
            'detail' => $desc ? "\"{$desc}\"" : 'Not set',
        ];

        // 3. Alt Text Check
        $content = $item->content ?? '';
        preg_match_all('/<img[^>]+>/i', $content, $matches);
        $imgCount = count($matches[0]);
        if ($imgCount === 0) {
            $altTextScore = 100;
            $altTextFeedback = 'No images found in the content, so no alt attributes are missing.';
            $altCount = 0;
        } else {
            $altCount = 0;
            foreach ($matches[0] as $img) {
                if (preg_match('/alt=["\']([^"\']*)["\']/i', $img, $altMatch)) {
                    if (!empty(trim($altMatch[1]))) {
                        $altCount++;
                    }
                }
            }
            $altTextScore = round(($altCount / $imgCount) * 100);
            if ($altTextScore === 100) {
                $altTextFeedback = "Perfect! All {$imgCount} image(s) in the content have descriptive alt tags.";
            } else {
                $missing = $imgCount - $altCount;
                $altTextFeedback = "Missing alt tags on {$missing} of {$imgCount} image(s) in the content. Alt text helps search engines read your images and improves accessibility.";
            }
        }
        $checks[] = [
            'id' => 'alt_text',
            'name' => 'Alt Text',
            'description' => 'Ensure all images in content have descriptive alt text.',
            'score' => $altTextScore,
            'passed' => $altTextScore >= 80,
            'feedback' => $altTextFeedback,
            'detail' => "{$imgCount} image(s) found. {$altCount} have alt tags.",
        ];

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
        if ($hasInternal) {
            $internalLinksScore = 100;
            $internalLinksFeedback = 'Perfect! Found internal links in your content. This helps spread authority and improves crawlability.';
        } else {
            if ($wordCount < 150) {
                $internalLinksScore = 100;
                $internalLinksFeedback = 'Content is short (under 150 words), so internal links are optional.';
            } else {
                $internalLinksScore = 0;
                $internalLinksFeedback = 'No internal links found. Adding links to other posts, categories, or pages on your website boosts SEO health.';
            }
        }
        $checks[] = [
            'id' => 'internal_links',
            'name' => 'Internal Links',
            'description' => 'Ensure the content links to other pages or posts on your site.',
            'score' => $internalLinksScore,
            'passed' => $internalLinksScore >= 80,
            'feedback' => $internalLinksFeedback,
            'detail' => $hasInternal ? 'Internal link(s) present' : 'No internal links found',
        ];

        // 5. Readability Check
        $tooLongParagraphs = 0;
        if ($wordCount < 150) {
            $readabilityScore = 100;
            $readabilityFeedback = "Content is short ({$wordCount} words), which is easy to read. Structure audits are skipped for short content.";
        } else {
            $readabilityScore = 0;
            $hasHeadings = preg_match('/<h[2-4][^>]*>/i', $content);
            if ($hasHeadings) {
                $readabilityScore += 50;
            }

            preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $pMatches);
            $totalParagraphs = count($pMatches[1]);
            foreach ($pMatches[1] as $pText) {
                $pWordCount = str_word_count(strip_tags($pText));
                if ($pWordCount > 100) {
                    $tooLongParagraphs++;
                }
            }

            if ($tooLongParagraphs === 0) {
                $readabilityScore += 50;
            } elseif ($tooLongParagraphs === 1) {
                $readabilityScore += 30;
            } else {
                $readabilityScore += 10;
            }

            $feedbackParts = [];
            if ($hasHeadings) {
                $feedbackParts[] = 'Structure contains headings (H2-H4) to break up sections.';
            } else {
                $feedbackParts[] = 'Missing heading tags (H2-H4). Use subheadings to make scanning easier.';
            }

            if ($tooLongParagraphs === 0) {
                $feedbackParts[] = 'Paragraph sizes are optimal (all paragraphs are under 100 words).';
            } else {
                $feedbackParts[] = "{$tooLongParagraphs} paragraph(s) are too long (over 100 words). Break them up to improve reading flow.";
            }

            $readabilityFeedback = implode(' ', $feedbackParts);
        }
        $checks[] = [
            'id' => 'readability',
            'name' => 'Readability',
            'description' => 'Ensure text structure uses subheadings and has manageable paragraph lengths.',
            'score' => $readabilityScore,
            'passed' => $readabilityScore >= 80,
            'feedback' => $readabilityFeedback,
            'detail' => "{$wordCount} words, {$tooLongParagraphs} long paragraph(s)",
        ];

        // 6. Keyword Density Check
        // Gather keywords (tags for Post, meta_keywords for Page)
        $keywords = [];
        if ($type === 'post') {
            $keywords = $item->tags->pluck('name')->toArray();
        }
        if (empty($keywords) && !empty($item->meta_keywords)) {
            $keywords = array_filter(array_map('trim', explode(',', $item->meta_keywords)));
        }

        if (empty($keywords)) {
            $keywordDensityScore = 100;
            $keywordDensityFeedback = 'No tags or meta keywords defined. Keyword density calculations could not be run.';
            $keywordDetails = 'No keywords set';
        } else {
            $textLower = strtolower(strip_tags($content));
            if ($wordCount > 0) {
                $tagScores = [];
                $densityDetails = [];
                foreach ($keywords as $keyword) {
                    $keywordLower = strtolower($keyword);
                    $count = substr_count($textLower, $keywordLower);
                    $density = ($count * 100) / $wordCount;
                    $densityFormatted = number_format($density, 1);
                    
                    if ($density >= 1.0 && $density <= 2.5) {
                        $tagScores[] = 100;
                        $densityDetails[] = "'{$keyword}': {$densityFormatted}% (Optimal)";
                    } elseif ($density >= 0.5 && $density <= 3.5) {
                        $tagScores[] = 70;
                        $densityDetails[] = "'{$keyword}': {$densityFormatted}% (Suboptimal)";
                    } elseif ($density > 0) {
                        $tagScores[] = 40;
                        $densityDetails[] = "'{$keyword}': {$densityFormatted}% (Too low/high)";
                    } else {
                        $tagScores[] = 0;
                        $densityDetails[] = "'{$keyword}': 0% (Missing in body)";
                    }
                }
                $keywordDensityScore = round(array_sum($tagScores) / count($tagScores));
                $keywordDensityFeedback = 'Density reports: ' . implode(', ', $densityDetails) . '. Ideal density is 1.0% to 2.5% per keyword.';
                $keywordDetails = count($keywords) . ' keyword(s) analyzed';
            } else {
                $keywordDensityScore = 100;
                $keywordDensityFeedback = 'Content is empty, keyword density cannot be computed.';
                $keywordDetails = 'Empty content';
            }
        }
        $checks[] = [
            'id' => 'keyword_density',
            'name' => 'Keyword Density',
            'description' => 'Target keywords should make up 1% to 2.5% of content for best search relevance.',
            'score' => $keywordDensityScore,
            'passed' => $keywordDensityScore >= 80,
            'feedback' => $keywordDensityFeedback,
            'detail' => $keywordDetails,
        ];

        // Overall Score (average of all 6)
        $overallScore = round(array_sum(array_column($checks, 'score')) / count($checks));

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

        return response()->json([
            'title' => $item->title,
            'type' => ucfirst($type),
            'overallScore' => $overallScore,
            'healthStatus' => $healthStatus,
            'healthClass' => $healthClass,
            'checks' => $checks,
            'wordCount' => $wordCount,
        ]);
    }
}
