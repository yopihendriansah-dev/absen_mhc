<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->timestamp('whatsapp_invitation_sent_at')->nullable()->after('invitation_send_count');
            $table->unsignedInteger('whatsapp_invitation_send_count')->default(0)->after('whatsapp_invitation_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->dropColumn([
                'whatsapp_invitation_sent_at',
                'whatsapp_invitation_send_count',
            ]);
        });
    }
};
