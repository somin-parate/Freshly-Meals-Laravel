<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::truncate();

        $cities =  [
            [
                'city' => 'Abu Dhabi',
                'code' => 'AUH',
            ],
            [
                'city' => 'Sharjah',
                'code' => 'SHJ',
            ],
            [
                'city' => 'Al Ain',
                'code' => 'AIN',
            ],
            [
                'city' => 'Ajman',
                'code' => 'AJM',
            ],
            [
                'city' => 'Rak',
                'code' => 'RAK',
            ],
            [
                'city' => 'Fujairah',
                'code' => 'FUJ',
            ],
            [
                'city' => 'UAQ',
                'code' => 'UAQ',
            ],
            [
                'city' => 'Al Ghadeer',
                'code' => 'Al Ghadeer',
            ],
        ];

        City::insert($cities);
    }
}
