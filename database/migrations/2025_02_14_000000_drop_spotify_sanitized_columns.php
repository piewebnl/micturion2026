<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('spotify_tracks', 'name_sanitized')) {
            Schema::table('spotify_tracks', function (Blueprint $table) {
                $table->dropColumn('name_sanitized');
            });
        }

        if (Schema::hasColumn('spotify_tracks', 'album_sanitized')) {
            Schema::table('spotify_tracks', function (Blueprint $table) {
                $table->dropColumn('album_sanitized');
            });
        }

        if (Schema::hasColumn('spotify_tracks', 'artist_sanitized')) {
            Schema::table('spotify_tracks', function (Blueprint $table) {
                $table->dropColumn('artist_sanitized');
            });
        }

        if (Schema::hasColumn('spotify_albums', 'name_sanitized')) {
            Schema::table('spotify_albums', function (Blueprint $table) {
                $table->dropColumn('name_sanitized');
            });
        }

        if (Schema::hasColumn('spotify_albums', 'artist_sanitized')) {
            Schema::table('spotify_albums', function (Blueprint $table) {
                $table->dropColumn('artist_sanitized');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('spotify_tracks', 'name_sanitized')) {
            Schema::table('spotify_tracks', function (Blueprint $table) {
                $table->string('name_sanitized')->nullable();
            });
        }

        if (!Schema::hasColumn('spotify_tracks', 'album_sanitized')) {
            Schema::table('spotify_tracks', function (Blueprint $table) {
                $table->string('album_sanitized')->nullable();
            });
        }

        if (!Schema::hasColumn('spotify_tracks', 'artist_sanitized')) {
            Schema::table('spotify_tracks', function (Blueprint $table) {
                $table->string('artist_sanitized')->nullable();
            });
        }

        if (!Schema::hasColumn('spotify_albums', 'name_sanitized')) {
            Schema::table('spotify_albums', function (Blueprint $table) {
                $table->string('name_sanitized')->nullable();
            });
        }

        if (!Schema::hasColumn('spotify_albums', 'artist_sanitized')) {
            Schema::table('spotify_albums', function (Blueprint $table) {
                $table->string('artist_sanitized')->nullable();
            });
        }
    }
};
