<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->string('author_email')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_approved']);
            $table->unique(['product_id', 'author_email']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->default(0)->after('views');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['rating_avg', 'rating_count']);
        });

        Schema::dropIfExists('product_reviews');
    }
};
