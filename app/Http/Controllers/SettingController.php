<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getData() { 
        $data = Setting::all(); 
        
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    public function store(Request $r) {
        $r->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        Setting::insert([
            'key' => $r->key,
            'value' => $r->value,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Setting created'
        ], 200);
    }
    
    public function uploadImage(Request $r) {
        $r->validate([
            'key' => 'required|string',
            'value' => 'required|file',
        ]);
    
        $file = new File();
        $file_path = $file->saveFile($r->file('value'));
    
        Setting::updateOrCreate(
            ['key' => $r->key],
            ['value' => $file_path]
        );
    
        return response()->json([
            'status' => true,
            'message' => 'Setting image uploaded'
        ], 200);
    }
    

    public function show($key) { 
        $setting = Setting::where('key',$key)->first();
        
        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => "Setting option not found"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $setting
        ], 200);
    }
    
    public function update(Request $r, $key) { 
        $r->validate([
            'value' => 'required|string',
        ]);

        $setting = Setting::where('key',$key)->first();

        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        $setting->value = $r->value;
        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'Setting updated'
        ], 200);
    }
    
    public function delete(Request $r) { 
        $setting = Setting::find($r->key);

        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => 'Setting option not found'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'status' => true,
            'message' => 'Setting option deleted'
        ], 200);
    }
    
}
