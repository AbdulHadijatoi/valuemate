<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use Illuminate\Http\Request;

class BannerAdController extends Controller
{
    public function getData() { 
        $bannerAds = BannerAd::all();

        return response()->json([
            'status' => true,
            'data' => $bannerAds
        ], 200);
    }
    
    public function store(Request $r) {
        BannerAd::create($r->validate([
            'title'=>'required',
            'file'=>'required',
            'link'=>'nullable',
            'ad_type'=>'required'
        ]));

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
            'link'=>'nullable',
            'ad_type'=>'required'
        ]);

        $bannerAd = BannerAd::find($id);

        if (!$bannerAd) {
            return response()->json([
                'status' => false,
                'message' => 'Banner Ad not found'
            ], 404);
        }

        $bannerAd->update($r->all());

        return response()->json([
            'status' => true,
            'message' => 'Banner Ad updated successfully'
        ], 200);
    }
    
    public function destroy($id) { 
        $bannerAd = BannerAd::find($id);
        
        if (!$bannerAd) {
            return response()->json([
                'status' => false,
                'message' => 'Banner Ad not found'
            ], 404);
        }

        $bannerAd->delete();

        return response()->json([
            'status' => true,
            'message' => 'Banner Ad deleted successfully'
        ], 200);
    }
    
}
