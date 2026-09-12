<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('registration_code')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('gender');
            $table->string('city')->nullable();
            $table->string('organization')->nullable();
            $table->text('notes')->nullable();
            $table->string('referral_source')->nullable();
            $table->timestamp('data_consent_at');
            $table->string('status')->default('registered');
            $table->string('invitation_status')->default('pending');
            $table->timestamp('invitation_sent_at')->nullable();
            $table->unsignedInteger('invitation_send_count')->default(0);
            $table->text('last_invitation_error')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'email']);
            $table->index(['event_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
