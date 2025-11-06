<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\PropertyType;
use App\Models\RequestType;
use App\Models\ServicePricing;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Traits\Cacheable;
use Illuminate\Http\Request;

class ServicePricingController extends Controller
{
    use Cacheable;
    public function getData() { 
        $data = ServicePricing::get();
    
        $data = $data->map(function ($item) {
            $data = [];
            $data['id'] = $item->id;
            $data['service_type_id'] = $item->service_type_id;
            $data['property_type_id'] = $item->property_type_id;
            $data['company_id'] = $item->company_id;
            $data['request_type_id'] = $item->request_type_id;
            $data['request_type_name'] = $item->requestType ? $item->requestType->name : '-';
            $data['service_type_name'] = $item->serviceType ? $item->serviceType->name : '-';
            $data['property_type_name'] = $item->propertyType ? $item->propertyType->name : '-';
            $data['company_name'] = $item->company ? $item->company->name : '-';
            $data['area_range'] = $item->area_from . ' - ' . $item->area_to;
            $data['area_from'] = $item->area_from;
            $data['area_to'] = $item->area_to;
            $data['price'] = $item->price;
            $data['created_at_date'] = $item->created_at ? $item->created_at->format('Y-m-d') : '-';
            $data['created_at_time'] = $item->created_at ? $item->created_at->format('H:i:s') : '-';
            return $data;
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    
    public function store(Request $r) {
        $r->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'nullable|exists:service_types,id',
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
                'request_type_id' => $r->request_type_id??1,
            ],
            [
                'price' => $r->price
            ]
        );
    
        // Clear cache
        $this->clearConstantCaches();
    
        return response()->json([
            'status' => true,
            'message' => 'Service pricing created or updated'
        ], 200);
    }

    public function update(Request $r, $id) {
        $r->validate([
            'price' => 'required|numeric',
        ]);
    
        $data = ServicePricing::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Service Pricing not found'
            ], 404);
        }
    
        $data->update([
            'price' => $r->price
        ]);
    
        // Clear cache
        $this->clearConstantCaches();
    
        return response()->json([
            'status' => true,
            'message' => 'Service Pricing updated'
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
            
            // Clear cache
            $this->clearConstantCaches();
            
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
