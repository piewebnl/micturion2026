<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('parent_name')->nullable();
            $table->string('persistent_id');
            $table->string('parent_persistent_id')->nullable();
            $table->unsignedTinyInteger('has_changed')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
