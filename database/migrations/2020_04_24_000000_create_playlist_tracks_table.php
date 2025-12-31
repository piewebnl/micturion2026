<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_tracks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('playlist_id')->constrained('playlists')->onDelete('cascade');

            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');

            // $table->string('persistent_id', 16);

            $table->unsignedTinyInteger('has_changed')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_tracks');
    }
};
