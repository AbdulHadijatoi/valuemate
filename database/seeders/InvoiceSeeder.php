<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::insert([
            ['valuation_request_id' => 1, 'invoice_number' => 'INV001', 'total' => 25.000, 'file' => 'documents/invoices/INV001.pdf'],
            ['valuation_request_id' => 2, 'invoice_number' => 'INV002', 'total' => 50.000, 'file' => 'documents/invoices/INV002.pdf'],
            ['valuation_request_id' => 3, 'invoice_number' => 'INV003', 'total' => 75.000, 'file' => 'documents/invoices/INV003.pdf'],
            ['valuation_request_id' => 4, 'invoice_number' => 'INV004', 'total' => 25.000, 'file' => 'documents/invoices/INV004.pdf'],
            ['valuation_request_id' => 5, 'invoice_number' => 'INV005', 'total' => 50.000, 'file' => 'documents/invoices/INV005.pdf'],
        ]);
    }
}
