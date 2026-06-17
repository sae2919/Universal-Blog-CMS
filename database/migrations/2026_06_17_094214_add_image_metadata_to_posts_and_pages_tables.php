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
            $table->json('image_metadata')->nullable()->after('content');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->json('image_metadata')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image_metadata');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('image_metadata');
        });
    }
};
