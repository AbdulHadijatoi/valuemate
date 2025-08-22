<?php

use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\ServiceTypeController;
use App\Mail\PaymentSuccessMail;
use App\Mail\StatusUpdatedMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('privacy-policy', [GuidelineController::class, 'showPrivacyPolicy'])->name('service_type.get_data');
Route::get('support', function(){
    return view('contact-us');
});

// Route::get('/ttt', function () {
//     $valuationRequest = \App\Models\ValuationRequest::with([
//         'company.companyDetails',
//         'user',
//         'propertyType',
//         'serviceType',
//         'requestType',
//         'location',
//         'servicePricing',
//         'status',
//         'lastPayment'
//     ])->find(1);

//     if (!$valuationRequest) {
//         return 'ValuationRequest not found.';
//     }

//     $emailData = [
//         'id' => $valuationRequest->id,
//         'company_name' => optional($valuationRequest->company)->name ?? '-',
//         'user_name' => optional($valuationRequest->user)->first_name . ' ' . optional($valuationRequest->user)->last_name,
//         'property_type' => optional($valuationRequest->propertyType)->name ?? '-',
//         'service_type' => optional($valuationRequest->serviceType)->name ?? '-',
//         'request_type' => optional($valuationRequest->requestType)->name ?? '-',
//         'location' => optional($valuationRequest->location)->name ?? '-',
//         'service_pricing' => optional($valuationRequest->servicePricing)->price ?? 'default',
//         'area' => $valuationRequest->area ?? '-',
//         'total_amount' => $valuationRequest->total_amount ?? '-',
//         'status' => optional($valuationRequest->status)->name ?? '-',
//         'reference' => $valuationRequest->reference ?? '-',
//         'created_at_date' => $valuationRequest->created_at ? Carbon::parse($valuationRequest->created_at)->format('Y-m-d') : null,
//         'created_at_time' => $valuationRequest->created_at ? Carbon::parse($valuationRequest->created_at)->format('H:i:s') : null,
//         'payment_status' => optional($valuationRequest->lastPayment)->status ?? null,
//     ];

//     try {
//         if ($valuationRequest->user && $valuationRequest->user->email) {
//             Mail::to($valuationRequest->user->email)->send(new PaymentSuccessMail($emailData, 'user'));
//         }
//     } catch (Exception $e) {
//         Log::error('Failed to send email to user: ' . $e->getMessage());
//     }

//     try {
//         $company_email = optional(optional($valuationRequest->company)->companyDetails)->email;
//         if ($company_email) {
//             Mail::to($company_email)->send(new PaymentSuccessMail($emailData, 'company'));
//         }
//     } catch (Exception $e) {
//         Log::error('Failed to send email to company: ' . $e->getMessage());
//     }

//     try {
//         $admin_email = \App\Models\Setting::getValue('admin_email');
//         if ($admin_email) {
//             Mail::to($admin_email)->send(new PaymentSuccessMail($emailData, 'admin'));
//         }
//     } catch (Exception $e) {
//         Log::error('Failed to send email to admin: ' . $e->getMessage());
//     }

//     return 'Test emails sent to user, company, and admin (if emails exist).';
// });
