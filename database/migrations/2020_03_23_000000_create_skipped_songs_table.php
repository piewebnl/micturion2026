<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skipped_songs', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('track_id')->nullable(); // Itunes ID
            $table->string('persistent_id', 16)->unique();
            $table->string('name');

            $table->string('album_name');
            $table->string('artist_name');

            $table->unsignedTinyInteger('track_number')->nullable();
            $table->unsignedTinyInteger('track_count')->nullable();
            $table->unsignedTinyInteger('disc_number')->nullable();
            $table->unsignedTinyInteger('disc_count')->nullable();

            $table->string('grouping')->nullable();

            $table->string('time', 10)->nullable();
            $table->unsignedBigInteger('play_count')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();

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
        Schema::dropIfExists('skipped_songs');
    }
};
