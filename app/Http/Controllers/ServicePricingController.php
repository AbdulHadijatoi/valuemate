<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\ServicePricing;
use App\Models\ServiceType;
use App\Models\Setting;
use Illuminate\Http\Request;

class ServicePricingController extends Controller
{
    public function getData() { 
        $data = ServicePricing::get();
    
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    
    public function store(Request $r) {
        $r->validate([
            'service_type_id' => 'nullable|exists:service_types,id',
            'property_type_id' => 'nullable|exists:property_types,id',
            'company_id' => 'nullable|exists:companies,id',
            'request_type_id' => 'nullable|exists:request_types,id',
            'area_from' => 'required|numeric',
            'area_to' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
    
        ServicePricing::updateOrCreate(
            [
                'service_type_id' => $r->service_type_id,
                'property_type_id' => $r->property_type_id,
                'company_id' => $r->company_id,
                'area_from' => $r->area_from,
                'area_to' => $r->area_to,
                'request_type_id' => $r->request_type_id,
            ],
            [
                'price' => $r->price
            ]
        );
    
        return response()->json([
            'status' => true,
            'message' => 'Service pricing created or updated'
        ], 200);
    }

    public function show($id) { 
        $data = ServicePricing::find($id);
        
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Service Pricing not found'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    public function delete($id) {
        $data = ServicePricing::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Service Pricing deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Service Pricing not found'
            ], 404);
        }
    }

}
