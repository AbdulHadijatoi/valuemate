<?php

namespace App\Http\Controllers;

use App\Constants\ValuationRequestStatusConstants;
use App\Mail\PaymentSuccessMail;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\User;
use App\Models\ValuationRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    
    public function getData() { 

        $data = Payment::get();

        $data = $data->map(function($item, $index) {
            $data = [];
            $data['index'] = $index + 1;
            $data['id'] = $item->id;
            $data['payment_method'] = $item->paymentMethod ? $item->paymentMethod->name : null;
            $data['valuation_request_id'] = $item->valuation_request_id;
            $data['amount'] = $item->amount;
            $data['status'] = $item->status;
            $data['payment_reference'] = $item->payment_reference;
            $data['user'] = $item->user ? $item->user->name : null;
            $data['thawani_payment_id'] = $item->thawani_payment_id;
            $data['thawani_session_id'] = $item->thawani_session_id;
            $data['created_at'] = optional($item->created_at)->format('Y-m-d H:i:s');
            return $data;
        });
        
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }

    // http://127.0.0.1:8000/api/checkout?valuation_request_id=1
    public function createThawaniCheckout(Request $request)
    {
        $request->validate([
            'valuation_request_id' => 'required',
        ]);

        $paymentMethod = $this->getThawaniCredentials();
        if (!$paymentMethod) {
            return $this->errorResponse("Payment method not configured.");
        }

        $valuationRequest = ValuationRequest::find($request->valuation_request_id);
        if (!$valuationRequest) {
            return $this->errorResponse("Valuation Request not found!", 422);
        }

        $payment = $this->createPayment($valuationRequest, $paymentMethod, $this->generateRandomString());
        if (!$payment) {
            return $this->errorResponse("Failed to make payment.", 422);
        }

        $valuationRequest->status_id = ValuationRequestStatusConstants::PAYMENT_ATTEMPTED;
        $valuationRequest->save();

        $customerId = $this->createOrRetrieveCustomer($paymentMethod);
        if (!$customerId) {
            return $this->errorResponse("Failed to create/retrieve customer.", 422);
        }

        $sessionResponse = $this->createThawaniSession($payment, $valuationRequest->total_amount, $paymentMethod, $customerId);
        Log::info('Session Response: ' . $sessionResponse->body());
        if ($sessionResponse->successful()) {
            $sessionId = $sessionResponse['data']['session_id'];
            // $thawaniPaymentId = $sessionResponse['data']['payment_id'];

            // ✅ Store Thawani payment_id for refunds
            $payment->update([
            //     'thawani_payment_id' => $thawaniPaymentId
                'thawani_session_id' => $sessionId
            ]);

            // return redirect()->away("https://uatcheckout.thawani.om/pay/{$sessionId}?key={$paymentMethod->public_key}");
            return response()->json([
                'status' => true,
                "message" => "Successfully received host url!",
                "data" => [
                    "url" => "https://uatcheckout.thawani.om/pay/{$sessionId}?key={$paymentMethod->public_key}"
                ]
            ], 200);
        }

        return $this->errorResponse("Failed to initiate Thawani checkout.", 422, $sessionResponse->json());
    }

    private function getThawaniCredentials()
    {
        $paymentMethod = PaymentMethod::where('name', 'Thawani')->first();
        if (!$paymentMethod || !$paymentMethod->private_key || !$paymentMethod->public_key) {
            return null;
        }
        return $paymentMethod;
    }

    private function errorResponse($message, $status = 422, $extra = [])
    {
        return response()->json(array_merge([
            'status' => false,
            'message' => $message
        ]), $status);
    }

    private function createPayment($valuationRequest, $paymentMethod, $randomString)
    {

        return Payment::create([
            "payment_method_id" => $paymentMethod->id,
            "valuation_request_id" => $valuationRequest->id,
            "amount" => $valuationRequest->total_amount,
            "status" => 'pending',
            "user_id" => auth()->id(),
            "payment_reference" => $randomString,
        ]);
    }

    private function createOrRetrieveCustomer($paymentMethod)
    {
        $user = auth()->user();

        if(!$user){
            $user = User::find(1);
            // return null;
        }

        if ($user->thawani_customer_id) {
            return $user->thawani_customer_id;
        }

        $response = Http::withHeaders([
            'thawani-api-key' => $paymentMethod->private_key
        ])->post('https://uatcheckout.thawani.om/api/v1/customers', [
            'client_customer_id' => (string) $user->id,
            'name' => $user->name,
        ]);

        if ($response->successful()) {
            $customerId = $response['data']['id'];
            $user->thawani_customer_id = $customerId;
            $user->save();
            return $customerId;
        }

        return null;
    }

    private function createThawaniSession($payment, $amount, $paymentMethod, $customerId)
    {
        return Http::withHeaders([
            'thawani-api-key' => $paymentMethod->private_key
        ])->post('https://uatcheckout.thawani.om/api/v1/checkout/session', [
            'client_reference_id' => $payment->payment_reference,
            'mode' => 'payment',
            'customer_id' => $customerId,
            'products' => [
                [
                    'name' => 'Valuation Request',
                    'quantity' => 1,
                    'unit_amount' => intval($amount * 1000), // Baisa
                ]
            ],
            'success_url' => url('api/success/' . encrypt($payment->payment_reference)),
            'cancel_url' => url('api/cancel/' . encrypt($payment->payment_reference)),
            'metadata' => [
                'payment_id' => $payment->id,
            ]
        ]);
    }

    public function success($payment_reference)
    {
        try {
            $decryptedRef = decrypt($payment_reference);
        } catch (\Exception $e) {
            return $this->errorResponse('Invalid payment reference.');
        }

        $payment = Payment::where('payment_reference', $decryptedRef)->first();
        if (!$payment) {
            return $this->errorResponse('Payment record not found.');
        }

        if($payment->status == "completed"){
            return $this->errorResponse('Payment already process! please contact support');
        }

        if(!$payment->thawani_session_id){
            return $this->errorResponse('Payment session not found.');
        }

        $paymentMethod = $this->getThawaniCredentials();
        if (!$paymentMethod) {
            return $this->errorResponse("Payment method not configured.");
        }

        $sessionResponse = Http::withHeaders([
            'thawani-api-key' => $paymentMethod->private_key
        ])->get("https://uatcheckout.thawani.om/api/v1/checkout/session/{$payment->thawani_session_id}");
        if ($sessionResponse->successful()) {
            $sessionData = $sessionResponse['data'];
            $status = $sessionData['payment_status']; // expected values: paid, failed, expired, etc.
            // $thawaniPaymentId = $sessionData['payment_id'];

            // Save Thawani payment_id (for refund use later)
            $payment->update([
                // 'thawani_payment_id' => $thawaniPaymentId,
                'status' => $status === 'paid' ? 'completed' : 'failed',
            ]);

            if ($status === 'paid') {
                $payment->valuationRequest->update([
                    'status_id' => ValuationRequestStatusConstants::CONFIRMED
                ]);

                // Prepare Email Data
                $valuationRequest = $payment->valuationRequest()->with([
                    'company',
                    'user',
                    'propertyType',
                    'serviceType',
                    'requestType',
                    'location',
                    'servicePricing',
                    'status',
                    'lastPayment'
                ])->first();

                $emailData = [
                    'id' => $valuationRequest->id,
                    'company_name' => optional($valuationRequest->company)->name ?? '-',
                    'user_name' => optional($valuationRequest->user)->first_name . ' ' . optional($valuationRequest->user)->last_name,
                    'property_type' => optional($valuationRequest->propertyType)->name ?? '-',
                    'service_type' => optional($valuationRequest->serviceType)->name ?? '-',
                    'request_type' => optional($valuationRequest->requestType)->name ?? '-',
                    'location' => optional($valuationRequest->location)->name ?? '-',
                    'service_pricing' => optional($valuationRequest->servicePricing)->price ?? 'default',
                    'area' => $valuationRequest->area ?? '-',
                    'total_amount' => $valuationRequest->total_amount ?? '-',
                    'status' => optional($valuationRequest->status)->name ?? '-',
                    'reference' => $valuationRequest->reference ?? '-',
                    'created_at_date' => $valuationRequest->created_at ? \Carbon\Carbon::parse($valuationRequest->created_at)->format('Y-m-d') : null,
                    'created_at_time' => $valuationRequest->created_at ? \Carbon\Carbon::parse($valuationRequest->created_at)->format('H:i:s') : null,
                    'payment_status' => optional($valuationRequest->lastPayment)->status ?? null,
                ];

                try {
                    if ($valuationRequest->user && $valuationRequest->user->email) {
                        Mail::to($valuationRequest->user->email)->send(new PaymentSuccessMail($emailData, 'user'));
                    }
                } catch (Exception $e) {
                    Log::error('Failed to send email to user: ' . $e->getMessage());
                }

                $company_email = $valuationRequest->company->companyDetails->email ?? null;
                
                if ($company_email) {
                    try {
                        Mail::to($company_email)->send(new PaymentSuccessMail($emailData, 'company'));
                    } catch (Exception $e) {
                        Log::error('Failed to send email to company: ' . $e->getMessage());
                    }
                }

                $admin_email = Setting::getValue('admin_email');
                if ($admin_email) {
                    try {
                        Mail::to($admin_email)->send(new PaymentSuccessMail($emailData, 'admin'));
                    } catch (Exception $e) {
                        Log::error('Failed to send email to admin: ' . $e->getMessage());
                    }
                }


                return response()->json([
                    "status" => true,
                    "message" => 'Payment was successful and verified.'
                ],200);
            } else {
                
                return $this->errorResponse("Payment failed or incomplete. Status: $status");
            }
        }

        return $this->errorResponse('Failed to verify payment status from Thawani.');
    }


    public function cancel($payment_reference)
    {
        $payment = Payment::where('payment_reference', decrypt($payment_reference))->first();
        if (!$payment) {
            return $this->errorResponse('Payment was canceled.');
        }

        if($payment->status == "completed"){
            return $this->errorResponse('Payment already process! please contact support');
        }

        $payment->update([
            'status' => 'failed'
        ]);

        return $this->errorResponse('Payment was canceled.');

    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }


    public function refundThawaniPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        $paymentMethod = $this->getThawaniCredentials();

        $response = Http::withHeaders([
            'thawani-api-key' => $paymentMethod->private_key
        ])->post('https://uatcheckout.thawani.om/api/v1/refunds', [
            'payment_id' => $payment->thawani_payment_id,
            'reason' => 'Customer requested refund'
        ]);

        if ($response->successful()) {
            return response()->json(['status' => true, 'message' => 'Refund issued.']);
        }

        return response()->json([
            'status' => false, 
            'message' => 'Refund failed.', 
            'error' => $response->json()
        ], 422);
    }
}
