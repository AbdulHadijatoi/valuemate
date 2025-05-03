<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use Illuminate\Http\Request;

class BannerAdController extends Controller
{
    public function index() { 
        return BannerAd::all(); 
    }
    
    public function store(Request $r) {
        return BannerAd::create($r->validate([
            'title'=>'required',
            'image'=>'required',
            'link'=>'nullable',
            'duration_type'=>'required'
        ]));
    }

    public function show(BannerAd $bannerAd) { 
        return $bannerAd; 
    }
    
    public function update(Request $r, BannerAd $bannerAd) { 
        $bannerAd->update($r->all()); return $bannerAd; 
    }
    
    public function destroy(BannerAd $bannerAd) { 
        $bannerAd->delete(); return response()->noContent(); 
    }
    
}
