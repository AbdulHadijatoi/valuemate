<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerAdController extends Controller
{
    public function getData() { 
        $bannerAds = BannerAd::all();

        return response()->json([
            'status' => true,
            'message' => "Data retrieved",
            'data' => $bannerAds??[]
        ], 200);
    }
    
    public function store(Request $r) {

        $r->validate([
            'title'=>'required',
            'description'=>'nullable',
            'image'=>'required',
            'link'=>'nullable',
            'ad_type'=>'nullable'
        ]);

        // save file using file model's function saveFile
        $file = new File();
        $file_path = $file->saveFile($r->file('image'));

        $data = [
            'title'=>$r->title,
            'description'=>$r->description,
            'file_id'=>$file->id,
            'link'=>$r->link,
            'ad_type'=>$r->ad_type,
        ];

        BannerAd::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Banner Ad created successfully'
        ], 200);
    }

    public function show($id) { 
        $bannerAd = BannerAd::find($id);
        
        if (!$bannerAd) {
            return response()->json([
                'status' => false,
                'message' => 'Banner Ad not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $bannerAd
        ], 200);
    }
    
    public function update(Request $r, $id) { 

        $r->validate([
            'title'=>'required',
            'image'=>'required',
            'description'=>'nullable',
            'link'=>'nullable',
            'ad_type'=>'nullable'
        ]);

        $bannerAd = BannerAd::find($id);

        if (!$bannerAd) {
            return response()->json([
                'status' => false,
                'message' => 'Banner Ad not found'
            ], 404);
        }

        $data = [
            'title'=>$r->title,
            'description'=>$r->description,
            'link'=>$r->link,
            'ad_type'=>$r->ad_type,
        ];

        // update file after removing old if uploaded
        if ($r->hasFile('image')) {
            $file = new File();
            $file_path = $file->saveFile($r->file('image'));

            // delete old file
            $old_file = $bannerAd->banner;

            if ($old_file) {
                Storage::delete($old_file->path);
            }

            $data['file_id'] = $file->id;
        }

        $bannerAd->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Banner Ad updated successfully'
        ], 200);
    }
    
    public function delete($id) { 
        $bannerAd = BannerAd::find($id);
        
        if (!$bannerAd) {
            return response()->json([
                'status' => false,
                'message' => 'Banner Ad not found'
            ], 404);
        }

        // delete file
        $old_file = $bannerAd->banner;
        
        if ($old_file) {
            Storage::delete($old_file->path);
        }

        $bannerAd->delete();

        return response()->json([
            'status' => true,
            'message' => 'Banner Ad deleted successfully'
        ], 200);
    }
    
}
