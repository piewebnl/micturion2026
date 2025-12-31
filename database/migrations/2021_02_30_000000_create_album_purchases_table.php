
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('album_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
            $table->string('persistent_album_id')->nullable();

            $table->date('year')->nullable();
            $table->date('month')->nullable();
            $table->date('day')->nullable();

            $table->string('price', 10)->nullable();

            $table->foreignId('format_id')->nullable()->constrained('formats')->onDelete('cascade');

            $table->foreignId('music_store_id')->constrained('music_stores');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_purchases');
    }
};
