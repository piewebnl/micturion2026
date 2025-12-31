<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_albums_unavailable', function (Blueprint $table) {
            $table->id();
            $table->string('persistent_id');
            $table->string('artist')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_albums_unavailable');
    }
};
