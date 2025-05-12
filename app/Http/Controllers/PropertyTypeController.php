<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function getData() { 
        $property_types = PropertyType::all(); 
        
        return response()->json([
            'status' => true,
            'data' => $property_types
        ], 200);
    }
    
    public function store(Request $r) {
        $r->validate([
            'name' => 'required|string|max:255',
        ]);

        PropertyType::create([
            'name' => $r->name,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Property Type created successfully'
        ], 200);
    }

    public function show($id) { 
        $property_type = PropertyType::find($id);
        
        if (!$property_type) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $property_type
        ], 200);
    }
    
    public function update(Request $r, $id) { 
        $r->validate([
            'name' => 'required|string|max:255',
        ]);

        $property_type = PropertyType::find($id);

        if (!$property_type) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        $property_type->update([
            'name' => $r->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property Type updated successfully'
        ], 200);
    }
    
    public function destroy(Request $r) { 
        $property_type = PropertyType::find($r->id);

        if (!$property_type) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        $property_type->delete();

        return response()->json([
            'status' => true,
            'message' => 'Property Type deleted successfully'
        ], 200);
    }
    
}
