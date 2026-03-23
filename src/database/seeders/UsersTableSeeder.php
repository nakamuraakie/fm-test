<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => '山田 太郎',
                'email' => 'yamada.taro@example.com',
                'password' => bcrypt('password'),
                'profile_completed' => 1,
            ],
            [
                'name' => '佐藤 花子',
                'email' => 'sato.hanako@example.com',
                'password' => bcrypt('password'),
                'profile_completed' => 1,
            ],
            [
                'name' => '鈴木 一郎',
                'email' => 'suzuki.ichiro@example.com',
                'password' => bcrypt('password'),
                'profile_completed' => 1,
            ],
        ]);

    }
}


