<?php

use App\Http\Controllers\ServiceTypeController;
use App\Mail\PaymentSuccessMail;
use App\Mail\StatusUpdatedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('test', [ServiceTypeController::class, 'getData'])->name('service_type.get_data');

Route::get('/test-emails', function () {
    $user = User::first();

    $mockData = [
        'id' => 1,
        'company_name' => 'Demo Company',
        'user_name' => $user->first_name . ' ' . $user->last_name,
        'property_type' => 'Apartment',
        'service_type' => 'Valuation',
        'request_type' => 'Urgent',
        'location' => 'Muscat',
        'service_pricing' => '100',
        'area' => '120 sqm',
        'total_amount' => '100 OMR',
        'status' => 'Confirmed',
        'reference' => 'REF123456',
        'created_at_date' => now()->format('Y-m-d'),
        'created_at_time' => now()->format('H:i:s'),
        'payment_status' => 'completed',
    ];

    $statusData = [
        'user_name' => $user->first_name . ' ' . $user->last_name,
        'reference' => 'REF123456',
        'status' => 'In Progress',
        'property_type' => 'Apartment',
        'location' => 'Muscat',
        'created_at_date' => now()->format('Y-m-d'),
        'created_at_time' => now()->format('H:i:s'),
    ];

    // Send both emails to your email address
    Mail::to($user->email)->send(new PaymentSuccessMail($mockData));
    Mail::to($user->email)->send(new StatusUpdatedMail($statusData));

    return 'Test emails sent to ' . $user->email;
});