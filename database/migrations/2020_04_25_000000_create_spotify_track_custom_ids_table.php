<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_track_custom_ids', function (Blueprint $table) {
            $table->id();
            $table->string('persistent_id');
            $table->string('spotify_api_track_custom_id');
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_track_custom_ids');
    }
};
