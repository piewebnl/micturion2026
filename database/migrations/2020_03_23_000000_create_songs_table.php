<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('track_id')->nullable(); // Itunes ID

            $table->string('persistent_id', 16)->unique();
            $table->string('name');
            // $table->unsignedInteger('year')->nullable(); // to album
            $table->unsignedInteger('rating')->nullable();

            $table->unsignedInteger('track_number')->nullable();
            $table->unsignedInteger('track_count')->nullable();
            $table->unsignedInteger('disc_number')->nullable();
            $table->unsignedInteger('disc_count')->nullable();

            $table->unsignedInteger('favourite')->nullable();

            $table->string('grouping')->nullable();

            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');

            $table->string('album_artist')->nullable();
            $table->string('sort_album_artist')->nullable();

            $table->string('time', 10)->nullable();
            $table->string('time_ms', 10)->nullable();
            $table->unsignedBigInteger('play_count')->nullable();

            $table->text('comments', 255)->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('kind')->nullable();

            $table->boolean('has_changed')->nullable();

            $table->string('date_added', 50);
            $table->string('date_modified', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
