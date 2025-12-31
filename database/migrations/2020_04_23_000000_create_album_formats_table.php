<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('album_formats', function (Blueprint $table) {

            $table->id();

            $table->foreignId('album_id')->nullable()->constrained('albums')->onDelete('cascade');

            $table->foreignId('format_id')->nullable()->constrained('formats')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_formats');
    }
};
