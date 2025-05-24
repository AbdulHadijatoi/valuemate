<?php

namespace App\Http\Controllers;

use App\Exports\ExportData;
use App\Models\BannerAd;
use App\Models\Company;
use App\Models\CompanyDetail;
use App\Models\File;
use App\Models\PropertyType;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{

    public function dataQuery($request, $export = false) {
        
        $data = Company::when($request->search_keyword, function($query) use ($request){
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

        $data = $data->map(function ($company) {
            $data = [];
            $data['id'] = $company->id;
            $data['file'] = $company->logo ? $company->logo->full_path : url(Setting::getValue('placeholder-image'));
            $data['name'] = $company->name ?? '-';
            $data['address'] = $company->companyDetails ? $company->companyDetails->address : '-';
            $data['phone'] = $company->companyDetails ? $company->companyDetails->phone : '-';
            $data['email'] = $company->companyDetails ? $company->companyDetails->email : '-';
            $data['website'] = $company->companyDetails ? $company->companyDetails->website : '-';
            $data['status'] = $company->status;
            $data['description'] = $company->companyDetails ? $company->companyDetails->description ??'-' : '-';
            $data['created_at_date'] = $company->created_at ? Carbon::parse($company->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $company->created_at ? Carbon::parse($company->created_at)->format('H:i:s') : null;
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
            'name' => 'required|string|max:255',
            'file' => 'nullable',
        ]);

        $file = new File();
        $file_path = $file->saveFile($r->file('file'));

        $company = Company::create([
            'name' => $r->name,
            'logo_file_id' => $file->id,
        ]);

        $companyDetails = new CompanyDetail();
        $companyDetails->company_id = $company->id;
        $companyDetails->address = $r->address;
        $companyDetails->email = $r->email;
        $companyDetails->phone = $r->phone;
        $companyDetails->website = $r->website;
        $companyDetails->description = $r->description;
        $companyDetails->save();

        return response()->json([
            'status' => true,
            'message' => 'Company created successfully'
        ], 200);
    }

    public function show($id) { 
        $company = Company::find($id);
        
        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found'
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
            'address' => 'nullable',
            'description' => 'nullable',
            'email' => 'nullable',
            'file' => 'nullable',
            'name' => 'nullable',
            'phone' => 'nullable',
            'status' => 'nullable',
            'website' => 'nullable',
        ]);

        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found'
            ], 404);
        }

        // update file after removing old if uploaded
        $file = null;
        if ($r->hasFile('file')) {
            $file = new File();
            $file_path = $file->saveFile($r->file('file'));

            // delete old file
            $old_file = $company->logo;

            if ($old_file) {
                Storage::delete($old_file->path);
                $old_file->delete();
            }
        }

        $updateData = [
            'name' => $r->name,
        ];

        if($file){
            $updateData['logo_file_id'] = $file->id;
        }

        $company->update($updateData);

        if($company->companyDetails){

            $company->companyDetails->update([
                "address" => $r->address,
                "email" => $r->email,
                "phone" => $r->phone,
                "website" => $r->website,
                "description" => $r->description,
            ]);

        }else{

            $companyDetails = new CompanyDetail();
            $companyDetails->company_id = $company->id;
            $companyDetails->address = $r->address;
            $companyDetails->email = $r->email;
            $companyDetails->phone = $r->phone;
            $companyDetails->website = $r->website;
            $companyDetails->description = $r->description;
            $companyDetails->save();

        }

        return response()->json([
            'status' => true,
            'message' => 'Company details updated successfully'
        ], 200);
    }
    
    public function delete(Request $r) { 
        $company = Company::find($r->id);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found'
            ], 404);
        }

        $old_file = $company->logo;
        
        if ($old_file) {
            Storage::delete($old_file->path);
        }

        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Company deleted successfully'
        ], 200);
    }

    public function export(Request $request){
        $data = $this->dataQuery($request, true)['data'];
        
        $headings = [
            "Id",
            "Image",
            "Name",
            "Address",
            "Phone",
            "Email",
            "Website",
            "Status",
            "Description",
            "Created Date",
            "Created Time"
        ];

        return Excel::download(new ExportData(collect($data),$headings), "data_export_" . time() . ".xlsx" );
    }
    
}
