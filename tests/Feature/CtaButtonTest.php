<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CtaButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_standalone_cta_button_renders_correctly_on_post_page()
    {
        // 1. Create a user
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        // 2. Create a category
        $category = Category::create([
            'slug' => 'test-category',
            'name' => 'Test Category',
            'status' => 'active',
        ]);

        // 3. Create a post with a standalone CTA button in content
        $buttonText = 'Grab Special Deal';
        $buttonUrl = 'https://example.com/special-deal';
        $ctaHtml = sprintf(
            '<p>Check out our offer: <a class="cta-button" href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
            $buttonUrl,
            $buttonText
        );

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Post with CTA Button',
            'slug' => 'post-with-cta-button',
            'excerpt' => 'This is a test post',
            'content' => $ctaHtml,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'locale' => 'en',
        ]);

        // 4. Request the frontend post page
        $response = $this->get(route('blog.show', [
            'categorySlug' => $category->slug,
            'postSlug' => $post->slug,
        ]));

        // 5. Assert successful response and verify cta-button rendering
        $response->assertStatus(200);
        $response->assertSee('<a class="cta-button"', false);
        $response->assertSee('href="https://example.com/special-deal"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee($buttonText);
    }
}
