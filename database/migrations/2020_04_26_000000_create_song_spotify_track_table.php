<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_spotify_track', function (Blueprint $table) {
            $table->id();

            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');

            $table->foreignId('spotify_track_id')->nullable()->constrained('spotify_tracks')->onDelete('cascade');

            /*
            $table->unsignedBigInteger('spotify_track_custom_id');
            $table->foreign('spotify_track_custom_id')->references('id')->on('spotify_track_custom_ids')->nullable();
            */

            $table->unsignedInteger('score', null)->nullable();
            $table->string('status'); // valid, warning, invalid

            $table->string('search_artist')->nullable();
            $table->string('search_album')->nullable();
            $table->string('search_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_spotify_track');
    }
};
