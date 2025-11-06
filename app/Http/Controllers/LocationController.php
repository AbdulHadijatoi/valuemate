<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Traits\Cacheable;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use Cacheable;

    public function getData(Request $request) {
        return $this->remember('locations_data', function () {
            $locations = Location::get();

            $locations = $locations->map(function($item) {
                $data = [];
                $data['id'] = $item->id;
                $data['name'] = $item->name;
                $data['name_ar'] = $item->name_ar;
                $data['description'] = $item->description;
                $data['description_ar'] = $item->description_ar;
                $data['latitude'] = $item->latitude;
                $data['longitude'] = $item->longitude;
                $data['status'] = $item->status;
                $data['map_url'] = $item->map_url; // This will use the accessor defined in the Location model
                $data['created_at_date'] = $item->created_at ? $item->created_at->format('Y-m-d') : null;
                $data['created_at_time'] = $item->created_at ? $item->created_at->format('H:i:s') : null;
                return $data;
            });

            return response()->json([
                'status' => true,
                'data' => $locations
            ], 200);
        }, 3600);
    }

    public function index($id = null) {
        $cacheKey = 'location_' . $id;
        
        return $this->remember($cacheKey, function () use ($id) {
            $data = Location::find($id);
                        
            return response()->json([
                'status' => true,
                'message' => 'Data retrieved',
                'data' => $data??[]
            ]);
        }, 3600);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Location::create($request->all());

        // Clear cache
        $this->clearCache('locations_data');
        $this->clearConstantCaches();

        return response()->json([
            'status' => true,
            'message' => 'Location created successfully'
        ], 200);
    }
    
    public function update(Request $request, $id) {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'nullable',
        ]);

        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Location not found'
            ], 404);
        }

        
        $location->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status ?? 1, // Default to 1 if not provided
        ]);

        // Clear cache
        $this->clearCache('locations_data');
        $this->clearCache('location_' . $id);
        $this->clearConstantCaches();

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

        // Clear cache
        $this->clearCache('locations_data');
        $this->clearCache('location_' . $id);
        $this->clearConstantCaches();

        return response()->json([
            'status' => true,
            'message' => 'Location deleted successfully'
        ], 200);
    }
    
    public function show($id) {
        $cacheKey = 'location_' . $id;
        
        return $this->remember($cacheKey, function () use ($id) {
            $location = Location::findOrFail($id);
            return response()->json([
                'status' => true,
                'mesage' => 'Location retrieved successfully',
                'data' => $location
            ], 200);
        }, 3600);
    }

}
