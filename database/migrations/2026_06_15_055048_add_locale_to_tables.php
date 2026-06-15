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
            $table->string('locale')->default('en')->after('category_id')->index();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('locale')->default('en')->after('slug')->index();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('locale')->default('en')->after('slug')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
