<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use App\Models\Company;
use App\Models\File;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function getData() { 
        $data = Company::all(); 
        
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    public function store(Request $r) {
        $r->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable',
        ]);

        $file = new File();
        $file_path = $file->saveFile($r->file('logo'));

        Company::create([
            'name' => $r->name,
            'logo_file_id' => $file->id,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Property Type created successfully'
        ], 200);
    }

    public function show($id) { 
        $company = Company::find($id);
        
        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $company
        ], 200);
    }
    
    public function update(Request $r, $id) { 
        $r->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable',
        ]);

        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        // update file after removing old if uploaded
        if ($r->hasFile('logo')) {
            $file = new File();
            $file_path = $file->saveFile($r->file('logo'));

            // delete old file
            $old_file = $company->logo;

            if ($old_file) {
                Storage::delete($old_file->path);
            }
        }

        $company->update([
            'name' => $r->name,
            'logo_file_id' => $file->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property Type updated successfully'
        ], 200);
    }
    
    public function delete(Request $r) { 
        $company = Company::find($r->id);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        $old_file = $company->logo;
        
        if ($old_file) {
            Storage::delete($old_file->path);
        }

        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Property Type deleted successfully'
        ], 200);
    }
    
}
