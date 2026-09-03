<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('distance_km', 8, 2)->default(0);
            $table->decimal('base_fee', 10, 2)->default(0);
            $table->decimal('rate_per_km', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_cities');
    }
};
