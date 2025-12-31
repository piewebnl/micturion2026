<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([

            'name' => env('TEST_USER_NAME'),
            'email' => env('TEST_USER_EMAIL'),
            'password' => bcrypt(env('TEST_USER_PASSWORD')),
            'email_verified_at' => date('Y-m-d h:i:s'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ]);
    }
}
