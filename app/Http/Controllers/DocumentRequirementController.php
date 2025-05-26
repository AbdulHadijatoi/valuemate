<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\PropertyType;
use App\Models\ServicePricing;
use App\Models\ServiceType;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DocumentRequirementController extends Controller
{
    public function getData() { 
        $data = DocumentRequirement::get();
        $property_types = PropertyType::get(['id','name']);
        $property_service_types = PropertyServiceType::with(['propertyType', 'serviceType'])->get();

        $data = $data->map(function($item){
            $data = [];
            $data['id']= $item->id;
            $data['document_name']= $item->document_name;
            $data['property_type_id']= $item->property_type_id;
            $data['service_type_id']= $item->service_type_id;
            $data['property_type_name']= $item->propertyType? $item->propertyType->name: null;
            $data['service_type_name']= $item->serviceType? $item->serviceType->name: null;
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format("Y-m-d") : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format("H:i:s") : null;
            return $data;
        });

        $grouped = $property_service_types->groupBy(function ($item) {
            return $item->propertyType->name ?? 'Unknown'; // Group by property type name
        })->map(function ($items, $propertyTypeName) {
            return [
                'property_type' => $propertyTypeName,
                'property_type_id' => $items->first()->property_type_id,
                'services' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_type_id' => $item->service_type_id,
                        'service_type_name' => $item->serviceType->name ?? null,
                        'created_at_date' => $item->serviceType && $item->serviceType->created_at ? Carbon::parse($item->serviceType->created_at)->format("Y-m-d") : null,
                        'created_at_time' => $item->serviceType && $item->serviceType->created_at ? Carbon::parse($item->serviceType->created_at)->format("H:i:s") : null,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $data,
            'property_types' => $property_types,
            'service_types' => $grouped,
        ], 200);
    }
    
    
    public function store(Request $r) {
        $r->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'required|exists:service_types,id',
            'document_name' => 'required'
        ]);
    
        DocumentRequirement::create([
            'property_type_id' => $r->property_type_id,
            'service_type_id' => $r->service_type_id,
            'document_name' => $r->document_name,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Document Requirement created'
        ], 200);
    }

    public function update(Request $r, $id) {
        $r->validate([
            'document_name' => 'required'
        ]);
    
        $data = DocumentRequirement::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Document Requirement not found'
            ], 404);
        }
    
        $data->update([
            'document_name' => $r->document_name,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Document Requirement updated'
        ], 200);
    }

    public function delete($id) {
        $data = DocumentRequirement::find($id);
    
        if ($data) {
            $data->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Document Requirement deleted'
            ], 200);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'Document Requirement not found'
        ], 404);
    }
}
