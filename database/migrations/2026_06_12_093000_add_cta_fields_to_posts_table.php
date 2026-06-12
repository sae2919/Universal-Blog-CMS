<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('cta_title')->nullable()->after('faqs');
            $table->text('cta_description')->nullable()->after('cta_title');
            $table->string('cta_button_text')->nullable()->after('cta_description');
            $table->string('cta_button_link')->nullable()->after('cta_button_text');
            $table->string('cta_bg_image')->nullable()->after('cta_button_link');
            
            $table->string('cta_directory_title')->nullable()->after('cta_bg_image');
            $table->string('cta_directory_subtitle')->nullable()->after('cta_directory_title');
            
            $table->string('cta_col1_title')->nullable()->after('cta_directory_subtitle');
            $table->text('cta_col1_links')->nullable()->after('cta_col1_title'); // Text format: "Text | Link" on each line
            
            $table->string('cta_col2_title')->nullable()->after('cta_col1_links');
            $table->text('cta_col2_links')->nullable()->after('cta_col2_title'); // Text format: "Text | Link" on each line
            
            $table->string('cta_col3_title')->nullable()->after('cta_col2_links');
            $table->text('cta_col3_links')->nullable()->after('cta_col3_title'); // Text format: "Text | Link" on each line
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'cta_title',
                'cta_description',
                'cta_button_text',
                'cta_button_link',
                'cta_bg_image',
                'cta_directory_title',
                'cta_directory_subtitle',
                'cta_col1_title',
                'cta_col1_links',
                'cta_col2_title',
                'cta_col2_links',
                'cta_col3_title',
                'cta_col3_links',
            ]);
        });
    }
};
