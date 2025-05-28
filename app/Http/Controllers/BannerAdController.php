<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use App\Models\File;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerAdController extends Controller
{
    public function dataQuery($request, $export = false) {
        
        $data = BannerAd::when($request->search_keyword, function($query) use ($request){
            $query->where('title', 'like', '%' . $request->search_keyword . '%');
        })
        ->when($request->from_date && $request->to_date, function ($query) use ($request) {
            return $query->whereDate('start_date','>=', Carbon::parse($request->from_date)->format('Y-m-d'))
                        ->whereDate('end_date','<=', Carbon::parse($request->to_date)->format('Y-m-d'));
        });

        if ($export) {
            $data = $data->get();
            $total = $data->count();
        }else{
            $data = $data->paginate($request->per_page);
            $total = $data->total();
        }

        $data = $data->map(function ($item) {
            $data = [];

            $data['id'] = $item->id;
            $data['title'] = $item->title ?? '-';
            $data['description'] = $item->description ?? '-';
            $data['image_url'] = $item->banner ? $item->banner->full_path : null;
            $data['link'] = $item->link ?? '-';
            $data['ad_type'] = $item->ad_type ?? '-';
            $data['start_date'] = $item->start_date ? Carbon::parse($item->start_date)->format('Y-m-d') : null;
            $data['end_date'] = $item->end_date ? Carbon::parse($item->end_date)->format('Y-m-d') : null;
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format('H:i:s') : null;
            return $data;
        });

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function getData(Request $request, $export = false) { 
        $request->validate([
            "from_date" => "nullable",
            "to_date" => "nullable",
            "per_page" => "nullable",
            "search_keyword" => "nullable",
        ]);

        $data = $this->dataQuery($request, $export);
        $total = $data['total'];
        
        return response()->json([
            'status' => true,
            'data' => $data['data'],
            "total" => $total,
        ], 200);
    }
    
    public function store(Request $r) {

        $r->validate([
            'title'=>'required',
            'description'=>'nullable',
            'file'=>'required',
            'link'=>'nullable',
            'ad_type'=>'nullable',
            'start_date'=>'nullable',
            'end_date'=>'nullable'
        ]);

        // save file using file model's function saveFile
        $file = new File();
        $file_path = $file->saveFile($r->file('file'));

        $data = [
            'title'=>$r->title,
            'description'=>$r->description,
            'file_id'=>$file->id,
            'link'=>$r->link,
            'ad_type'=>$r->ad_type,
            'start_date'=>$r->start_date,
            'end_date'=>$r->end_date,
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
            'file'=>'nullable',
            'description'=>'nullable',
            'link'=>'nullable',
            'ad_type'=>'nullable',
            'start_date'=>'nullable',
            'end_date'=>'nullable'
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
            'start_date'=>$r->start_date,
            'end_date'=>$r->end_date,
        ];

        // update file after removing old if uploaded
        if ($r->hasFile('file')) {
            $file = new File();
            $file_path = $file->saveFile($r->file('file'));

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
