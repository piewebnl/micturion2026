<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiermaker_albums', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('tiermaker_id');
            $table->foreign('tiermaker_id')->references('id')->on('tiermaker_artists')->onDelete('cascade');

            $table->string('album_persistent_id')->index();
            $table->foreign('album_persistent_id')->references('persistent_id')->on('albums')->onDelete('cascade');

            $table->unsignedInteger('order')->default(0); // optional per-item order
            $table->string('tier', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiermaker_albums');
    }
};
