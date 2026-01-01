<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlist_album_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wishlist_album_id')->constrained('albums')->onDelete('cascade');

            $table->foreignId('music_store_id')->constrained('music_stores');

            $table->string('url')->nullable();
            $table->string('format', 10)->nullable();

            $table->float('price', 10, 2)->nullable()->index();
            $table->unsignedInteger('score', null)->nullable();
            // $table->unique(['wishlist_album_id', 'music_store_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_album_prices');
    }
};
