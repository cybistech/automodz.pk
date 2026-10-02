<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('order_number');
            $table->timestamp('whatsapp_confirmation_sent_at')->nullable()->after('notes');
            $table->timestamp('whatsapp_confirmed_at')->nullable()->after('whatsapp_confirmation_sent_at');
            $table->timestamp('whatsapp_shipped_sent_at')->nullable()->after('whatsapp_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_number',
                'whatsapp_confirmation_sent_at',
                'whatsapp_confirmed_at',
                'whatsapp_shipped_sent_at',
            ]);
        });
    }
};
