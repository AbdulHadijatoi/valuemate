<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\ValuationRequestStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValuationRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('valuation_request_statuses')->truncate();
        ValuationRequestStatus::insert([
            ['name' => 'Pending'],
            ['name' => 'Payment Attempted'],
            ['name' => 'In Progress'],
            ['name' => 'Completed'],
            ['name' => 'Rejected'],
            ['name' => 'Confirmed'],
            ['name' => 'Under Review'],
        ]);
    }

}


