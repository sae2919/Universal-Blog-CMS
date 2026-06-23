<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoPostScenarioTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;
    private Tag $tagLaravel;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create a super admin / SEO editor user
        $this->admin = User::create([
            'name' => 'SEO Specialist',
            'email' => 'seo@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Set up a category
        $this->category = Category::create([
            'slug' => 'it-courses',
            'name' => 'IT Courses',
            'status' => 'active',
        ]);

        // 3. Set up tag
        $this->tagLaravel = Tag::create([
            'slug' => 'laravel',
            'name' => 'Laravel',
        ]);

        // Clean up storage disk
        Storage::fake('public');
    }

    /**
     * Test the complete flow of post generation, database media persistence,
     * publishing, sitemap injection, and frontend SEO compliance.
     */
    public function test_seo_employee_workflow_and_frontend_compliance()
    {
        // --- Scenario 1: AI Assistant Generation ---
        // Verify that the SEO specialist can call the AI generation endpoint
        // which automatically completes fields, creates inline images, and extracts FAQs.
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.ai.generate-article'), [
                'title' => 'Laravel performance optimization guides',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'title',
                'meta_title',
                'content',
                'excerpt',
                'tags',
                'keywords',
                'seo_description',
                'faqs',
                'featured_image_url',
                'featured_image_path',
                'generated_media',
            ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        
        // Assert that the content contains the table but NOT image wrapper and slider elements
        $contentHtml = $responseData['content'];
        $this->assertStringNotContainsString('tiptap-image-wrapper', $contentHtml);
        $this->assertStringNotContainsString('post-slider', $contentHtml);
        $this->assertStringContainsString('<table', $contentHtml);

        // --- Scenario 2: No Database Storage of Generated Images ---
        // Verify that no images are stored since image generation is disabled.
        $generatedMediaList = $responseData['generated_media'];
        $this->assertIsArray($generatedMediaList);
        $this->assertEmpty($generatedMediaList);
        $this->assertNull($responseData['featured_image_path']);
        $this->assertEquals(0, Media::count());

        // --- Scenario 3: Store/Publish the Article ---
        // Submit the form with all fields (simulate clicking "Publish Article")
        $postPayload = [
            'title' => $responseData['title'],
            'category_id' => $this->category->id,
            'locale' => 'en',
            'excerpt' => $responseData['excerpt'],
            'content' => $responseData['content'],
            'status' => 'published',
            'is_featured' => true,
            'is_trending' => true,
            'allow_comments' => true,
            'tags' => [$this->tagLaravel->id],
            'faqs' => $responseData['faqs'],
            'meta_title' => $responseData['meta_title'],
            'meta_description' => $responseData['seo_description'],
            'meta_keywords' => $responseData['keywords'],
            'og_title' => $responseData['title'],
            'og_description' => $responseData['excerpt'],
            'image_metadata' => json_encode($generatedMediaList),
            'generated_image_path' => $responseData['featured_image_path'],
        ];

        // Simulate submitting the post
        $storeResponse = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), $postPayload);

        $storeResponse->assertRedirect(route('admin.posts.index'));
        $storeResponse->assertSessionHas('success', 'Post created successfully!');

        // Assert database persistence
        $this->assertDatabaseHas('posts', [
            'title' => $responseData['title'],
            'category_id' => $this->category->id,
            'status' => 'published',
            'is_featured' => 1,
            'is_trending' => 1,
        ]);

        $createdPost = Post::where('title', $responseData['title'])->firstOrFail();
        $this->assertCount(1, $createdPost->tags);
        $this->assertGreaterThanOrEqual(1, count($createdPost->faqs));

        // --- Scenario 4: Frontend Display & Layouts ---
        // 4a. Verify Homepage listing
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($createdPost->title);

        // 4b. Verify Blog list page
        $blogResponse = $this->get(route('blog.index'));
        $blogResponse->assertStatus(200);
        $blogResponse->assertSee($createdPost->title);

        // 4c. Verify Category page
        $categoryResponse = $this->get(route('blog.category', $this->category->slug));
        $categoryResponse->assertStatus(200);
        $categoryResponse->assertSee($createdPost->title);

        // 4d. Verify Tag page (it should have robots noindex meta tag)
        $tagResponse = $this->get(route('blog.tag', $this->tagLaravel->slug));
        $tagResponse->assertStatus(200);
        $tagResponse->assertSee($createdPost->title);
        $tagResponse->assertSee('<meta name="robots" content="noindex, follow">', false);

        // --- Scenario 5: Deep SEO Compliance on Post Detail Page ---
        // Let's visit the single blog post page
        $postUrl = route('blog.show', [
            'categorySlug' => $this->category->slug,
            'postSlug' => $createdPost->slug
        ]);

        $postDetailResponse = $this->get($postUrl);
        $postDetailResponse->assertStatus(200);

        // 5a. Canonical tag must point to the absolute URL of the page itself
        $postDetailResponse->assertSee('<link rel="canonical" href="' . $postUrl . '">', false);

        // 5b. Headings hierarchy: H1 must be the post title exactly (and only one H1 should exist)
        $postHtml = $postDetailResponse->getContent();
        $this->assertEquals(1, preg_match_all('/<h1[^>]*>(.*?)<\/h1>/si', $postHtml, $h1Matches));
        $this->assertStringContainsString($createdPost->title, $h1Matches[1][0]);

        // 5c. Meta tags & Open Graph tags
        $postDetailResponse->assertSee('<meta name="description" content="' . e($createdPost->meta_description) . '">', false);
        $postDetailResponse->assertSee('<meta name="keywords" content="' . e($createdPost->meta_keywords) . '">', false);
        $postDetailResponse->assertSee('<meta property="og:title" content="' . e($createdPost->meta_title . ' — ' . \App\Models\Setting::getValue('site_name')) . '">', false);
        $postDetailResponse->assertSee('<meta property="og:description" content="' . e($createdPost->meta_description) . '">', false);
        $postDetailResponse->assertSee('<meta property="og:url" content="' . $postUrl . '">', false);

        // 5d. Schema Markup (JSON-LD) - check for BlogPosting, BreadcrumbList, FAQPage
        $this->assertStringContainsString('"@type": "BlogPosting"', $postHtml);
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $postHtml);
        $this->assertStringContainsString('"@type": "FAQPage"', $postHtml);


        // --- Scenario 6: Robots.txt and Sitemap compliance ---
        // 6a. Robots.txt
        $robotsResponse = $this->get('/robots.txt');
        $robotsResponse->assertStatus(200);
        $robotsResponse->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $robotsResponse->assertSee('Sitemap: http://localhost:8001/sitemap.xml', false);

        // 6b. Sitemap.xml includes the new post
        $sitemapResponse = $this->get('/sitemap.xml');
        $sitemapResponse->assertStatus(200);
        $sitemapResponse->assertSee('<loc>' . $postUrl . '</loc>', false);
    }
}
