<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('My Blog');
            $table->string('site_tagline')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->string('contact_email')->nullable();

            // Social Media
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();

            // Blog Settings
            $table->unsignedInteger('posts_per_page')->default(10);
            $table->string('default_meta_title')->nullable();
            $table->text('default_meta_description')->nullable();

            // Analytics & Tracking
            $table->string('google_analytics')->nullable();
            $table->string('google_tag_manager')->nullable();

            // Theme / Niche settings (makes the blog "universal")
            $table->string('site_niche')->default('general'); // tech, sports, education, etc.
            $table->string('site_accent_color')->default('#3b82f6');
            $table->string('site_font')->default('Inter');
            $table->enum('site_layout', ['grid', 'list', 'magazine'])->default('grid');
            $table->string('default_og_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
