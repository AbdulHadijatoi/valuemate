<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use App\Models\File;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function dataQuery() {
        
        $data = PaymentMethod::with('file')->get([]);

        $data = $data->map(function ($item) {
            $data = [];

            $data['id'] = $item->id;
            $data['name'] = $item->name ?? '-';
            $data['status'] = $item->status ?? '-';
            $data['image_url'] = $item->logo ? $item->logo->full_path : null;
            return $data;
        });

        return [
            'data' => $data
        ];
    }

    public function getData(Request $request) { 

        $data = $this->dataQuery($request);
        
        return response()->json([
            'status' => true,
            'data' => $data['data']
        ], 200);
    }
    
    public function store(Request $r) {

        $r->validate([
            'name'=>'required',
            'file'=>'required'
        ]);

        // save file using file model's function saveFile
        $file = new File();
        $file->saveFile($r->file('file'));

        $data = [
            'name'=>$r->name,
            'file_id'=>$file->id,
        ];

        PaymentMethod::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Payment method created successfully'
        ], 200);
    }
    
    public function update(Request $r, $id) { 

        $r->validate([
            'name'=>'required',
            'file'=>'nullable',
            'status'=>'nullable',
        ]);

        $payment_method = PaymentMethod::find($id);

        if (!$payment_method) {
            return response()->json([
                'status' => false,
                'message' => 'Payment method not found'
            ], 404);
        }

        $data = [
            'name'=>$r->name,
            'status'=>$r->status,
        ];

        if ($r->hasFile('file')) {
            $file = new File();
            $file->saveFile($r->file('file'));

            $old_file = $payment_method->logo;

            if ($old_file) {
                Storage::delete($old_file->path);
            }

            $data['file_id'] = $file->id;
        }

        $payment_method->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Payment Method updated successfully'
        ], 200);
    }
    
    public function delete($id) { 
        $payment_method = PaymentMethod::find($id);
        
        if (!$payment_method) {
            return response()->json([
                'status' => false,
                'message' => 'Payment method not found'
            ], 404);
        }

        // delete file
        $old_file = $payment_method->logo;
        
        if ($old_file) {
            Storage::delete($old_file->path);
        }

        $payment_method->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment method deleted successfully'
        ], 200);
    }
    
}
