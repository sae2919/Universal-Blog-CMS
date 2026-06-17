<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
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

    public function test_admin_can_update_ai_system_instruction_setting()
    {
        $customInstruction = "You are a witty, code-savvy assistant. Generate articles with a humorous tone.";

        $response = $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'site_name' => 'Updated Blog Name',
                'site_niche' => 'technology',
                'site_accent_color' => 'indigo',
                'site_font' => 'font-sans',
                'site_layout' => 'grid',
                'ai_system_instruction' => $customInstruction,
            ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success', 'Settings saved!');

        // Check value in DB
        $setting = Setting::first();
        $this->assertEquals($customInstruction, $setting->ai_system_instruction);

        // Check cache retrieval
        $this->assertEquals($customInstruction, Setting::getValue('ai_system_instruction'));
    }
}
