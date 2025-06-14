<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\PropertyType;
use App\Models\ServiceType;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PropertyServiceTypeController extends Controller
{
    public function getData() { 
        $data = PropertyServiceType::with(['propertyType', 'serviceType'])->get();
        $grouped = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'property_type_name' => $item->propertyType?$item->propertyType->name:null,
                'service_type_name' => $item->serviceType?$item->serviceType->name:null,
                'property_type_id' => $item->property_type_id,
                'service_type_id' => $item->service_type_id,
                'created_at_date' => $item->created_at ? Carbon::parse($item->created_at)->format("Y-m-d") : null,
                'created_at_time' => $item->created_at ? Carbon::parse($item->created_at)->format("H:i:s") : null,
            ];
        });
    
        return response()->json([
            'status' => true,
            'data' => $grouped
        ], 200);
    }
    
    
    public function store(Request $r) {
        $r->validate([
            'property_type_id' => 'required',
            'service_type_id' => 'required',
        ]);
    
        PropertyServiceType::updateOrCreate(
            [
                'service_type_id' => $r->service_type_id,
                'property_type_id' => $r->property_type_id,
            ],
            [] // <- second argument: values to update/create
        );
    
        return response()->json([
            'status' => true,
            'message' => 'created or updated'
        ], 200);
    }

    public function show($property_type_id) { 
        $data = PropertyServiceType::with(['propertyType', 'serviceType'])
                    ->where('property_type_id', $property_type_id)
                    ->get();
    
        $grouped = $data->groupBy(function ($item) {
            return $item->propertyType->name ?? 'Unknown'; // Group by property type name
        })->map(function ($items, $propertyTypeName) {
            return [
                'property_type' => $propertyTypeName,
                'property_type_id' => $items->first()->property_type_id,
                'services' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_type_id' => $item->service_type_id,
                        'service_type' => $item->serviceType->name ?? null,
                    ];
                })->values(),
            ];
        })->values();
    
        return response()->json([
            'status' => true,
            'data' => $grouped
        ], 200);
    }
    
    public function delete($id) {
        $data = PropertyServiceType::find($id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Property Service Type deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Property Service Type not found'
            ], 404);
        }
    }

}
