<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiermaker_artists', function (Blueprint $table) {
            $table->id();

            $table->string('artist_name');
            $table->foreign('artist_name')->references('name')->on('artists')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiermaker_artists');
    }
};
