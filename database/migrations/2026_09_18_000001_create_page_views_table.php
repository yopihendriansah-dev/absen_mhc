<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table): void {
            $table->id();
            $table->string('url');
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');
        });

        Schema::create('daily_page_view_stats', function (Blueprint $table): void {
            $table->id();
            $table->date('date');
            $table->string('url');
            $table->integer('views')->default(0);
            $table->unique(['date', 'url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_page_view_stats');
        Schema::dropIfExists('page_views');
    }
};
