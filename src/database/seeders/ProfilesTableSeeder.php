<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('profiles')->insert([
            [
                'user_id' => 1,
                'postcode' => '123-4567',
                'address' => '東京都新宿区新宿1丁目1-1',
                'building' => '新宿ビル',
            ],
            [
                'user_id' => 2,
                'postcode' => '234-5678',
                'address' => '大阪府大阪市中央区心斎橋2丁目2-2',
                'building' => '心斎橋タワー',
            ],
            [
                'user_id' => 3,
                'postcode' => '345-6789',
                'address' => '愛知県名古屋市中区名駅3丁目3-3',
                'building' => '名駅ビル',
            ],
        ]);
    }
}