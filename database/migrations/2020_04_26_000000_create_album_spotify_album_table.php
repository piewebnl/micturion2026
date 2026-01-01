<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('album_spotify_album', function (Blueprint $table) {
            $table->id();

            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');

            $table->foreignId('spotify_album_id')->nullable()->constrained('spotify_albums')->onDelete('cascade');

            $table->unsignedInteger('score', null)->nullable();
            $table->string('status'); // valid, warning, invalid

            $table->string('search_artist')->nullable();
            $table->string('search_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_spotify_album');
    }
};
