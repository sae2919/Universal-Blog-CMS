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
}
