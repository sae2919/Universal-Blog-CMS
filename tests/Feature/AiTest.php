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
                'content' => 'This is a laravel post with some text.',
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
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotEmpty($response->json('title'));
        $this->assertNotEmpty($response->json('content'));
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
}
