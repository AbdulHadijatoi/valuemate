<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index() { 
        return Location::all(); 
    }

    public function store(Request $r) { 
        return Location::create($r->validate(['area_id'=>'required','name'=>'required'])); 
    }

    public function show(Location $location) { 
        return $location; 
    }

    public function update(Request $r, Location $location) { 
        $location->update($r->all()); return $location; 
    }
    
    public function destroy(Location $location) { 
        $location->delete(); return response()->noContent(); 
    }
}
