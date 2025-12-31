<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlist_albums', function (Blueprint $table) {
            $table->id();
            $table->string('persistent_album_id')->unique();
            $table->text('format')->nullable();
            $table->string('notes', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_albums');
    }
};
