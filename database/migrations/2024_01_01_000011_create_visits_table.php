<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 5)->nullable();
            $table->string('device')->nullable();     // mobile, tablet, desktop
            $table->string('browser')->nullable();    // Chrome, Firefox, Safari
            $table->string('referer')->nullable();    // Where visitor came from
            $table->timestamp('visited_at')->useCurrent();

            $table->index(['url', 'visited_at']);
            $table->index('visited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
