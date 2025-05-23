<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index() { 
        return DocumentRequirement::all(); 
    }

    public function store(Request $r) {
        return DocumentRequirement::create($r->validate([
            'property_type_id'=>'required',
            'service_type_id'=>'required',
            'title'=>'required'
        ]));
    }
    public function show(DocumentRequirement $documentRequirement) { 
        return $documentRequirement; 
    }

    public function update(Request $r, DocumentRequirement $documentRequirement) { 
        $documentRequirement->update($r->all()); 
        return $documentRequirement; 
    }

    public function destroy(DocumentRequirement $documentRequirement) { 
        $documentRequirement->delete(); 
        return response()->noContent(); 
    }

}
