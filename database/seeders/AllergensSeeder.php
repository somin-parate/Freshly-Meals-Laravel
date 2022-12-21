<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allergens;

class AllergensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Allergens::truncate();

        $allergens =  [
            [
                'icon' => 'mushrooms.png',
                'title' => 'MUSHROOMS',
            ],
            [
                'icon' => 'nuts.png',
                'title' => 'NUTS',
            ],
            [
                'icon' => 'milk.png',
                'title' => 'DAIRY',
            ],
            [
                'icon' => 'shellfish.png',
                'title' => 'SHELLFISH',
            ],
            [
                'icon' => 'eggs.png',
                'title' => 'EGG',
            ],
            [
                'icon' => 'soya.png',
                'title' => 'SOYA',
            ],
            [
                'icon' => 'sesame.png',
                'title' => 'SESAME SEEDS',
            ],
        ];

        Allergens::insert($allergens);
    }
}
