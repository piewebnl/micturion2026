<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(UserSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ConcertVenueSeeder::class);
        $this->call(ConcertArtistSeeder::class);
        $this->call(ConcertFestivalSeeder::class);
        $this->call(ConcertSeeder::class);
        $this->call(ConcertItemSeeder::class);
        $this->call(MusicStoreSeeder::class);
        $this->call(WishlistAlbumSeeder::class);
        $this->call(SpotifyAlbumCustomIdSeeder::class);
        $this->call(SpotifyAlbumUnavailableSeeder::class);
        $this->call(SpotifyTrackCustomIdSeeder::class);
        $this->call(SpotifyTrackUnavailableSeeder::class);
        $this->call(DiscogsReleaseCustomIdSeeder::class);
    }
}
