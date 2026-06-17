<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first() ?? new Setting();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name'               => 'required|string|max:255',
            'site_tagline'            => 'nullable|string|max:255',
            'contact_email'           => 'nullable|email',
            'contact_phone'           => 'nullable|string|max:50',
            'office_address'          => 'nullable|string|max:1000',
            'facebook'                => 'nullable|url',
            'twitter'                 => 'nullable|url',
            'linkedin'                => 'nullable|url',
            'instagram'               => 'nullable|url',
            'youtube'                 => 'nullable|url',
            'posts_per_page'          => 'integer|min:1|max:100',
            'default_meta_title'      => 'nullable|string|max:255',
            'default_meta_description'=> 'nullable|string|max:500',
            'google_analytics'        => 'nullable|string',
            'google_tag_manager'      => 'nullable|string',
            'site_niche'              => 'required|string',
            'site_accent_color'       => 'required|string',
            'site_font'               => 'required|string',
            'site_layout'             => 'required|in:grid,list,magazine',
            'site_logo'               => 'nullable|image|max:2048',
            'site_favicon'            => 'nullable|image|max:512',
            'default_og_image'        => 'nullable|image|max:2048',
            'global_cta_title'        => 'nullable|string|max:255',
            'global_cta_description'  => 'nullable|string|max:1000',
            'global_cta_button_text'  => 'nullable|string|max:255',
            'global_cta_button_link'  => 'nullable|string|max:255',
            'global_cta_bg_image'     => 'nullable|image|max:2048',
            'ai_system_instruction'   => 'nullable|string|max:5000',
        ]);

        if ($request->hasFile('site_logo')) {
            $validated['site_logo'] = $request->file('site_logo')->store('settings', 'public');
        }
        if ($request->hasFile('site_favicon')) {
            $validated['site_favicon'] = $request->file('site_favicon')->store('settings', 'public');
        }
        if ($request->hasFile('default_og_image')) {
            $validated['default_og_image'] = $request->file('default_og_image')->store('settings', 'public');
        }
        if ($request->hasFile('global_cta_bg_image')) {
            $validated['global_cta_bg_image'] = $request->file('global_cta_bg_image')->store('settings', 'public');
        }

        Setting::updateOrCreate(['id' => 1], $validated);

        // Clear all cached settings
        Setting::clearCache();

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved!');
    }

    public function resetAnalytics()
    {
        // Delete all visit logs
        \App\Models\Visit::query()->delete();

        // Reset views count on all posts
        \App\Models\Post::query()->update(['views' => 0]);

        // Flush application cache to clear sidebar popular lists immediately
        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Analytics dashboard data has been reset to zero!');
    }
}
