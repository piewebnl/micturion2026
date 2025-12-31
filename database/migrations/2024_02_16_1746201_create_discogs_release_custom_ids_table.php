<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discogs_release_custom_ids', function (Blueprint $table) {

            $table->id();
            $table->string('persistent_album_id');
            $table->string('release_id');
            $table->string('artist')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discogs_release_custom_ids');
    }
};
