<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use RefreshDatabase;
    
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin','guard_name' => 'api']);       
        $userRole = Role::firstOrCreate(['name' => 'client','guard_name' => 'api']);

        // Create a default user for foreign keys (if not already created)
        if (!User::find(1)) {
            $user = User::factory()->create([
                'id' => 1, 
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'), // Use bcrypt for password hashing
                'first_name' => 'Talal',
            ]);

            // assign role to user
            $user->assignRole($adminRole);
        }

        // Locations
        DB::table('locations')->insert([
            ['name' => 'Dubai'],
            ['name' => 'Abu Dhabi'],
            ['name' => 'Sharjah'],
        ]);

        // Property Types
        DB::table('property_types')->insert([
            ['name' => 'Houses'],
            ['name' => 'Apartments'],
            ['name' => 'Residential Land'],
            ['name' => 'Agricultural Land'],
            ['name' => 'Industrial Land'],
            ['name' => 'Commercial Land'],
            ['name' => 'Commercial Shops'],
            ['name' => 'Commercial Buildings'],
            ['name' => 'Residential Complexes'],
            ['name' => 'Commercial Complexes'],
            ['name' => 'Warehouses'],
            ['name' => 'Factories']
        ]);

        // Service Types
        DB::table('service_types')->insert([
            ['name' => 'Construction'],
            ['name' => 'Purchase'],
        ]);

        // Settings
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'ValuationPro'],
            ['key' => 'currency', 'value' => 'AED'],
        ]);

        // Valuation Request Statuses
        DB::table('valuation_request_statuses')->insert([
            ['name' => 'Pending'],
            ['name' => 'In Progress'],
            ['name' => 'Completed'],
            ['name' => 'Rejected'],
        ]);

        // Payment Methods
        DB::table('payment_methods')->insert([
            ['name' => 'Thawani'],
            ['name' => 'Apple Pay'],
            ['name' => 'Google Pay'],
        ]);

        // Companies
        DB::table('companies')->insert([
            ['name' => 'Valuators Inc.', 'status' => 'active'],
            ['name' => 'Real Estate Experts', 'status' => 'active'],
        ]);

        // Company Details
        DB::table('company_details')->insert([
            [
                'company_id' => 1,
                'address' => '123 Business Street, Dubai',
                'email' => 'info@valuators.com',
                'phone' => '0501234567',
                'website' => 'https://valuators.com',
            ],
            [
                'company_id' => 2,
                'address' => '456 Estate Ave, Abu Dhabi',
                'email' => 'contact@reexperts.ae',
                'phone' => '0507654321',
                'website' => 'https://reexperts.ae',
            ],
        ]);

        // Files
        DB::table('files')->insert([
            ['path' => 'documents/file1.pdf', 'type' => 'document'],
            ['path' => 'images/photo1.jpg', 'type' => 'image'],
        ]);

        // Properties
        DB::table('properties')->insert([
            ['property_type_id' => 1, 'area' => 1200.50, 'location_id' => 1, 'status' => 'available'],
            ['property_type_id' => 2, 'area' => 3000.00, 'location_id' => 2, 'status' => 'sold'],
        ]);

        // Areas
        DB::table('areas')->insert([
            ['name' => 'Downtown', 'location_id' => 1],
            ['name' => 'Al Reem Island', 'location_id' => 2],
        ]);

        // Banner Ads
        DB::table('banner_ads')->insert([
            [
                'title' => 'Summer Offer',
                'file_id' => 2,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'ad_type' => 'monthly',
            ],
        ]);

        // Chats
        DB::table('chats')->insert([
            ['user_id' => 1, 'message' => 'How do I request a valuation?', 'status' => 'pending'],
        ]);

        // Document Requirements
        DB::table('document_requirements')->insert([
            ['property_type_id' => 1, 'document_name' => 'Title Deed'],
            ['property_type_id' => 2, 'document_name' => 'Utility Bill'],
        ]);

        // Invoices
        DB::table('invoices')->insert([
            [
                'user_id' => 1,
                'company_id' => 1,
                'amount' => 1500.00,
                'transaction_number' => Str::random(10),
                'status' => 'paid',
            ],
        ]);

        // Notifications
        DB::table('notifications')->insert([
            ['user_id' => 1, 'message' => 'Your valuation request has been completed.', 'status' => 'unread'],
        ]);

        // Payments
        DB::table('payments')->insert([
            [
                'payment_method_id' => 1,
                'amount' => 1500.00,
                'status' => 'completed',
                'payment_reference' => Str::random(12),
            ],
        ]);

        // Pricing Rules
        DB::table('pricing_rules')->insert([
            ['property_type_id' => 1, 'area_range' => '1000-1500', 'price' => 500.00],
            ['property_type_id' => 2, 'area_range' => '2000-3000', 'price' => 1200.00],
        ]);

        // Valuation Requests
        DB::table('valuation_requests')->insert([
            [
                'company_id' => 1,
                'user_id' => 1,
                'status_id' => 1,
                'property_type_id' => 1,
                'area' => '1200',
                'location_id' => 1,
                'pricing_rule_id' => 1,
                'total_amount' => 500.00,
            ],
        ]);

        // Valuation Request Details
        DB::table('valuation_request_details')->insert([
            ['valuation_request_id' => 1, 'file_id' => 1],
        ]);

        
    }
}
