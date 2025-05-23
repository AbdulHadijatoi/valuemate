<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\ServiceType;
use App\Models\Setting;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
   
    public function create(Request $r) {
        $r->validate([
            'name' => 'required',
        ]);
    
        $serviceType = ServiceType::create([
            'name' => $r->name,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Service Type created',
            'data' => $serviceType
        ], 200);
    }

    public function update(Request $r, $id) {
        $r->validate([
            'name' => 'required',
        ]);
    
        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            $serviceType->update([
                'name' => $r->name,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Service Type updated',
                'data' => $serviceType
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Service Type not found'
            ], 404);
        }
    }

    public function delete($id) {
        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            $serviceType->delete();
            return response()->json([
                'status' => true,
                'message' => 'Service Type deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Service Type not found'
            ], 404);
        }
    }


    public function get() {
        $serviceTypes = ServiceType::all();
    
        return response()->json([
            'status' => true,
            'data' => $serviceTypes
        ], 200);
    }
}
