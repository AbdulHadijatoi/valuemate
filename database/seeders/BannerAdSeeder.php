<?php

namespace Database\Seeders;

use App\Models\BannerAd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BannerAd::insert([
            ['image' => 'images/ads/ad1.jpg', 'start_date' => now(), 'end_date' => now()->addWeek()],
            ['image' => 'images/ads/ad2.jpg', 'start_date' => now(), 'end_date' => now()->addMonth()],
            ['image' => 'images/ads/ad3.jpg', 'start_date' => now(), 'end_date' => now()->addDays(10)],
            ['image' => 'images/ads/ad4.jpg', 'start_date' => now(), 'end_date' => now()->addDays(5)],
            ['image' => 'images/ads/ad5.jpg', 'start_date' => now(), 'end_date' => null], // permanent
        ]);
    }
}
