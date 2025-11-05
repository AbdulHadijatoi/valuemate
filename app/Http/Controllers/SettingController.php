<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Setting;
use App\Traits\Cacheable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    use Cacheable;

    public function getData() {
        return $this->remember('settings_data', function () { 
        $data = Setting::get(); 
        

        $data = $data->map(function($item, $index) {
            $data = [];
            
            if($item->is_file){
                $data['value'] = url(Storage::url($item->value));
            }else{
                $data['value'] = $item->value;
            }

            $data['key'] = $item->key;
            $data['is_file'] = $item->is_file;
            $data['index'] = ++$index;

            return $data;
        });


            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        }, 3600);
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

        // Clear related caches
        $this->clearResourceCache('settings');
        $this->clearConstantCaches();
        Cache::forget('setting_value_' . $r->key);

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
            [
                'key' => $r->key
            ],[
                'value' => $file_path, 
                'is_file' => 1
            ],
        );

        // Clear related caches
        $this->clearResourceCache('settings');
        $this->clearConstantCaches();
        Cache::forget('setting_value_' . $r->key);
    
        return response()->json([
            'status' => true,
            'message' => 'Setting image uploaded'
        ], 200);
    }
    

    public function show($key) { 
        $cacheKey = 'setting_' . $key;
        
        return $this->remember($cacheKey, function () use ($key) {
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
        }, 3600);
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

        // Clear related caches
        $this->clearResourceCache('settings');
        $this->clearConstantCaches();
        Cache::forget('setting_' . $key);
        Cache::forget('setting_value_' . $key);

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

        // Clear related caches
        $this->clearResourceCache('settings');
        $this->clearConstantCaches();
        Cache::forget('setting_' . $r->key);
        Cache::forget('setting_value_' . $r->key);

        return response()->json([
            'status' => true,
            'message' => 'Setting option deleted'
        ], 200);
    }
    
}
