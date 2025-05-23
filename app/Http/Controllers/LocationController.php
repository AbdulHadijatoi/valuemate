<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    public function getData(Request $request) {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $locations = Location::query();

        if ($request->has('search')) {
            $locations->where('name', 'like', '%' . $request->search . '%');
        }

        $locations = $locations->get();

        return response()->json([
            'status' => true,
            'message' => "Data retrieved",
            'data' => $locations
        ], 200);
    }

    public function index($id = null) { 

        $data = Location::find($id);
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data??[]
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Location::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Location created successfully'
        ], 200);
    }
    
    public function update(Request $request, $id) {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Location not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Location updated successfully'
        ], 200);
    }
    
    public function delete($id) {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Location not found'
            ], 404);
        }

        $location->delete();

        return response()->json([
            'status' => true,
            'message' => 'Location deleted successfully'
        ], 200);
    }
    
    public function show($id) {
        $location = Location::findOrFail($id);
        return response()->json([
            'status' => true,
            'mesage' => 'Location retrieved successfully',
            'data' => $location
        ], 200);
    }

}
