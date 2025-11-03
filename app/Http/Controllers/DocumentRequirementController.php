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
        $data = DocumentRequirement::orderBy('id','Desc')->get();
        
        $data = $data->map(function($item){
            $data = [];
            $data['id']= $item->id;
            $data['document_name']= $item->document_name;
            $data['document_name_ar']= $item->document_name_ar;
            $data['property_type_id']= $item->property_type_id;
            $data['service_type_id']= $item->service_type_id;
            $data['property_type_name']= $item->propertyType? $item->propertyType->name: null;
            $data['property_type_name_ar']= $item->propertyType? $item->propertyType->name_ar: null;
            $data['service_type_name']= $item->serviceType? $item->serviceType->name: null;
            $data['service_type_name_ar']= $item->serviceType? $item->serviceType->name_ar: null;
            $data['is_file']= $item->is_file;
            $data['type']= $item->is_file?'File':'Text';
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format("Y-m-d") : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format("H:i:s") : null;
            return $data;
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }
    
    
    public function store(Request $r) {
        $r->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'required|exists:service_types,id',
            'document_name' => 'required',
            'document_name_ar' => 'nullable',
            'is_file' => 'nullable'
        ]);
    
        DocumentRequirement::create([
            'property_type_id' => $r->property_type_id,
            'service_type_id' => $r->service_type_id,
            'document_name' => $r->document_name,
            'document_name_ar' => $r->document_name_ar,
            'is_file' => $r->is_file,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Document Requirement created'
        ], 200);
    }

    public function update(Request $r, $id) {
        $r->validate([
            'document_name' => 'required',
            'document_name_ar' => 'nullable',
            'is_file' => 'required',
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
            'document_name_ar' => $r->document_name_ar,
            'is_file' => $r->is_file,
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
