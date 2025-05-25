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

class ConstantController extends Controller
{
    public function getData() { 
        $service_types = ServiceType::all(); 
        $companies = Company::all(); 
        $locations = Location::all(); 
        $propertyTypes = PropertyType::all(); 
        $requestTypes = RequestType::all(); 
        $requiredDocuments = DocumentRequirement::get(['property_type_id', 'service_type_id','document_name']); 
        $propertyServiceTypes = PropertyServiceType::with(['propertyType', 'serviceType'])->get();
        $payment_methods = PaymentMethod::get(['id', 'name']);
        $statuses = ValuationRequestStatus::get();
        $propertyServiceTypes = $propertyServiceTypes->groupBy(function ($item) {
            return $item->propertyType->name ?? 'Unknown'; // Group by property type name
        })->map(function ($items, $propertyTypeName) {
            return [
                'property_type' => $propertyTypeName,
                'property_type_id' => $items->first()->property_type_id,
                'services' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_type_id' => $item->service_type_id,
                        'service_type' => $item->serviceType->name ?? null,
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
            return $data;
        });

        $propertyTypes = $propertyTypes->map(function ($propertyType) {
            return [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
            ];
        });

        $service_types = $service_types->map(function ($serviceType) {
            return [
                'service_type_id' => $serviceType->id,
                'service_type' => $serviceType->name,
            ];
        });
        $locations = $locations->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
            ];
        });

        $service_pricings = ServicePricing::get(['id','service_type_id','property_type_id','company_id','request_type_id', 'area_from', 'area_to','price']);
        
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
        ];

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function getSettingValue(Request $request) { 

        $value = Setting::getValue($request->key);
        
        if (!$value) {
            return response()->json([
                'status' => false,
                'message' => "Setting option not found"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $value
        ], 200);
    }
     
}
