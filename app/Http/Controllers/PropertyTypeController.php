<?php

namespace App\Http\Controllers;

use App\Exports\ExportData;
use App\Models\BannerAd;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PropertyTypeController extends Controller
{
    public function dataQuery($request, $export = false) {
        
        $data = PropertyType::when($request->search_keyword, function($query) use ($request){
            $query->where('name', 'like', '%' . $request->search_keyword . '%');
        })
        ->when($request->from_date && $request->to_date, function ($query) use ($request) {
            return $query->whereDate('created_at','>=', $request->from_date)
                        ->whereDate('created_at','<=', $request->to_date);
        });

        if ($export) {
            $data = $data->get();
            $total = $data->count();
        }else{
            $data = $data->paginate($request->per_page);
            $total = $data->total();
        }

        $data = $data->map(function ($item) use ($export){
            $data = [];
            $data['id'] = $item->id;
            $data['name'] = $item->name;
            $data['name_ar'] = $item->name_ar;
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format('H:i:s') : null;

            // if(!$export) {  
            // }
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

    public function export(Request $request){
        $data = $this->dataQuery($request, true)['data'];
        
        $headings = [
            "#",
            "Property Type",
            "Created At Data",
            "Created At Time"
        ];

        return Excel::download(new ExportData(collect($data),$headings), "data_export_" . time() . ".xlsx" );
    }
    
    public function store(Request $r) {
        $r->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        PropertyType::create([
            'name' => $r->name,
            'name_ar' => $r->name_ar,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Property Type created successfully'
        ], 200);
    }

    public function show($id) { 
        $property_type = PropertyType::find($id);
        
        if (!$property_type) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $property_type
        ], 200);
    }
    
    public function update(Request $r, $id) { 
        $r->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $property_type = PropertyType::find($id);

        if (!$property_type) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        $property_type->update([
            'name' => $r->name,
            'name_ar' => $r->name_ar,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property Type updated successfully'
        ], 200);
    }
    
    public function delete($id) { 
        $property_type = PropertyType::find($id);

        if (!$property_type) {
            return response()->json([
                'status' => false,
                'message' => 'Property Type not found'
            ], 404);
        }

        $property_type->delete();

        return response()->json([
            'status' => true,
            'message' => 'Property Type deleted successfully'
        ], 200);
    }
    
}
