<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_albums', function (Blueprint $table) {
            $table->id();

            $table->string('spotify_api_album_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('name_sanitized')->nullable();
            $table->string('artist')->nullable();
            $table->string('artwork_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_albums');
    }
};
