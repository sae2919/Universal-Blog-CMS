<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;
    private Tag $tag1;
    private Tag $tag2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->category = Category::create([
            'slug' => 'tech',
            'name' => 'Technology',
            'status' => 'active',
        ]);

        $this->tag1 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $this->tag2 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
    }

    public function test_admin_can_create_post_with_tags_and_faqs()
    {
        $postData = [
            'title' => 'My First Amazing Post',
            'category_id' => $this->category->id,
            'locale' => 'en',
            'excerpt' => 'An excerpt explaining the post contents.',
            'content' => '<p>This is the detailed body content of the blog post.</p>',
            'status' => 'published',
            'is_featured' => true,
            'is_trending' => false,
            'allow_comments' => true,
            'tags' => [$this->tag1->id, $this->tag2->id],
            'faqs' => [
                [
                    'question' => 'Is this a test question?',
                    'answer' => '<p>Yes, it is.</p>'
                ],
                [
                    'question' => 'How does slugging work?',
                    'answer' => '<p>It works automatically from the title.</p>'
                ]
            ],
            'meta_title' => 'SEO Title',
            'meta_description' => 'SEO Description',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), $postData);

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success', 'Post created successfully!');

        $this->assertDatabaseHas('posts', [
            'title' => 'My First Amazing Post',
            'slug' => 'my-first-amazing-post',
            'category_id' => $this->category->id,
            'locale' => 'en',
            'is_featured' => 1,
            'is_trending' => 0,
            'allow_comments' => 1,
        ]);

        $post = Post::where('slug', 'my-first-amazing-post')->firstOrFail();
        $this->assertCount(2, $post->tags);
        $this->assertTrue($post->tags->contains($this->tag1));
        $this->assertTrue($post->tags->contains($this->tag2));

        $this->assertCount(2, $post->faqs);
        $this->assertEquals('Is this a test question?', $post->faqs[0]['question']);
    }

    public function test_admin_can_edit_and_update_post()
    {
        $post = Post::create([
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'title' => 'Old Post Title',
            'slug' => 'old-post-title',
            'excerpt' => 'Old excerpt',
            'content' => 'Old content',
            'status' => 'draft',
            'locale' => 'en',
        ]);
        $post->tags()->sync([$this->tag1->id]);

        $updateData = [
            'title' => 'Brand New Post Title',
            'category_id' => $this->category->id,
            'locale' => 'en',
            'excerpt' => 'Updated excerpt',
            'content' => 'Updated content',
            'status' => 'published',
            'tags' => [$this->tag2->id], // Switch tags
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.posts.update', $post->id), $updateData);

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success', 'Post updated successfully!');

        $post->refresh();
        $this->assertEquals('Brand New Post Title', $post->title);
        $this->assertEquals('old-post-title', $post->slug); // Slugs are preserved on update for SEO stability
        $this->assertEquals('published', $post->status);
        $this->assertNotNull($post->published_at);

        $this->assertCount(1, $post->tags);
        $this->assertTrue($post->tags->contains($this->tag2));
        $this->assertFalse($post->tags->contains($this->tag1));
    }

    public function test_post_creation_requires_mandatory_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), []);

        $response->assertSessionHasErrors(['title', 'category_id', 'locale', 'content', 'status']);
    }

    public function test_updating_post_retains_featured_image_file()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        
        $imagePath = 'uploads/ai_generated_dummy.webp';
        \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, 'dummy image content');
        
        $post = Post::create([
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'title' => 'Post with image',
            'slug' => 'post-with-image',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'status' => 'draft',
            'locale' => 'en',
            'featured_image' => $imagePath,
        ]);
        
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath));

        $updateData = [
            'title' => 'Updated Post Title',
            'category_id' => $this->category->id,
            'locale' => 'en',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'status' => 'published',
            'generated_image_path' => $imagePath,
        ];
        
        $response = $this->actingAs($this->admin)
            ->put(route('admin.posts.update', $post->id), $updateData);

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success', 'Post updated successfully!');
        
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath), 'Featured image file was deleted during update.');
        $post->refresh();
        $this->assertEquals($imagePath, $post->featured_image);
    }
}
