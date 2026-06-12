<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_url')->unique();
            $table->string('new_url');
            $table->integer('status_code')->default(301); // 301 Permanent, 302 Temporary
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hit_count')->default(0); // Track how often it's used
            $table->timestamps();

            $table->index(['old_url', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
