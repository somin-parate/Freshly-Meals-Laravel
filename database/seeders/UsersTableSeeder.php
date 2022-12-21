<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->where('email', 'freshlyuser@gmail.com')->delete();

        DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'freshlyuser@gmail.com',
            'password' => bcrypt('freshlyuser@123'),
            'type' => 'admin',
        ]);
    }
}
