<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\ServicePricing;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Models\ValuationRequest;
use Illuminate\Http\Request;

class ValuationRequestController extends Controller
{
    public function getData() { 
        $data = ValuationRequest::get();
    
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    
    public function store(Request $r) {
        $r->validate([
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'required|exists:users,id',
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'required|exists:service_types,id',
            'request_type_id' => 'required|exists:request_types,id',
            'location_id' => 'required|exists:locations,id',
            'service_pricing_id' => 'required|exists:service_pricings,id',
            'area_from' => 'required|numeric',
            'area_to' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'reference' => 'required|string|max:255',
        ]);
    
        $valuation_request = ValuationRequest::create([
            "company_id" => $r->company_id,
            "user_id" => $r->user_id,
            "property_type_id" => $r->property_type_id,
            "service_type_id" => $r->service_type_id,
            "request_type_id" => $r->request_type_id,
            "location_id" => $r->location_id,
            "service_pricing_id" => $r->service_pricing_id,
            "area_from" => $r->area_from,
            "area_to" => $r->area_to,
            "total_amount" => $r->total_amount,
            "reference" => $r->reference
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request created.',
            'data' => $valuation_request
        ], 200);
    }

    public function show($id) {
        $data = ValuationRequest::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function updateStatus(Request $r) {
        $r->validate([
            'valuation_request_id' => 'required|exists:valuation_requests,id',
            'status_id' => 'required|exists:valuation_request_statuses,id',
        ]);
    
        $valuationRequest = ValuationRequest::find($r->valuation_request_id);
    
        if (!$valuationRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        $valuationRequest->update([
            'status_id' => $r->status_id
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request status updated.'
        ], 200);
    }

    public function update(Request $r, $id) {
        $r->validate([
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'required|exists:users,id',
            'status_id' => 'required|exists:valuation_request_statuses,id',
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'required|exists:service_types,id',
            'request_type_id' => 'required|exists:request_types,id',
            'location_id' => 'required|exists:locations,id',
            'service_pricing_id' => 'required|exists:service_pricings,id',
            'area_from' => 'required|numeric',
            'area_to' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'reference' => 'required|string|max:255',
        ]);
    
        $data = ValuationRequest::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        $data->update([
            "company_id" => $r->company_id,
            "user_id" => $r->user_id,
            "status_id" => $r->status_id,
            "property_type_id" => $r->property_type_id,
            "service_type_id" => $r->service_type_id,
            "request_type_id" => $r->request_type_id,
            "location_id" => $r->location_id,
            "service_pricing_id" => $r->service_pricing_id,
            "area_from" => $r->area_from,
            "area_to" => $r->area_to,
            "total_amount" => $r->total_amount,
            "reference" => $r->reference
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request updated.'
        ], 200);
    }

    public function delete($id) {
        $data = ValuationRequest::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        $data->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request deleted.'
        ], 200);
    }

    public function uploadDocuments(Request $r) {
        $r->validate([
            'valuation_request_id' => 'required|exists:valuation_requests,id',
            'document_requirement_id' => 'required|exists:document_requirements,id',
            'document_file' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    
        $valuationRequest = ValuationRequest::find($r->valuation_request_id);
    
        if (!$valuationRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        if ($r->hasFile('document_file')) {
            $file = new File();
            $file_path = $file->saveFile($r->file('document_file'));
        }

        $valuationRequest->documents()->create([
            'document_requirement_id' => $r->document_requirement_id,
            'file_id' => $file->id,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Documents uploaded successfully.'
        ], 200);
    }
}
