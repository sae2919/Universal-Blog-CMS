<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageMetadataService
{
    /**
     * Enhance image metadata with filePath, width, and height.
     */
    public function optimizeMetadata(array $metadata, string $content = '', ?string $featuredImage = null): array
    {
        $manager = new ImageManager(new Driver());
        $disk = Storage::disk('public');
        
        $idToSrcMap = [];
        // Extract actual filenames from content HTML for matching
        $contentFilenames = [];
        if (!empty($content)) {
            preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches);
            foreach ($matches[1] as $src) {
                $contentFilenames[] = basename(parse_url($src, PHP_URL_PATH));
            }

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $img) {
                $dataId = $img->getAttribute('data-image-id');
                $src = $img->getAttribute('src');
                if ($dataId && $src) {
                    $idToSrcMap[$dataId] = $src;
                }
            }
        }
        
        foreach ($metadata as $id => &$info) {
            if (isset($idToSrcMap[$id])) {
                $info['url'] = $idToSrcMap[$id];
            }
            $fileName = $info['fileName'] ?? null;
            if (!$fileName) {
                continue;
            }
            
            $foundPath = null;
            
            // 1. Try to match with a filename extracted from content HTML first (handles uploads with timestamps)
            foreach ($contentFilenames as $cName) {
                $origBase = pathinfo($fileName, PATHINFO_FILENAME);
                $slugBase = \Str::slug($origBase);
                if (str_starts_with($cName, $slugBase) || str_contains($cName, $slugBase)) {
                    if ($disk->exists('uploads/' . $cName)) {
                        $foundPath = 'uploads/' . $cName;
                        break;
                    }
                }
            }
            
            // 2. Try to match the featured image
            if (!$foundPath && $featuredImage) {
                $featName = basename($featuredImage);
                $origBase = pathinfo($fileName, PATHINFO_FILENAME);
                if ($featName === $fileName || str_contains($featName, $origBase)) {
                    if ($disk->exists($featuredImage)) {
                        $foundPath = $featuredImage;
                    }
                }
            }
            
            // 3. Fallback to basic file lookup
            if (!$foundPath) {
                $possiblePaths = [
                    'uploads/' . $fileName,
                    'posts/' . $fileName,
                    'pages/' . $fileName,
                    $fileName,
                ];
                foreach ($possiblePaths as $p) {
                    if ($disk->exists($p)) {
                        $foundPath = $p;
                        break;
                    }
                }
            }
            
            if ($foundPath) {
                $info['filePath'] = $foundPath;
                try {
                    $fullPath = $disk->path($foundPath);
                    $image = $manager->read($fullPath);
                    
                    $info['width'] = $image->width();
                    $info['height'] = $image->height();
                } catch (\Exception $e) {
                    Log::error("Failed to read image for metadata optimization: " . $e->getMessage());
                }
            }
        }
        
        return $metadata;
    }

    /**
     * Strip src and layout attributes from img tags before saving to DB, keeping data-image-id.
     */
    public function stripSrc(string $content, array $metadata): string
    {
        if (empty($content)) {
            return $content;
        }
        
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $images = $dom->getElementsByTagName('img');
        $changed = false;
        
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $dataId = $img->getAttribute('data-image-id');
            
            $matchedId = $dataId;
            if (!$matchedId && $src) {
                $srcName = basename(parse_url($src, PHP_URL_PATH));
                foreach ($metadata as $id => $info) {
                    // Match by filePath first
                    if (isset($info['filePath']) && basename($info['filePath']) === $srcName) {
                        $matchedId = $id;
                        break;
                    }
                    // Match by fileName original
                    if (isset($info['fileName']) && basename($info['fileName']) === $srcName) {
                        $matchedId = $id;
                        break;
                    }
                    // Fuzzy match slugified original filename
                    if (isset($info['fileName'])) {
                        $origBase = pathinfo($info['fileName'], PATHINFO_FILENAME);
                        $slugBase = \Str::slug($origBase);
                        if (str_starts_with($srcName, $slugBase) || str_contains($srcName, $slugBase)) {
                            $matchedId = $id;
                            break;
                        }
                    }
                }
            }
            
            if ($matchedId) {
                $changed = true;
                $img->setAttribute('data-image-id', $matchedId);
                
                // Remove src attribute to decouple it
                $img->removeAttribute('src');
                
                // Remove dynamic layout attributes
                $img->removeAttribute('width');
                $img->removeAttribute('height');
                $img->removeAttribute('loading');
                
                $style = $img->getAttribute('style');
                if ($style) {
                    $style = preg_replace('/aspect-ratio:\s*[^;]+;?/i', '', $style);
                    $style = trim($style);
                    if ($style) {
                        $img->setAttribute('style', $style);
                    } else {
                        $img->removeAttribute('style');
                    }
                }
            }
        }
        
        if ($changed) {
            $rootDiv = $dom->getElementsByTagName('div')->item(0);
            if ($rootDiv) {
                $newContent = '';
                foreach ($rootDiv->childNodes as $child) {
                    $newContent .= $dom->saveHTML($child);
                }
                return $newContent;
            }
        }
        
        return $content;
    }

    /**
     * Restore the public URL/src of image tags for editing or standard content loading.
     */
    public function restoreSrc(string $content, array $metadata): string
    {
        if (empty($content)) {
            return $content;
        }
        
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $images = $dom->getElementsByTagName('img');
        $changed = false;
        
        foreach ($images as $img) {
            $dataId = $img->getAttribute('data-image-id');
            if (!$dataId || !isset($metadata[$dataId])) {
                continue;
            }
            
            $info = $metadata[$dataId];
            $filePath = $info['filePath'] ?? null;
            if (!$filePath) {
                $fileName = $info['fileName'] ?? null;
                if ($fileName) {
                    $disk = Storage::disk('public');
                    $possiblePaths = ['uploads/' . $fileName, 'posts/' . $fileName, 'pages/' . $fileName, $fileName];
                    foreach ($possiblePaths as $p) {
                        if ($disk->exists($p)) {
                            $filePath = $p;
                            break;
                        }
                    }
                }
            }
            
            if ($filePath) {
                $changed = true;
                $img->setAttribute('src', asset('storage/' . $filePath));
            } elseif (isset($info['url'])) {
                $changed = true;
                $img->setAttribute('src', $info['url']);
            }
        }
        
        if ($changed) {
            $rootDiv = $dom->getElementsByTagName('div')->item(0);
            if ($rootDiv) {
                $newContent = '';
                foreach ($rootDiv->childNodes as $child) {
                    $newContent .= $dom->saveHTML($child);
                }
                return $newContent;
            }
        }
        
        return $content;
    }

    /**
     * Enhance HTML content by injecting layout attributes, src, and aspect-ratio on matching images.
     */
    public function enhanceContent(string $content, array $metadata): string
    {
        if (empty($content)) {
            return $content;
        }
        
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $images = $dom->getElementsByTagName('img');
        $changed = false;
        
        foreach ($images as $img) {
            $dataId = $img->getAttribute('data-image-id');
            
            // Try to match by src if data-image-id is missing (backward compatibility)
            $matchedId = $dataId;
            if (!$matchedId) {
                $src = $img->getAttribute('src');
                if ($src) {
                    $srcName = basename(parse_url($src, PHP_URL_PATH));
                    foreach ($metadata as $id => $info) {
                        if (isset($info['filePath']) && basename($info['filePath']) === $srcName) {
                            $matchedId = $id;
                            break;
                        }
                        if (isset($info['fileName']) && basename($info['fileName']) === $srcName) {
                            $matchedId = $id;
                            break;
                        }
                        if (isset($info['fileName'])) {
                            $origBase = pathinfo($info['fileName'], PATHINFO_FILENAME);
                            $slugBase = \Str::slug($origBase);
                            if (str_starts_with($srcName, $slugBase) || str_contains($srcName, $slugBase)) {
                                $matchedId = $id;
                                break;
                            }
                        }
                    }
                }
            }
            
            if ($matchedId && isset($metadata[$matchedId])) {
                $changed = true;
                $matchedInfo = $metadata[$matchedId];
                
                if (!$img->hasAttribute('data-image-id')) {
                    $img->setAttribute('data-image-id', $matchedId);
                }
                
                // Restore src
                $filePath = $matchedInfo['filePath'] ?? null;
                if (!$filePath) {
                    $fileName = $matchedInfo['fileName'] ?? null;
                    if ($fileName) {
                        $disk = Storage::disk('public');
                        $possiblePaths = ['uploads/' . $fileName, 'posts/' . $fileName, 'pages/' . $fileName, $fileName];
                        foreach ($possiblePaths as $p) {
                            if ($disk->exists($p)) {
                                $filePath = $p;
                                break;
                            }
                        }
                    }
                }
                if ($filePath) {
                    $img->setAttribute('src', asset('storage/' . $filePath));
                } elseif (isset($matchedInfo['url'])) {
                    $img->setAttribute('src', $matchedInfo['url']);
                }
                
                // Add width and height
                if (isset($matchedInfo['width']) && !$img->hasAttribute('width')) {
                    $img->setAttribute('width', $matchedInfo['width']);
                }
                if (isset($matchedInfo['height']) && !$img->hasAttribute('height')) {
                    $img->setAttribute('height', $matchedInfo['height']);
                }
                
                // Add lazy loading
                if (!$img->hasAttribute('loading')) {
                    $img->setAttribute('loading', 'lazy');
                }
                
                // Add aspect-ratio style
                if (isset($matchedInfo['width']) && isset($matchedInfo['height'])) {
                    $existingStyle = $img->getAttribute('style');
                    $aspectRatio = $matchedInfo['width'] . ' / ' . $matchedInfo['height'];
                    
                    $styleParts = [];
                    if ($existingStyle) {
                        $styleParts[] = rtrim($existingStyle, ';');
                    }
                    $styleParts[] = "aspect-ratio: {$aspectRatio}";
                    
                    $img->setAttribute('style', implode('; ', $styleParts) . ';');
                }
            }
        }
        
        if ($changed) {
            $rootDiv = $dom->getElementsByTagName('div')->item(0);
            if ($rootDiv) {
                $newContent = '';
                foreach ($rootDiv->childNodes as $child) {
                    $newContent .= $dom->saveHTML($child);
                }
                return $newContent;
            }
        }
        
        return $content;
    }
}
