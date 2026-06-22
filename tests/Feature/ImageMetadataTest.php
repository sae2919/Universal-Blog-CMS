<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageMetadataTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->category = Category::create([
            'slug' => 'test-category',
            'name' => 'Test Category',
            'status' => 'active',
        ]);
    }

    public function test_post_creation_saves_image_metadata()
    {
        $metadata = [
            'img_12345' => [
                'fileName' => 'test-image.jpg',
                'fileType' => 'image/jpeg',
                'fileSize' => 1024,
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Post with Metadata',
                'category_id' => $this->category->id,
                'locale' => 'en',
                'excerpt' => 'An excerpt',
                'content' => 'Post content',
                'status' => 'published',
                'image_metadata' => json_encode($metadata),
            ]);

        $response->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post with Metadata',
        ]);

        $post = Post::where('title', 'Test Post with Metadata')->firstOrFail();
        $this->assertEquals($metadata, $post->image_metadata);
    }

    public function test_post_update_persists_image_metadata()
    {
        $post = Post::create([
            'user_id' => $this->adminUser->id,
            'category_id' => $this->category->id,
            'title' => 'Initial Post Title',
            'slug' => 'initial-post-title',
            'excerpt' => 'This is a test post',
            'content' => 'Initial content',
            'status' => 'published',
            'locale' => 'en',
            'image_metadata' => [
                'img_111' => [
                    'fileName' => 'original.jpg',
                    'fileType' => 'image/jpeg',
                    'fileSize' => 500,
                ]
            ]
        ]);

        $newMetadata = [
            'img_111' => [
                'fileName' => 'original.jpg',
                'fileType' => 'image/jpeg',
                'fileSize' => 500,
            ],
            'img_222' => [
                'fileName' => 'added.png',
                'fileType' => 'image/png',
                'fileSize' => 2048,
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.posts.update', $post->id), [
                'title' => 'Updated Post Title',
                'category_id' => $this->category->id,
                'locale' => 'en',
                'excerpt' => 'Updated excerpt',
                'content' => 'Updated content',
                'status' => 'published',
                'image_metadata' => json_encode($newMetadata),
            ]);

        $response->assertRedirect(route('admin.posts.index'));

        $post->refresh();
        $this->assertEquals('Updated Post Title', $post->title);
        $this->assertEquals($newMetadata, $post->image_metadata);
    }

    public function test_page_creation_saves_image_metadata()
    {
        $metadata = [
            'img_page_123' => [
                'fileName' => 'page-hero.jpg',
                'fileType' => 'image/jpeg',
                'fileSize' => 500000,
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.pages.store'), [
                'title' => 'Test Page with Metadata',
                'locale' => 'en',
                'content' => 'Page content html',
                'status' => 'published',
                'image_metadata' => json_encode($metadata),
            ]);

        $response->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', [
            'title' => 'Test Page with Metadata',
        ]);

        $page = Page::where('title', 'Test Page with Metadata')->firstOrFail();
        $this->assertEquals($metadata, $page->image_metadata);
    }

    public function test_page_update_persists_image_metadata()
    {
        $page = Page::create([
            'title' => 'Initial Page Title',
            'slug' => 'initial-page-title',
            'locale' => 'en',
            'content' => 'Initial page content',
            'status' => 'published',
            'image_metadata' => [
                'img_page_000' => [
                    'fileName' => 'initial.png',
                    'fileType' => 'image/png',
                    'fileSize' => 200,
                ]
            ]
        ]);

        $newMetadata = [
            'img_page_000' => [
                'fileName' => 'initial.png',
                'fileType' => 'image/png',
                'fileSize' => 200,
            ],
            'img_page_999' => [
                'fileName' => 'updated.jpg',
                'fileType' => 'image/jpeg',
                'fileSize' => 9999,
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.pages.update', $page->id), [
                'title' => 'Updated Page Title',
                'locale' => 'en',
                'content' => 'Updated page content',
                'status' => 'published',
                'image_metadata' => json_encode($newMetadata),
            ]);

        $response->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertEquals('Updated Page Title', $page->title);
        $this->assertEquals($newMetadata, $page->image_metadata);
    }

    public function test_manual_image_alignment_and_optimization()
    {
        $metadata = [
            'img_left' => [
                'fileName' => 'left.webp',
                'fileType' => 'image/webp',
                'fileSize' => 1234,
                'filePath' => 'uploads/left.webp',
                'width' => 600,
                'height' => 400,
            ],
            'img_right' => [
                'fileName' => 'right.webp',
                'fileType' => 'image/webp',
                'fileSize' => 5678,
                'filePath' => 'uploads/right.webp',
                'width' => 800,
                'height' => 600,
            ],
            'img_center' => [
                'fileName' => 'center.webp',
                'fileType' => 'image/webp',
                'fileSize' => 9999,
                'filePath' => 'uploads/center.webp',
                'width' => 1000,
                'height' => 800,
            ],
        ];

        $htmlContent = '
            <div class="tiptap-image-wrapper" style="float: left; width: 300px;">
                <img data-image-id="img_left" src="http://localhost:8001/storage/uploads/left.webp" style="width: 300px;" />
            </div>
            <div class="tiptap-image-wrapper" style="float: right; width: 400px;">
                <img data-image-id="img_right" src="http://localhost:8001/storage/uploads/right.webp" style="width: 400px;" />
            </div>
            <div class="tiptap-image-wrapper" style="float: none; margin-left: auto; margin-right: auto; width: 500px;">
                <img data-image-id="img_center" src="http://localhost:8001/storage/uploads/center.webp" style="width: 500px;" />
            </div>
        ';

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.posts.store'), [
                'title' => 'Manual Alignment Post',
                'category_id' => $this->category->id,
                'locale' => 'en',
                'excerpt' => 'Manual excerpt',
                'content' => $htmlContent,
                'status' => 'published',
                'image_metadata' => json_encode($metadata),
            ]);

        $response->assertRedirect(route('admin.posts.index'));

        // 1. Assert content stored in DB has stripped "src"
        $rawPostContent = \DB::table('posts')->where('title', 'Manual Alignment Post')->value('content');
        $post = Post::where('title', 'Manual Alignment Post')->firstOrFail();
        $this->assertStringNotContainsString('src=', $rawPostContent);
        $this->assertStringContainsString('data-image-id="img_left"', $rawPostContent);
        $this->assertStringContainsString('data-image-id="img_right"', $rawPostContent);
        $this->assertStringContainsString('data-image-id="img_center"', $rawPostContent);

        // 2. Assert getContentAttribute restores "src" for editing
        $restoredContent = $post->content;
        $this->assertStringContainsString('src="' . asset('storage/uploads/left.webp') . '"', $restoredContent);
        $this->assertStringContainsString('src="' . asset('storage/uploads/right.webp') . '"', $restoredContent);
        $this->assertStringContainsString('src="' . asset('storage/uploads/center.webp') . '"', $restoredContent);

        // 3. Assert getOptimizedContentAttribute outputs optimized styles and attributes
        $optimizedHtml = $post->optimized_content;
        
        // Assert left align structure
        $this->assertStringContainsString('style="float: left; width: 300px;"', $optimizedHtml);
        $this->assertStringContainsString('width="600"', $optimizedHtml);
        $this->assertStringContainsString('height="400"', $optimizedHtml);
        
        // Assert right align structure
        $this->assertStringContainsString('style="float: right; width: 400px;"', $optimizedHtml);
        $this->assertStringContainsString('width="800"', $optimizedHtml);
        $this->assertStringContainsString('height="600"', $optimizedHtml);

        // Assert lazy loading on images
        $this->assertEquals(3, substr_count($optimizedHtml, 'loading="lazy"'));
        $this->assertStringContainsString('aspect-ratio: 600 / 400', $optimizedHtml);
        $this->assertStringContainsString('aspect-ratio: 800 / 600', $optimizedHtml);
        $this->assertStringContainsString('aspect-ratio: 1000 / 800', $optimizedHtml);
    }

    public function test_offline_fallback_external_image_url_restoration()
    {
        $metadata = [
            'img_external' => [
                'fileName' => 'photo-12345.jpg',
                'fileType' => 'image/jpeg',
                'fileSize' => 0,
            ]
        ];

        $htmlContent = '<p><img data-image-id="img_external" src="https://images.unsplash.com/photo-12345?w=1200" /></p>';

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.posts.store'), [
                'title' => 'Offline Fallback URL Post',
                'category_id' => $this->category->id,
                'locale' => 'en',
                'excerpt' => 'An excerpt',
                'content' => $htmlContent,
                'status' => 'published',
                'image_metadata' => json_encode($metadata),
            ]);

        $response->assertRedirect(route('admin.posts.index'));

        $rawPostContent = \DB::table('posts')->where('title', 'Offline Fallback URL Post')->value('content');
        $post = Post::where('title', 'Offline Fallback URL Post')->firstOrFail();

        // 1. Assert src is stripped in DB
        $this->assertStringNotContainsString('src=', $rawPostContent);
        $this->assertStringContainsString('data-image-id="img_external"', $rawPostContent);

        // 2. Assert url is stored in DB image_metadata
        $this->assertArrayHasKey('img_external', $post->image_metadata);
        $this->assertEquals('https://images.unsplash.com/photo-12345?w=1200', $post->image_metadata['img_external']['url']);

        // 3. Assert getContentAttribute restores Unsplash URL
        $this->assertStringContainsString('src="https://images.unsplash.com/photo-12345?w=1200"', $post->content);

        // 4. Assert getOptimizedContentAttribute restores Unsplash URL
        $this->assertStringContainsString('src="https://images.unsplash.com/photo-12345?w=1200"', $post->optimized_content);
    }
}
