<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(['id' => '1', 'name' => 'Albums', 'format_match' => '(Album)', 'image_type' => 'album', 'order' => 1]);
        DB::table('categories')->insert(['id' => '2', 'name' => 'EPs', 'format_match' => '(EP)', 'image_type' => 'album', 'order' => 2]);
        DB::table('categories')->insert(['id' => '3', 'name' => 'Singles', 'format_match' => '(Single)', 'image_type' => 'album', 'order' => 3]);
        DB::table('categories')->insert(['id' => '4', 'name' => 'Videos', 'format_match' => '(Video)', 'image_type' => 'video', 'order' => 4]);
        DB::table('categories')->insert(['id' => '5', 'name' => 'Bootlegs', 'format_match' => '(Bootleg)', 'image_type' => 'album', 'order' => 5]);
        DB::table('categories')->insert(['id' => '6', 'name' => 'Video Bootlegs', 'format_match' => '(Video-Bootleg)', 'image_type' => 'video', 'order' => 6]);
        DB::table('categories')->insert(['id' => '7', 'name' => 'Songs', 'format_match' => '(Song)', 'image_type' => 'album', 'order' => 7]);
    }
}
