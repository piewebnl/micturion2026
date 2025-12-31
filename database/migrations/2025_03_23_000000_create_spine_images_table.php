<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spine_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('album_id')->nullable()->constrained('albums')->onDelete('cascade');

            $table->string('slug')->nullable();
            $table->integer('largest_width')->nullable();
            $table->integer('largest_height')->nullable();

            $table->integer('checked')->default(0);

            $table->string('hash')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spine_images');
    }
};
