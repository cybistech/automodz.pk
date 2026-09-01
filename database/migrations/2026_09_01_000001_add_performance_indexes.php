<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured', 'created_at'], 'products_active_featured_created_idx');
            $table->index(['is_active', 'created_at'], 'products_active_created_idx');
            $table->index(['is_active', 'brand'], 'products_active_brand_idx');
            $table->index(['is_active', 'vehicle_make'], 'products_active_vehicle_make_idx');
            $table->index(['is_active', 'category_id'], 'products_active_category_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
            $table->index('payment_status', 'orders_payment_status_idx');
            $table->index('status', 'orders_status_idx');
            $table->index('customer_email', 'orders_customer_email_idx');
            $table->index('customer_phone', 'orders_customer_phone_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_featured_created_idx');
            $table->dropIndex('products_active_created_idx');
            $table->dropIndex('products_active_brand_idx');
            $table->dropIndex('products_active_vehicle_make_idx');
            $table->dropIndex('products_active_category_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_created_idx');
            $table->dropIndex('orders_payment_status_idx');
            $table->dropIndex('orders_status_idx');
            $table->dropIndex('orders_customer_email_idx');
            $table->dropIndex('orders_customer_phone_idx');
        });
    }
};
