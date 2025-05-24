<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public $base_url = "https://uatcheckout.thawani.om";
    public $thawani_api_key = "rRQ26GcsZzoEhbrP2HZvLYDbn9C9et"; // secrete key
    public $thawani_url_key = 'HGvTMLDssJghr9tlN9gr4DVYt0qyBy'; // publishable key
    
    public function getData() { 

        $data = Payment::get();
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }

    public function create_customer($id){

        $data['client_customer_id'] = $id;

        $curl = curl_init($this->base_url .'/api/v1/customers');

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'thawani-api-key: '.$this->thawani_api_key
        ));

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(($data)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($response);


        dd($json);
    }

    public function createSession(){
        $data['client_reference_id'] = '';
        $data['mode'] = 'payment';
        
        $data['products'] = [
            [
                'name' => 'Valuation Service',
                'quantity' => 1,
                'unit_amount' => 1000, // Thawani expects amount in Baisa (OMR * 1000)
            ]
        ];

        $data['customer_id'] = 1;
        $data['success_url'] = url('success');
        $data['cancel_url'] = url('cancel');
        $curl = curl_init($this->base_url .'/api/v1/checkout/session');

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'thawani-api-key: '.$this->thawani_api_key
        ));
        
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(($data)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($response);


        return  json_encode($json);

        if ($json->success)
            return  $json->data->session_id;
        else
            return null;
    }

    public function createThawaniCheckout(Request $request)
    {
        // return $this->createSession();
        // http://127.0.0.1:8000/api/create-thawani-checkout?total_amount=10&valuation_request_id=1
        $request->validate([
            'total_amount' => 'required|numeric',
            'valuation_request_id' => 'required|string',
        ]);

        $totalAmount = $request->total_amount;
        $valuationRequestId = $request->valuation_request_id;
        
        
        $apiKey = "rRQ26GcsZzoEhbrP2HZvLYDbn9C9et";
        $publishableKey = "HGvTMLDssJghr9tlN9gr4DVYt0qyBy";

        $response = Http::withToken($apiKey)->post('https://uatcheckout.thawani.om/api/v1/checkout/session', [
            'client_reference_id' => $valuationRequestId,
            'mode' => 'payment',
            'products' => [
                [
                    'name' => 'Valuation Service',
                    'quantity' => 1,
                    'unit_amount' => intval($totalAmount * 100), // Thawani expects amount in Baisa (OMR * 1000)
                ]
            ],
            'success_url' => url('success'),
            'cancel_url' => url('cancel'),
            'metadata' => [
                'valuation_request_id' => $valuationRequestId,
            ]
        ]);

        if ($response->successful()) {
            $sessionId = $response['data']['session_id'];

            return redirect()->away("https://uatcheckout.thawani.om/pay/{$sessionId}?key={$publishableKey}");
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to initiate Thawani checkout.',
                'error' => $response->json(),
            ], 500);
        }
    }


    public function retrieve_session(Request $request){
        
        // $cardId=$request->card_id;
        $is_gift = $request->is_gift??0;
        // return 'test';
        $thawani_pay_url = $this->base_url ."/pay/";
        
        $retrive_customer = $this->create_customer($request->id);
        // return json_encode($retrive_customer);
        $customer_id = '';


        $local_customer = DB::table('users')
                            ->select(['thawani_cus_id'])
                            ->where([['id', $request->id]])
                            ->first();

        if ($local_customer) {
            $customer_id = $local_customer->thawani_cus_id;
        }

        
        if (!$customer_id){
            try {
                $retrive_customer = $this->create_customer($request->id);

                $customer_id = $retrive_customer->data->id;
                DB::table('users')
                    ->where('id', $request->id)
                    ->update(['thawani_cus_id' => $customer_id]);

            } catch (\Throwable $th) {
                $customer_id = '';
            }
        }
        // return $customer_id;

        $products = array();
        $index = array();
        
        if($is_gift == 1){
            $raw = DB::table('send_gifts')
                    ->select(['send_gifts.recipient_name', 'send_gifts.name_of_sender', 'send_gifts.gift_amount'])
                    ->join('payment_tokens', 'send_gifts.transaction_id', 'payment_tokens.id')
                    ->where([['payment_tokens.id', $request->transaction_id]])
                    ->get();

            foreach ($raw as $item) {
                $index['name'] =  substr('Gift to ' . $item->recipient_name . ' from ' . $item->name_of_sender,0,20);
                $index['quantity'] = 1;
                $index['unit_amount'] = $item->gift_amount * 1000;
                $products[] = $index;
            }
        }else{
            $raw = DB::table('causes')
                ->select(['causes.Title_en', 'cause_payments.payment_amount'])
                ->join('cause_payments', 'causes.id', 'cause_payments.cause_id')
                ->where([['cause_payments.transaction_id', $request->transaction_id]])
                ->get();

                
            if (count($raw) > 0) {
                foreach ($raw as $item) {
                    $index['name'] = substr($item->Title_en,0,20);
                    $index['quantity'] = 1;
                    $index['unit_amount'] = $item->payment_amount * 1000;
                    $products[] = $index;
                }
            }
            // remove this else after app is approved on play store and ios store ***
            else{
                $raw = DB::table('send_gifts')
                    ->select(['send_gifts.recipient_name', 'send_gifts.name_of_sender', 'send_gifts.gift_amount'])
                    ->join('payment_tokens', 'send_gifts.transaction_id', 'payment_tokens.id')
                    ->where([['payment_tokens.id', $request->transaction_id]])
                    ->get();

                foreach ($raw as $item) {
                    $index['name'] =  substr('Gift to ' . $item->recipient_name . ' from ' . $item->name_of_sender,0,20);
                    $index['quantity'] = 1;
                    $index['unit_amount'] = $item->gift_amount * 1000;
                    $products[] = $index;
                }
            }
        }

        
        $data['client_reference_id'] = '';

        $data['mode'] = 'payment';
        $data['products'] = $products;
        // $data['client_reference_id'] = $request->id;


        // $data['save_card_on_success'] = true;
        // return $data;
        $data['customer_id'] = $customer_id;
        // $customer_id;
        // $data['success_url'] = 'https://alrahma.om/thawani/success/' . $request->transaction_id . '/' . $request->total_amount . '/' . $request->lang;
        $data['success_url'] = url('/thawani/success/' . $request->transaction_id . '/' . $request->total_amount . '/' . $request->lang);




        $data['cancel_url'] = url('/thawani/cancel');


        // $data['save_card_on_success'] = true;
        // if($cardId)
        // {
            
        // }
        // else{

        // }
        // return $data;
        $session_id = '';
        Log::info('Before calling createSession()');
        try {
            $session_id =    $this->createSession($data);
            Log::info('After calling createSession()');

            // return $session_id;
            // return json_encode($session_id);
            if ($session_id){
                Log::info('Before redirect to thawani');
                return redirect()->to($thawani_pay_url . $session_id . '?key='.$this->thawani_url_key);
            }else {
                DB::table('users')
                    ->where('id', $request->id)
                    ->update(['thawani_cus_id' => null]);

                $data['customer_id'] = '';
                Log::info('Before calling createSession()');
                $session_id =  $this->createSession($data);
                Log::info('After calling createSession()');

                if ($session_id){
                    Log::info('Before redirect to thawani');
                    return redirect()->to($thawani_pay_url . $session_id . '?key='.$this->thawani_url_key);
                }
            }
        } catch (\Throwable $th) {
            $data['customer_id'] = '';
            Log::info('Before calling createSession()');
            $session_id =  $this->createSession($data);
            Log::info('After calling createSession()');
            if ($session_id){
                Log::info('Before redirect to thawani');
                return redirect()->to($thawani_pay_url . $session_id . '?key='.$this->thawani_url_key);
            }
        }
        return redirect()->to(url('/thawani/cancel'));
    }
}
