<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('global_cta_title')->nullable();
            $table->text('global_cta_description')->nullable();
            $table->string('global_cta_button_text')->nullable();
            $table->string('global_cta_button_link')->nullable();
            $table->string('global_cta_bg_image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'global_cta_title',
                'global_cta_description',
                'global_cta_button_text',
                'global_cta_button_link',
                'global_cta_bg_image',
            ]);
        });
    }
};
