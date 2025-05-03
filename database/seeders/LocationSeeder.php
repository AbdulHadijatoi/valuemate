<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::insert([
            ['area_id' => 1, 'name' => 'Al Khuwair'],
            ['area_id' => 1, 'name' => 'Al Ghubrah'],
            ['area_id' => 2, 'name' => 'Dahariz'],
            ['area_id' => 3, 'name' => 'Falaj Al Qabail'],
            ['area_id' => 4, 'name' => 'Birkat Al Mouz'],
        ]);
    }
}
