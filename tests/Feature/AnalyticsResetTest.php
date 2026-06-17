<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Visit;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AnalyticsResetTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Seed default setting
        Setting::create([
            'site_name' => 'Test Blog',
            'site_niche' => 'technology',
            'site_accent_color' => 'indigo',
            'site_font' => 'font-sans',
            'site_layout' => 'grid',
        ]);
    }

    public function test_admin_can_reset_analytics_data()
    {
        // 1. Create a Category and Post with some views
        $category = Category::create([
            'name' => 'Technology',
            'locale' => 'en',
            'status' => 'active',
        ]);

        $post1 = Post::create([
            'user_id' => $this->admin->id,
            'category_id' => $category->id,
            'locale' => 'en',
            'title' => 'First Post',
            'content' => 'Content 1',
            'status' => 'published',
            'views' => 15,
        ]);

        $post2 = Post::create([
            'user_id' => $this->admin->id,
            'category_id' => $category->id,
            'locale' => 'en',
            'title' => 'Second Post',
            'content' => 'Content 2',
            'status' => 'published',
            'views' => 100,
        ]);

        // 2. Create some visits in visits table
        Visit::create([
            'url' => '/blog/first-post',
            'ip_address' => '127.0.0.1',
            'country' => 'US',
            'device' => 'desktop',
            'browser' => 'Chrome',
            'visited_at' => now(),
        ]);

        Visit::create([
            'url' => '/blog/second-post',
            'ip_address' => '127.0.0.1',
            'country' => 'US',
            'device' => 'desktop',
            'browser' => 'Firefox',
            'visited_at' => now(),
        ]);

        // Assert setup has database items
        $this->assertEquals(2, Visit::count());
        $this->assertEquals(15, Post::find($post1->id)->views);
        $this->assertEquals(100, Post::find($post2->id)->views);

        // Put some dummy data in the cache to check cache flushing
        Cache::put('popular_posts', [$post1, $post2], 3600);
        $this->assertTrue(Cache::has('popular_posts'));

        // 3. Act as admin and post to the reset route
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.reset-analytics'));

        // 4. Assert responses and redirects
        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success', 'Analytics dashboard data has been reset to zero!');

        // 5. Assert database records are updated/cleared
        $this->assertEquals(0, Visit::count());
        $this->assertEquals(0, Post::find($post1->id)->views);
        $this->assertEquals(0, Post::find($post2->id)->views);

        // 6. Assert cache has been flushed
        $this->assertFalse(Cache::has('popular_posts'));
    }

    public function test_non_admin_cannot_reset_analytics_data()
    {
        $nonAdmin = User::factory()->create([
            'role' => 'author',
        ]);

        // Act as non-admin
        $response = $this->actingAs($nonAdmin)
            ->post(route('admin.settings.reset-analytics'));

        // Assert forbidden/unauthorized redirect (or whatever the app middleware does)
        // Usually it redirects or throws 403. Let's see what the admin middleware does.
        $response->assertStatus(403);
    }
}
