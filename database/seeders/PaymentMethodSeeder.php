<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add more permissions inside this array
        $payment_methods = [
            'Thawani',
            'Apple Pay',
            'Google Pay',
        ];

        $this->processData($payment_methods);
    }

    public function processData($data){
        foreach ($data as $payment_method) {
            PaymentMethod::firstOrCreate(['name' => $payment_method]);
        }
    }
}


