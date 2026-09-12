<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->text('description');
            $table->string('location_name');
            $table->text('location_address')->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('whatsapp_group_url')->nullable();
            $table->string('capacity_type')->default('unlimited');
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['status', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
