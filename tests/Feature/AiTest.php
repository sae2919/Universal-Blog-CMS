<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_generate_article_endpoint_requires_auth()
    {
        $this->postJson(route('admin.ai.generate-article'), ['title' => 'Test Topic'])
            ->assertStatus(401);
    }

    public function test_generate_article_endpoint_autocompletes_successfully()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.generate-article'), [
                'title' => 'Laravel performance optimization guides',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'title',
                'content',
                'excerpt',
                'tags',
                'keywords',
                'seo_description',
                'featured_image_url',
                'featured_image_path',
                'generated_media',
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotEmpty($response->json('title'));
        $this->assertNotEmpty($response->json('content'));
        $this->assertNotEmpty($response->json('featured_image_url'));
        $this->assertNotEmpty($response->json('featured_image_path'));
        $this->assertIsArray($response->json('generated_media'));

        $content = $response->json('content');
        // Ensure placeholders were replaced with tiptap wrapper and post-slider class
        $this->assertStringContainsString('tiptap-image-wrapper', $content);
        $this->assertStringContainsString('post-slider', $content);
        $this->assertStringContainsString('<table', $content);
        $this->assertStringNotContainsString('data-ai-prompt', $content);
        $this->assertStringNotContainsString('post-slider-placeholder', $content);
    }

    public function test_correct_grammar_endpoint_rectifies_text()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.correct-grammar'), [
                'content' => '<p>I has a apple and teh code write code real good.</p>',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $corrected = $response->json('corrected_content');
        $this->assertStringContainsString('have', $corrected);
        $this->assertStringContainsString('apple', $corrected);
        $this->assertStringContainsString('code', $corrected);
    }

    public function test_generate_faqs_endpoint_generates_faqs()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.generate-faqs'), [
                'content' => 'We are talking about laravel and php here.',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'faqs' => [
                    '*' => ['question', 'answer']
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_generate_article_does_not_repeat_unsplash_images()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.generate-article'), [
                'title' => 'Laravel code optimization python data science',
            ]);

        $response->assertStatus(200);
        $media = $response->json('generated_media');
        
        $this->assertIsArray($media);
        $this->assertGreaterThanOrEqual(2, count($media));

        $fileNames = array_column($media, 'fileName');
        $uniqueFileNames = array_unique($fileNames);
        
        $this->assertCount(count($fileNames), $uniqueFileNames, 'Image filenames were duplicated in the generated article.');
    }

    public function test_generate_article_does_not_repeat_unsplash_images_across_different_requests()
    {
        \Illuminate\Support\Facades\Cache::forget('ai_used_unsplash_urls');

        // First Request
        $response1 = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.generate-article'), [
                'title' => 'Laravel coding optimization',
            ]);
        $response1->assertStatus(200);
        $url1 = $response1->json('featured_image_url');

        // Second Request
        $response2 = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.generate-article'), [
                'title' => 'Laravel coding optimization',
            ]);
        $response2->assertStatus(200);
        $url2 = $response2->json('featured_image_url');

        $this->assertNotEmpty($url1);
        $this->assertNotEmpty($url2);
        $this->assertNotEquals($url1, $url2, 'The same Unsplash image URL was selected across different generation requests.');
    }
}
