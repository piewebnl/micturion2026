<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_tracks', function (Blueprint $table) {
            $table->id();

            $table->string('spotify_api_track_id')->unique()->nullable();
            $table->string('spotify_api_album_id')->nullable();
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('track_number')->nullable();
            $table->unsignedInteger('disc_number')->nullable();
            $table->string('artwork_url')->nullable();


            $table->string('search_name')->nullable();
            $table->string('search_album')->nullable();
            $table->string('search_artist')->nullable();


            $table->string('status');
            $table->unsignedInteger('score')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_tracks');
    }
};
