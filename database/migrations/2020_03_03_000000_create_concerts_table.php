<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concerts', function (Blueprint $table) {

            $table->id();

            $table->date('date')->nullable();

            $table->foreignId('concert_venue_id')->constrained('concert_venues')->onDelete('cascade');

            $table->foreignId('concert_festival_id')->nullable()->constrained('concert_festivals')->onDelete('cascade');

            $table->string('notes')->nullable();

            $table->boolean('festival')->nullable();

            $table->boolean('support')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concerts');
    }
};
