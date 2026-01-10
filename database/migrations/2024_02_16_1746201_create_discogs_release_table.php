<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discogs_releases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('release_id')->nullable();
            $table->foreignId('album_id')->nullable()->constrained('albums')->onDelete('cascade');
            $table->string('artist')->nullable();
            $table->string('title')->nullable();
            $table->string('format', 25)->nullable();
            $table->date('date')->nullable();
            $table->string('country')->nullable();
            $table->unsignedInteger('score', null)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('status_info', 20)->nullable();
            $table->unsignedInteger('lowest_price', null)->nullable();
            $table->text('comments')->nullable();
            $table->text('notes')->nullable();
            $table->string('url')->nullable();
            $table->string('hash')->nullable();
            $table->string('artwork_url')->nullable();
            $table->text('artwork_other_urls')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discogs_releases');
    }
};
