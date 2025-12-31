<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('sort_name')->nullable();
            $table->string('rating', 3)->nullable();
            $table->string('persistent_id')->unique();
            $table->unsignedBigInteger('play_count')->nullable();

            $table->unsignedInteger('year')->nullable();
            $table->string('location')->nullable();

            $table->foreignId('genre_id')->nullable()->constrained('genres')->onDelete('cascade');

            $table->boolean('is_compilation')->nullable();

            $table->text('notes')->nullable();

            $table->string('date_added', 50);
            $table->string('date_modified', 50);

            $table->foreignId('category_id')->constrained('categories');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
