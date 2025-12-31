<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concert_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('concert_item_id')->nullable()->constrained('concert_items')->onDelete('cascade');

            $table->string('slug')->nullable();
            $table->integer('largest_width')->nullable();
            $table->integer('largest_height')->nullable();

            $table->string('hash')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concert_images');
    }
};
