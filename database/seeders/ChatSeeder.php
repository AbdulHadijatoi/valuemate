<?php

namespace Database\Seeders;

use App\Models\Chat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Chat::insert([
            ['user_id' => 2, 'message' => 'Hello, I need help with valuation.'],
            ['user_id' => 2, 'message' => 'Sure, please provide your request ID.'],
            ['user_id' => 3, 'message' => 'Can I pay via Thawani?'],
            ['user_id' => 3, 'message' => 'Yes, we support Thawani.'],
            ['user_id' => 4, 'message' => 'My document upload is failing.'],
        ]);
    }
}
