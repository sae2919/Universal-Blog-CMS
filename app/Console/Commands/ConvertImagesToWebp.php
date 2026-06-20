<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Media;
use App\Models\Post;
use App\Models\Page;
use App\Models\Category;

class ConvertImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-images-to-webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all existing JPG/PNG/WebP images to WebP format, update file storage, and DB references.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WebP Image Migration...');
        $disk = Storage::disk('public');
        $manager = new ImageManager(new Driver());
        
        // 1. Process Media Records
        $mediaRecords = Media::all();
        $convertedMediaCount = 0;
        
        foreach ($mediaRecords as $media) {
            $path = $media->file_path;
            if (!$path || !$disk->exists($path)) {
                $this->warn("Media #{$media->id} file not found: {$path}");
                continue;
            }
            
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($extension === 'webp' && $media->mime_type === 'image/webp') {
                continue;
            }
            
            if (in_array($extension, ['gif', 'svg'])) {
                continue;
            }
            
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }
            
            try {
                $fullPath = $disk->path($path);
                $imageObj = $manager->read($fullPath);
                $webpData = $imageObj->toWebp(90);
                
                $dir = pathinfo($path, PATHINFO_DIRNAME);
                $filename = pathinfo($path, PATHINFO_FILENAME);
                $newPath = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                
                $disk->put($newPath, $webpData);
                
                if ($newPath !== $path) {
                    $disk->delete($path);
                }
                
                // Update media record
                $media->update([
                    'file_path' => $newPath,
                    'file_name' => pathinfo($media->file_name, PATHINFO_FILENAME) . '.webp',
                    'mime_type' => 'image/webp',
                    'file_size' => strlen($webpData),
                ]);
                
                $convertedMediaCount++;
            } catch (\Exception $e) {
                $this->error("Failed to convert media #{$media->id}: " . $e->getMessage());
            }
        }
        $this->info("Converted {$convertedMediaCount} media files to WebP.");

        // 2. Process Post Featured Images & Metadata
        $posts = Post::all();
        $convertedPostFeats = 0;
        
        foreach ($posts as $post) {
            $dirty = false;
            
            // Featured Image
            $feat = $post->featured_image;
            if ($feat && $disk->exists($feat)) {
                $ext = strtolower(pathinfo($feat, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png']) && $ext !== 'webp') {
                    try {
                        $fullPath = $disk->path($feat);
                        $imageObj = $manager->read($fullPath);
                        $webpData = $imageObj->toWebp(90);
                        
                        $dir = pathinfo($feat, PATHINFO_DIRNAME);
                        $filename = pathinfo($feat, PATHINFO_FILENAME);
                        $newFeat = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                        
                        $disk->put($newFeat, $webpData);
                        if ($newFeat !== $feat) {
                            $disk->delete($feat);
                        }
                        
                        $post->featured_image = $newFeat;
                        $dirty = true;
                        $convertedPostFeats++;
                    } catch (\Exception $e) {
                        $this->error("Failed to convert featured image for post #{$post->id}: " . $e->getMessage());
                    }
                }
            }
            
            // Image Metadata
            $meta = $post->image_metadata;
            if (is_array($meta)) {
                $metaDirty = false;
                foreach ($meta as $id => &$info) {
                    // Update filePath
                    if (isset($info['filePath'])) {
                        $fPath = $info['filePath'];
                        $ext = strtolower(pathinfo($fPath, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $ext !== 'webp') {
                            // Convert the file if it hasn't been converted yet
                            if ($disk->exists($fPath)) {
                                try {
                                    $fullPath = $disk->path($fPath);
                                    $imageObj = $manager->read($fullPath);
                                    $webpData = $imageObj->toWebp(90);
                                    
                                    $dir = pathinfo($fPath, PATHINFO_DIRNAME);
                                    $filename = pathinfo($fPath, PATHINFO_FILENAME);
                                    $newFPath = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                                    
                                    $disk->put($newFPath, $webpData);
                                    if ($newFPath !== $fPath) {
                                        $disk->delete($fPath);
                                    }
                                } catch (\Exception $e) {
                                    $this->error("Failed to convert metadata image {$fPath}: " . $e->getMessage());
                                }
                            }
                            
                            $dir = pathinfo($fPath, PATHINFO_DIRNAME);
                            $filename = pathinfo($fPath, PATHINFO_FILENAME);
                            $info['filePath'] = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                            
                            if (isset($info['fileName'])) {
                                $info['fileName'] = pathinfo($info['fileName'], PATHINFO_FILENAME) . '.webp';
                            }
                            
                            $metaDirty = true;
                        }
                    }
                }
                
                if ($metaDirty) {
                    $post->image_metadata = $meta;
                    $dirty = true;
                }
            }
            
            if ($dirty) {
                $post->saveQuietly();
            }
        }
        $this->info("Converted/updated featured images and metadata for posts. (Featured: {$convertedPostFeats})");

        // 3. Process Page Featured Images & Metadata
        $pages = Page::all();
        $convertedPageFeats = 0;
        
        foreach ($pages as $page) {
            $dirty = false;
            
            // Featured Image
            $feat = $page->featured_image;
            if ($feat && $disk->exists($feat)) {
                $ext = strtolower(pathinfo($feat, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png']) && $ext !== 'webp') {
                    try {
                        $fullPath = $disk->path($feat);
                        $imageObj = $manager->read($fullPath);
                        $webpData = $imageObj->toWebp(90);
                        
                        $dir = pathinfo($feat, PATHINFO_DIRNAME);
                        $filename = pathinfo($feat, PATHINFO_FILENAME);
                        $newFeat = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                        
                        $disk->put($newFeat, $webpData);
                        if ($newFeat !== $feat) {
                            $disk->delete($feat);
                        }
                        
                        $page->featured_image = $newFeat;
                        $dirty = true;
                        $convertedPageFeats++;
                    } catch (\Exception $e) {
                        $this->error("Failed to convert featured image for page #{$page->id}: " . $e->getMessage());
                    }
                }
            }
            
            // Image Metadata
            $meta = $page->image_metadata;
            if (is_array($meta)) {
                $metaDirty = false;
                foreach ($meta as $id => &$info) {
                    // Update filePath
                    if (isset($info['filePath'])) {
                        $fPath = $info['filePath'];
                        $ext = strtolower(pathinfo($fPath, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $ext !== 'webp') {
                            if ($disk->exists($fPath)) {
                                try {
                                    $fullPath = $disk->path($fPath);
                                    $imageObj = $manager->read($fullPath);
                                    $webpData = $imageObj->toWebp(90);
                                    
                                    $dir = pathinfo($fPath, PATHINFO_DIRNAME);
                                    $filename = pathinfo($fPath, PATHINFO_FILENAME);
                                    $newFPath = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                                    
                                    $disk->put($newFPath, $webpData);
                                    if ($newFPath !== $fPath) {
                                        $disk->delete($fPath);
                                    }
                                } catch (\Exception $e) {
                                    $this->error("Failed to convert metadata image {$fPath}: " . $e->getMessage());
                                }
                            }
                            
                            $dir = pathinfo($fPath, PATHINFO_DIRNAME);
                            $filename = pathinfo($fPath, PATHINFO_FILENAME);
                            $info['filePath'] = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                            
                            if (isset($info['fileName'])) {
                                $info['fileName'] = pathinfo($info['fileName'], PATHINFO_FILENAME) . '.webp';
                            }
                            
                            $metaDirty = true;
                        }
                    }
                }
                
                if ($metaDirty) {
                    $page->image_metadata = $meta;
                    $dirty = true;
                }
            }
            
            if ($dirty) {
                $page->saveQuietly();
            }
        }
        $this->info("Converted/updated featured images and metadata for pages. (Featured: {$convertedPageFeats})");

        // 4. Process Category Images
        $categories = Category::all();
        $convertedCatCount = 0;
        
        foreach ($categories as $cat) {
            $catImg = $cat->image;
            if ($catImg && $disk->exists($catImg)) {
                $ext = strtolower(pathinfo($catImg, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png']) && $ext !== 'webp') {
                    try {
                        $fullPath = $disk->path($catImg);
                        $imageObj = $manager->read($fullPath);
                        $webpData = $imageObj->toWebp(90);
                        
                        $dir = pathinfo($catImg, PATHINFO_DIRNAME);
                        $filename = pathinfo($catImg, PATHINFO_FILENAME);
                        $newCatImg = ($dir === '.' ? '' : $dir . '/') . $filename . '.webp';
                        
                        $disk->put($newCatImg, $webpData);
                        if ($newCatImg !== $catImg) {
                            $disk->delete($catImg);
                        }
                        
                        $cat->update(['image' => $newCatImg]);
                        $convertedCatCount++;
                    } catch (\Exception $e) {
                        $this->error("Failed to convert image for category #{$cat->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Converted {$convertedCatCount} category images to WebP.");
        
        $this->info('WebP Image Migration successfully completed!');
    }
}
