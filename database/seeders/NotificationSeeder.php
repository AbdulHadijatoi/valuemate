<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::insert([
            ['user_id' => 2, 'title' => 'Valuation Completed', 'message' => 'Your request #INV001 is complete.'],
            ['user_id' => 2, 'title' => 'Payment Received', 'message' => 'We received your payment.'],
            ['user_id' => 3, 'title' => 'Request Under Review', 'message' => 'We are reviewing your request.'],
            ['user_id' => 4, 'title' => 'New Message', 'message' => 'You have a new message from admin.'],
            ['user_id' => 5, 'title' => 'Documents Missing', 'message' => 'Please upload the missing documents.'],
        ]);
    }
}
