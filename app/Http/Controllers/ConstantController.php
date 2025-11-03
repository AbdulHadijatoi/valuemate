<?php

namespace App\Http\Controllers;

use App\Models\BannerAd;
use App\Models\Company;
use App\Models\DocumentRequirement;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\PropertyServiceType;
use App\Models\PropertyType;
use App\Models\RequestType;
use App\Models\ServicePricing;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Models\ValuationRequestStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConstantController extends Controller
{
    public function getData() { 
        $service_types = ServiceType::all(); 
        $companies = Company::all(); 
        $locations = Location::all(); 
        $propertyTypes = PropertyType::all(); 
        $requestTypes = RequestType::all(); 
        $requiredDocuments = DocumentRequirement::get(['property_type_id', 'service_type_id','document_name', 'document_name_ar', 'is_file', 'id']); 
        $propertyServiceTypes = PropertyServiceType::with(['propertyType', 'serviceType'])->get();
        $payment_methods = PaymentMethod::with('logo')->get();

        $payment_methods = $payment_methods->map(function ($item) {
            $data = [];

            $data['id'] = $item->id;
            $data['name'] = $item->name ?? '-';
            $data['name_ar'] = $item->name_ar ?? '-';
            $data['status'] = $item->status ?? '-';
            $data['image_url'] = $item->logo ? $item->logo->full_path : null;
            return $data;
        });

        $statuses = ValuationRequestStatus::get();
        $propertyServiceTypes = $propertyServiceTypes->groupBy(function ($item) {
            return $item->propertyType->name ?? 'Unknown'; // Group by property type name
        })->map(function ($items, $propertyTypeName) {
            return [
                'property_type' => $propertyTypeName,
                'property_type_ar' => $items->first()->propertyType->name_ar ?? null,
                'property_type_id' => $items->first()->property_type_id,
                'services' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_type_id' => $item->service_type_id,
                        'service_type' => $item->serviceType->name ?? null,
                        'service_type_ar' => $item->serviceType->name_ar ?? null,
                    ];
                })->values(),
            ];
        })->values();

        $companies = $companies->map(function ($company) {
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
            $data['description_ar'] = $company->companyDetails ? $company->companyDetails->description_ar ??'-' : '-';
            return $data;
        });

        $propertyTypes = $propertyTypes->map(function ($propertyType) {
            return [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
                'name_ar' => $propertyType->name_ar,
            ];
        });

        $service_types = $service_types->map(function ($serviceType) {
            return [
                'service_type_id' => $serviceType->id,
                'service_type' => $serviceType->name,
                'service_type_ar' => $serviceType->name_ar,
            ];
        });
        $locations = $locations->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'name_ar' => $location->name_ar,
            ];
        });

        $service_pricings = ServicePricing::get(['id','service_type_id','property_type_id','company_id','request_type_id', 'area_from', 'area_to','price']);
        
        $settings = Setting::get();

        $banners = BannerAd::with([
            'banner'
        ])->get();

        $banners = $banners->map(function ($item) {
            $data = [];

            $data['id'] = $item->id;
            $data['title'] = $item->title ?? '-';
            $data['title_ar'] = $item->title_ar ?? '-';
            $data['description'] = $item->description ?? '-';
            $data['description_ar'] = $item->description_ar ?? '-';
            $data['image_url'] = $item->banner ? $item->banner->full_path : null;
            $data['link'] = $item->link ?? '-';
            $data['ad_type'] = $item->ad_type ?? '-';
            $data['start_date'] = $item->start_date ? Carbon::parse($item->start_date)->format('Y-m-d') : null;
            $data['end_date'] = $item->end_date ? Carbon::parse($item->end_date)->format('Y-m-d') : null;
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format('H:i:s') : null;
            return $data;
        });

        $data = [
            'payment_methods' => $payment_methods,
            'service_types' => $service_types,
            'companies' => $companies,
            'locations' => $locations,
            'property_types' => $propertyTypes,
            'property_service_types' => $propertyServiceTypes,
            'request_types' => $requestTypes,
            'service_pricings' => $service_pricings,
            'document_requirements' => $requiredDocuments,
            'statuses' => $statuses,
            'banners' => $banners,
            "settings" => $settings,
        ];

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function getSettingValue(Request $request) { 

        $value = Setting::where('key', $request->key)->first(['value','is_file']);
        
        if (!$value) {
            return response()->json([
                'status' => false,
                'message' => "Setting option not found"
            ], 404);
        }

        if($value->is_file){
            $value = url(Storage::url($value->value));
        }else{
            $value = $value->value;
        }

        return response()->json([
            'status' => true,
            'data' => $value
        ], 200);
    }
    
    public function constantData(){
        $property_types = PropertyType::get(['id','name','name_ar']);
        $companies = Company::get(['id','name','name_ar']);
        $request_types = RequestType::get(['id','name','name_ar','description','description_ar']);
        $service_types = ServiceType::get(['id','name','name_ar']);
        $data = PropertyServiceType::with(['propertyType', 'serviceType'])->get();

        $property_service_types = $data->groupBy(function ($item) {
            return $item->propertyType->name ?? 'Unknown'; // Group by property type name
        })->map(function ($items, $propertyTypeName) {
            return [
                'property_type' => $propertyTypeName,
                'property_type_ar' => $items->first()->propertyType->name_ar ?? null,
                'property_type_id' => $items->first()->property_type_id,
                'services' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_type_id' => $item->service_type_id,
                        'service_type_name' => $item->serviceType->name ?? null,
                        'service_type_name_ar' => $item->serviceType->name_ar ?? null,
                        'created_at_date' => $item->serviceType && $item->serviceType->created_at ? Carbon::parse($item->serviceType->created_at)->format("Y-m-d") : null,
                        'created_at_time' => $item->serviceType && $item->serviceType->created_at ? Carbon::parse($item->serviceType->created_at)->format("H:i:s") : null,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                "property_service_types" => $property_service_types,
                "service_types" => $service_types,
                "property_types" => $property_types,
                "companies" => $companies,
                "request_types" => $request_types,
            ]
        ], 200);
    }
}
