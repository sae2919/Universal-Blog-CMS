@extends('layouts.admin')

@section('title', 'System Settings')
@section('header', 'System Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">System Configuration</h2>
        <p class="text-sm text-gray-500 mt-1">Configure global variables, social links, SEO defaults, Google Analytics, and theme layout.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Section 1: General Settings --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
            <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                 General Settings
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_name" class="block text-sm font-semibold text-gray-700">Site Name</label>
                    <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings->site_name) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('site_name') border-red-300 focus:ring-red-500 @enderror">
                    @error('site_name') <p class="mt-1 text-xs text-red-650">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="site_tagline" class="block text-sm font-semibold text-gray-700">Site Tagline</label>
                    <input type="text" name="site_tagline" id="site_tagline" value="{{ old('site_tagline', $settings->site_tagline) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_email" class="block text-sm font-semibold text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings->contact_email) }}"
                           placeholder="support@findmyguru.com"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="posts_per_page" class="block text-sm font-semibold text-gray-700">Posts Per Page (Pagination)</label>
                    <input type="number" name="posts_per_page" id="posts_per_page" value="{{ old('posts_per_page', $settings->posts_per_page ?? 10) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_phone" class="block text-sm font-semibold text-gray-700">Contact Phone</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}"
                           placeholder="+917680097094"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="office_address" class="block text-sm font-semibold text-gray-700">Office Address</label>
                    <textarea name="office_address" id="office_address" rows="2"
                              placeholder="501, Manjeera Majestic Commercial, KPHB, Hyderabad, India - 500072"
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('office_address', $settings->office_address) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                <div>
                    <label for="site_logo" class="block text-sm font-semibold text-gray-700">Site Logo</label>
                    @if($settings->site_logo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $settings->site_logo) }}" class="h-10 object-contain rounded border border-gray-200 p-1 bg-gray-50" alt="">
                        </div>
                    @endif
                    <input type="file" name="site_logo" id="site_logo" class="mt-2 block w-full text-xs text-gray-500 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div>
                    <label for="site_favicon" class="block text-sm font-semibold text-gray-700">Favicon</label>
                    @if($settings->site_favicon)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $settings->site_favicon) }}" class="w-8 h-8 object-contain rounded border border-gray-200 p-1 bg-gray-50" alt="">
                        </div>
                    @endif
                    <input type="file" name="site_favicon" id="site_favicon" class="mt-2 block w-full text-xs text-gray-500 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div>
                    <label for="default_og_image" class="block text-sm font-semibold text-gray-700">Default Social (OG) Share Image</label>
                    @if($settings->default_og_image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $settings->default_og_image) }}" class="h-10 object-cover rounded border border-gray-200 p-1 bg-gray-50" alt="">
                        </div>
                    @endif
                    <input type="file" name="default_og_image" id="default_og_image" class="mt-2 block w-full text-xs text-gray-500 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
        </div>

        {{-- Section 2: Personalization & Layout Niche --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
            <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                 Universal Personalization
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="site_niche" class="block text-sm font-semibold text-gray-700">Blog Niche / Topic</label>
                    <select name="site_niche" id="site_niche" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="technology" {{ old('site_niche', $settings->site_niche) === 'technology' ? 'selected' : '' }}>Technology</option>
                        <option value="education" {{ old('site_niche', $settings->site_niche) === 'education' ? 'selected' : '' }}>Education</option>
                        <option value="sports" {{ old('site_niche', $settings->site_niche) === 'sports' ? 'selected' : '' }}>Sports</option>
                        <option value="travel" {{ old('site_niche', $settings->site_niche) === 'travel' ? 'selected' : '' }}>Travel</option>
                        <option value="news" {{ old('site_niche', $settings->site_niche) === 'news' ? 'selected' : '' }}>General News</option>
                    </select>
                </div>

                <div>
                    <label for="site_accent_color" class="block text-sm font-semibold text-gray-700">Theme Accent Color</label>
                    <select name="site_accent_color" id="site_accent_color" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="indigo" {{ old('site_accent_color', $settings->site_accent_color) === 'indigo' ? 'selected' : '' }}>Indigo Blue</option>
                        <option value="emerald" {{ old('site_accent_color', $settings->site_accent_color) === 'emerald' ? 'selected' : '' }}>Emerald Green</option>
                        <option value="rose" {{ old('site_accent_color', $settings->site_accent_color) === 'rose' ? 'selected' : '' }}>Rose Red</option>
                        <option value="sky" {{ old('site_accent_color', $settings->site_accent_color) === 'sky' ? 'selected' : '' }}>Sky Blue</option>
                        <option value="amber" {{ old('site_accent_color', $settings->site_accent_color) === 'amber' ? 'selected' : '' }}>Amber Orange</option>
                    </select>
                </div>

                <div>
                    <label for="site_font" class="block text-sm font-semibold text-gray-700">Typography / Font</label>
                    <select name="site_font" id="site_font" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="font-sans" {{ old('site_font', $settings->site_font) === 'font-sans' ? 'selected' : '' }}>System Sans</option>
                        <option value="font-serif" {{ old('site_font', $settings->site_font) === 'font-serif' ? 'selected' : '' }}>Elegant Serif</option>
                        <option value="font-mono" {{ old('site_font', $settings->site_font) === 'font-mono' ? 'selected' : '' }}>Console Mono</option>
                    </select>
                </div>

                <div>
                    <label for="site_layout" class="block text-sm font-semibold text-gray-700">Frontend Post Grid Layout</label>
                    <select name="site_layout" id="site_layout" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="grid" {{ old('site_layout', $settings->site_layout) === 'grid' ? 'selected' : '' }}>Grid Cards</option>
                        <option value="list" {{ old('site_layout', $settings->site_layout) === 'list' ? 'selected' : '' }}>Vertical Lists</option>
                        <option value="magazine" {{ old('site_layout', $settings->site_layout) === 'magazine' ? 'selected' : '' }}>Magazine Flow</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 3: SEO Defaults --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
            <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                 Global SEO Metadata
            </h3>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="default_meta_title" class="block text-sm font-semibold text-gray-700">Default Title Tag</label>
                    <input type="text" name="default_meta_title" id="default_meta_title" value="{{ old('default_meta_title', $settings->default_meta_title) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="default_meta_description" class="block text-sm font-semibold text-gray-700">Default Meta Description</label>
                    <textarea name="default_meta_description" id="default_meta_description" rows="3"
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('default_meta_description', $settings->default_meta_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 4: Social Links --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
            <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                 Social Media Profiles
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="facebook" class="block text-sm font-semibold text-gray-700">Facebook URL</label>
                    <input type="url" name="facebook" id="facebook" value="{{ old('facebook', $settings->facebook) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="twitter" class="block text-sm font-semibold text-gray-700">Twitter URL</label>
                    <input type="url" name="twitter" id="twitter" value="{{ old('twitter', $settings->twitter) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="linkedin" class="block text-sm font-semibold text-gray-700">LinkedIn URL</label>
                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $settings->linkedin) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="instagram" class="block text-sm font-semibold text-gray-700">Instagram URL</label>
                    <input type="url" name="instagram" id="instagram" value="{{ old('instagram', $settings->instagram) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="youtube" class="block text-sm font-semibold text-gray-700">YouTube Channel URL</label>
                    <input type="url" name="youtube" id="youtube" value="{{ old('youtube', $settings->youtube) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Section 5: Analytics --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
            <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                 Third Party Analytics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="google_analytics" class="block text-sm font-semibold text-gray-700">Google Analytics Tracking Code (GA4)</label>
                    <textarea name="google_analytics" id="google_analytics" rows="4" placeholder="e.g. <!-- Google Tag (gtag.js) -->..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs">{{ old('google_analytics', $settings->google_analytics) }}</textarea>
                </div>

                <div>
                    <label for="google_tag_manager" class="block text-sm font-semibold text-gray-700">Google Tag Manager Header Code</label>
                    <textarea name="google_tag_manager" id="google_tag_manager" rows="4" placeholder="e.g. (function(w,d,s,l,i){w[l]=w[l]||[]..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs">{{ old('google_tag_manager', $settings->google_tag_manager) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 6: Global Call-to-Action (CTA) --}}
        <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6 space-y-6">
            <h3 class="text-md font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                 Global Call-to-Action (CTA)
            </h3>
            <p class="text-xs text-gray-400">This CTA will be displayed at the bottom of all pages across your website (e.g. homepage, category index, tag index, pages). Leave these fields blank to disable it.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="global_cta_title" class="block text-sm font-semibold text-gray-700">CTA Title</label>
                    <input type="text" name="global_cta_title" id="global_cta_title" value="{{ old('global_cta_title', $settings->global_cta_title) }}" placeholder="e.g. Find Expert Mentors & Tutors on FindMyGuru"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="global_cta_bg_image" class="block text-sm font-semibold text-gray-700">CTA Background Image</label>
                    @if($settings->global_cta_bg_image)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset('storage/' . $settings->global_cta_bg_image) }}" class="h-14 object-cover rounded border border-gray-200 p-1 bg-gray-50" alt="">
                        </div>
                    @endif
                    <input type="file" name="global_cta_bg_image" id="global_cta_bg_image" class="mt-2 block w-full text-xs text-gray-500 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="global_cta_description" class="block text-sm font-semibold text-gray-700">CTA Description</label>
                    <textarea name="global_cta_description" id="global_cta_description" rows="3" placeholder="Brief description of the call-to-action..."
                              class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('global_cta_description', $settings->global_cta_description) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="global_cta_button_text" class="block text-sm font-semibold text-gray-700">CTA Button Text</label>
                    <input type="text" name="global_cta_button_text" id="global_cta_button_text" value="{{ old('global_cta_button_text', $settings->global_cta_button_text) }}" placeholder="e.g. Find Tutors Near Me"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="global_cta_button_link" class="block text-sm font-semibold text-gray-700">CTA Button Link</label>
                    <input type="text" name="global_cta_button_link" id="global_cta_button_link" value="{{ old('global_cta_button_link', $settings->global_cta_button_link) }}" placeholder="e.g. /search"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-250">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
                Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
