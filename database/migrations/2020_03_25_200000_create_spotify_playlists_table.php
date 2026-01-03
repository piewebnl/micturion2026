<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_playlists', function (Blueprint $table) {

            $table->id();

            $table->string('spotify_api_playlist_id'); // spotify playlist id?
            $table->string('name');
            $table->string('url')->nullable();
            // $table->string('external_url')->nullable();
            $table->string('tracks_url')->nullable();
            $table->integer('total_tracks')->nullable();
            $table->datetime('date')->nullable();
            $table->string('snapshot_id')->nullable();
            $table->unsignedInteger('snapshot_id_has_changed')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_playlists');
    }
};
