<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concert_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('concert_id')->nullable()->constrained('concerts')->onDelete('cascade');

            $table->foreignId('concert_artist_id')->nullable()->constrained('concert_artists')->onDelete('cascade');

            $table->boolean('support')->nullable();
            $table->string('setlistfm_url')->nullable();

            $table->string('image_url')->nullable();

            $table->unsignedInteger('order')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concert_items');
    }
};
