<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_playlist_tracks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('spotify_playlist_id')->constrained('spotify_playlists')->onDelete('cascade');

            $table->foreignId('spotify_track_id')->constrained('spotify_tracks')->onDelete('cascade');

            $table->unsignedTinyInteger('has_changed')->nullable();
            $table->bigInteger('order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_playlist_tracks');
    }
};
