<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index() { 
        return Area::all(); 
    }
    
    public function store(Request $r) { 
        return Area::create($r->validate(['name'=>'required'])); 
    }
    
    public function show(Area $area) { 
        return $area; 
    }
    
    public function update(Request $r, Area $area) { 
        $area->update($r->all()); return $area; 
    }
    
    public function destroy(Area $area) { 
        $area->delete(); return response()->noContent(); 
    }
    
}
