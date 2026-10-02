<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->boolean('send_order_confirmation')->default(true);
            $table->boolean('require_customer_confirm')->default(true);
            $table->boolean('send_shipped_update')->default(true);
            $table->string('phone_number_id')->nullable();
            $table->text('access_token')->nullable();
            $table->string('verify_token')->nullable();
            $table->string('app_secret')->nullable();
            $table->string('api_version', 16)->default('v21.0');
            $table->string('default_language', 10)->default('en');
            $table->string('order_confirmation_template')->nullable();
            $table->string('shipping_update_template')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
