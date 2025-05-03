<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            ChatSeeder::class,
            AreaSeeder::class,
            LocationSeeder::class,
            BannerAdSeeder::class,
            InvoiceSeeder::class,
            NotificationSeeder::class,
            DocumentRequirementSeeder::class,
            // Add any other model seeders you've made
        ]);
    }
}
