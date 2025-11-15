<?php

namespace App\Http\Controllers;

use App\Exports\ExportData;
use App\Mail\StatusUpdatedMail;
use App\Models\DocumentRequirement;
use App\Models\File;
use App\Models\PropertyServiceType;
use App\Models\ServicePricing;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Models\ValuationRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ValuationRequestController extends Controller
{
    public function dataQuery($request, $export = false) {
        
        $data = ValuationRequest::when($request->search_keyword, function($query) use ($request){
            $query->where('name', 'like', '%' . $request->search_keyword . '%');
        })
        ->when($request->from_date && $request->to_date, function ($query) use ($request) {
            return $query->whereDate('created_at','>=', $request->from_date)
                        ->whereDate('created_at','<=', $request->to_date);
        })
        ->orderBy('id', 'desc');

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
            $data['company_name'] = $item->company ? $item->company->name : '-';
            $data['user_name'] = $item->user ? $item->user->first_name .' '. $item->user->last_name : '-';
            $data['property_type'] = $item->propertyType ? $item->propertyType->name : '-';
            $data['property_type_ar'] = $item->propertyType ? $item->propertyType->name_ar : '-';
            $data['service_type'] = $item->serviceType ? $item->serviceType->name : '-';
            $data['service_type_ar'] = $item->serviceType ? $item->serviceType->name_ar : '-';
            $data['request_type'] = $item->requestType ? $item->requestType->name : '-';
            $data['request_type_ar'] = $item->requestType ? $item->requestType->name_ar : '-';
            $data['location'] = $item->location ? $item->location->name : '-';
            $data['location_ar'] = $item->location ? $item->location->name_ar : '-';
            $data['service_pricing'] = $item->servicePricing ? $item->servicePricing->price : 'default';
            $data['area'] = $item->area ?? '-';
            $data['total_amount'] = $item->total_amount ?? '-';
            $data['status'] = $item->status ? $item->status->name : '-';
            $data['status_ar'] = $item->status ? $item->status->name_ar : '-';
            $data['reference'] = $item->reference ?? '-';
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format('H:i:s') : null;

            if(!$export) {  
                $data['service_type_id'] = $item->service_type_id;
                $data['property_type_id'] = $item->property_type_id;
                $data['has_documents'] = $item->documents ->count() > 0 ? true : false;
                $requiredDocs = DocumentRequirement::where('property_type_id', $item->property_type_id)
                    ->where('service_type_id', $item->service_type_id)
                    ->get(['id', 'document_name', 'document_name_ar', 'is_file']);
                $data['required_documents'] = $requiredDocs && $requiredDocs->count() > 0? $requiredDocs :null;
            }
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
            "Id",
            "Company Name",
            "User Name",
            "Property Type",
            "Service Type",
            "Request Type",
            "Location",
            "Service Pricing",
            "Area (meter squire)",
            "Total Amount",
            "Status",
            "Reference",
            "Created At Date",
            "Created At Time"
        ];

        return Excel::download(new ExportData(collect($data),$headings), "data_export_" . time() . ".xlsx" );
    }
    
    
    public function viewDocuments(Request $r) {
        $r->validate([
            'valuation_request_id' => 'required|exists:valuation_requests,id',
        ]);

        $valuationRequest = ValuationRequest::find($r->valuation_request_id);
    
        if (!$valuationRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        $documents = $valuationRequest->documents()->get();
       
        $documents = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'document_name' => $document->documentRequirement ?$document->documentRequirement->document_name:null,
                'document_name_ar' => $document->documentRequirement ?$document->documentRequirement->document_name_ar:null,
                'is_file' => $document->documentRequirement ? $document->documentRequirement->is_file : null,
                'full_path' => $document->document ? $document->document->full_path : null,
                'text_value' => $document->text_value
            ];
        });
        return response()->json([
            'status' => true,
            'data' => $documents
        ], 200);
    }

    // public function store2(Request $r) {
    //     $r->validate([
    //         'company_id' => 'required|exists:companies,id',
    //         'user_id' => 'nullable|exists:users,id',
    //         'property_type_id' => 'required|exists:property_types,id',
    //         'service_type_id' => 'nullable|exists:service_types,id',
    //         'request_type_id' => 'required|exists:request_types,id',
    //         'location_id' => 'required|exists:locations,id',
    //         // 'service_pricing_id' => 'nullable|exists:service_pricings,id',
    //         'area' => 'required|numeric',
    //     ]);

    //     if($r->valuation_request_id && $r->valuation_request_id != null && $r->valuation_request_id != 0){
    //         return $this->update($r, $r->valuation_request_id);
    //     }

    //     if($r->user_id && !auth()->user()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'You do not have permission to create a valuation request for another user.'
    //         ], 403);
    //     }

    //     $user_id = $r->user_id ?? auth()->id();
    
    //     // calculate total amount based on service pricing
    //     $service_pricing_id = null;
    //     // if($r->service_pricing_id) {
           
    //     //     $servicePricing = ServicePricing::find($r->service_pricing_id);
    //     //     if (!$servicePricing) {
    //     //         return response()->json([
    //     //             'status' => false,
    //     //             'message' => 'Service Pricing not found.'
    //     //         ], 404);
    //     //     }
    //     //     $service_pricing_id = $servicePricing->id;
    //     //     $total_amount = $servicePricing->price;
    //     // } else {
    //     //     // If no service pricing is provided, set total amount to 0
    //     //     $total_amount = $r->total_amount;
    //     // }            

    //     $reference = 'VR-' . time() . '-' . $user_id;

    //     $valuation_request = ValuationRequest::create([
    //         "company_id" => $r->company_id,
    //         "user_id" => $user_id,
    //         "property_type_id" => $r->property_type_id,
    //         "service_type_id" => $r->service_type_id,
    //         "request_type_id" => $r->request_type_id,
    //         "location_id" => $r->location_id,
    //         // "service_pricing_id" => $service_pricing_id??null,
    //         "area" => $r->area,
    //         "total_amount" => $total_amount,
    //         "reference" => $reference
    //     ]);
    
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Valuation Request created.',
    //         'data' => $valuation_request
    //     ], 200);
    // }

    public function store(Request $r) {
        $r->validate([
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'nullable|exists:users,id',
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'nullable|exists:service_types,id',
            'request_type_id' => 'required|exists:request_types,id',
            'location_id' => 'required|exists:locations,id',
            'area' => 'required|numeric',
        ]);

        if($r->valuation_request_id && $r->valuation_request_id != null && $r->valuation_request_id != 0){
            return $this->update($r, $r->valuation_request_id);
        }

        if($r->user_id && !auth()->user()) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to create a valuation request for another user.'
            ], 403);
        }

        $user_id = $r->user_id ?? auth()->id();

        // Default amount logic (based on request_type_id)
        $default_amount = ($r->request_type_id == 2) ? 50 : 25;

        // Try to find a matching service pricing
        $match = ServicePricing::where('service_type_id', $r->service_type_id)
            ->where('property_type_id', $r->property_type_id)
            ->where('company_id', $r->company_id)
            ->where('request_type_id', $r->request_type_id)
            ->where('area_from', '<=', $r->area)
            ->where('area_to', '>=', $r->area)
            ->first();

        $service_pricing_id = $match ? $match->id : null;
        $total_amount = $match ? $match->price : $default_amount;

        $reference = 'VR-' . time() . '-' . $user_id;

        $valuation_request = ValuationRequest::create([
            "company_id" => $r->company_id,
            "user_id" => $user_id,
            "property_type_id" => $r->property_type_id,
            "service_type_id" => $r->service_type_id,
            "request_type_id" => $r->request_type_id,
            "location_id" => $r->location_id,
            "area" => $r->area,
            "total_amount" => $total_amount,
            "reference" => $reference,
            "service_pricing_id" => $service_pricing_id // Save it if needed
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Valuation Request created.',
            'data' => [
                'id' => $valuation_request->id,
                'amount' => $valuation_request->total_amount
            ]
        ], 200);
    }


    public function update(Request $r, $id) {
        $r->validate([
            'company_id' => 'required|exists:companies,id',
            'status_id' => 'nullable|exists:valuation_request_statuses,id',
            'property_type_id' => 'required|exists:property_types,id',
            'service_type_id' => 'required|exists:service_types,id',
            'request_type_id' => 'required|exists:request_types,id',
            'location_id' => 'required|exists:locations,id',
            'service_pricing_id' => 'nullable|exists:service_pricings,id',
            'area' => 'required|numeric',
        ]);
    
        $data = ValuationRequest::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }

        // Default amount logic (based on request_type_id)
        $default_amount = ($r->request_type_id == 2) ? 50 : 25;

        // Try to find a matching service pricing
        $match = ServicePricing::where('service_type_id', $r->service_type_id)
            ->where('property_type_id', $r->property_type_id)
            ->where('company_id', $r->company_id)
            ->where('request_type_id', $r->request_type_id)
            ->where('area_from', '<=', $r->area)
            ->where('area_to', '>=', $r->area)
            ->first();

        $service_pricing_id = $match ? $match->id : null;
        $total_amount = $match ? $match->price : $default_amount;
    
        $updateData = [
            "company_id" => $r->company_id,
            "property_type_id" => $r->property_type_id,
            "service_type_id" => $r->service_type_id,
            "request_type_id" => $r->request_type_id,
            "location_id" => $r->location_id,
            "service_pricing_id" => $service_pricing_id??null,
            "area" => $r->area,
            "total_amount" => $total_amount,
        ];

        if($r->status_id){
            $updateData["status_id"] = $r->status_id;
        }

        $data->update($updateData);
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request updated.',
            'data' => [
                'id' => $data->id,
                'amount' => $data->total_amount
            ]
        ], 200);
    }

    public function show($id) {
        $data = ValuationRequest::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
    
    public function requestHistory() {
        $data = ValuationRequest::with([
                    'company',
                    'user',
                    'propertyType',
                    'serviceType',
                    'requestType',
                    'location',
                    'servicePricing',
                    'status',
                    'lastPayment'
                ])
                ->whereHas('payments')
                ->where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get();
        $data = $data->map(function ($item){
            $company = $item->company;
            $data = [];
            $data['id'] = $item->id;
            $data['company_name'] = $item->company ? $item->company->name : '-';
            $data['company_logo'] = $company->logo ? $company->logo->full_path : url(Setting::getValue('placeholder-image'));
            $data['user_name'] = $item->user ? $item->user->first_name .' '. $item->user->last_name : '-';
            $data['property_type'] = $item->propertyType ? $item->propertyType->name : '-';
            $data['property_type_ar'] = $item->propertyType ? $item->propertyType->name_ar : '-';
            $data['service_type'] = $item->serviceType ? $item->serviceType->name : '-';
            $data['service_type_ar'] = $item->serviceType ? $item->serviceType->name_ar : '-';
            $data['request_type'] = $item->requestType ? $item->requestType->name : '-';
            $data['request_type_ar'] = $item->requestType ? $item->requestType->name_ar : '-';
            $data['location'] = $item->location ? $item->location->name : '-';
            $data['location_ar'] = $item->location ? $item->location->name_ar : '-';
            $data['service_pricing'] = $item->servicePricing ? $item->servicePricing->price : 'default';
            $data['area'] = $item->area ?? '-';
            $data['total_amount'] = $item->total_amount ?? '-';
            $data['status_id'] = $item->status_id;
            $data['status'] = $item->status ? $item->status->name : '-';
            $data['status_ar'] = $item->status ? $item->status->name_ar : '-';
            $data['reference'] = $item->reference ?? '-';
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format('H:i:s') : null;
            $data['payment_status'] = $item->lastPayment ? $item->lastPayment->status: null;
            return $data;
        });

        return [
            'status' => true,
            'data' => $data,
        ];
    }

    public function updateStatus(Request $r) {
        $r->validate([
            'valuation_request_id' => 'required|exists:valuation_requests,id',
            'status_id' => 'required|exists:valuation_request_statuses,id',
        ]);
    
        $valuationRequest = ValuationRequest::find($r->valuation_request_id);
    
        if (!$valuationRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        $valuationRequest->update([
            'status_id' => $r->status_id
        ]);

        if ($valuationRequest->user && $valuationRequest->user->email) {
            $data = [
                'user_name' => $valuationRequest->user->first_name . ' ' . $valuationRequest->user->last_name,
                'reference' => $valuationRequest->reference,
                'status' => optional($valuationRequest->status)->name ?? '-',
                'property_type' => optional($valuationRequest->propertyType)->name ?? '-',
                'location' => optional($valuationRequest->location)->name ?? '-',
                'created_at_date' => $valuationRequest->created_at ? Carbon::parse($valuationRequest->created_at)->format('Y-m-d') : null,
                'created_at_time' => $valuationRequest->created_at ? Carbon::parse($valuationRequest->created_at)->format('H:i:s') : null,
            ];

            // Load documents with relationships
            $valuationRequest->load('documents.document', 'documents.documentRequirement');
            
            // Prepare documents data for email
            $documents = [];
            foreach ($valuationRequest->documents as $document) {
                $docData = [
                    'document_name' => $document->documentRequirement ? $document->documentRequirement->document_name : null,
                    'document_name_ar' => $document->documentRequirement ? $document->documentRequirement->document_name_ar : null,
                    'is_file' => $document->documentRequirement ? $document->documentRequirement->is_file : null,
                ];

                if ($document->documentRequirement && $document->documentRequirement->is_file && $document->document) {
                    // File document
                    $docData['file_path'] = $document->document->path;
                    $docData['file_type'] = $document->document->type;
                } else if ($document->text_value) {
                    // Text document
                    $docData['text_value'] = $document->text_value;
                }

                $documents[] = $docData;
            }

            // Queue the email
            Mail::to($valuationRequest->user->email)->queue(new StatusUpdatedMail($data, $documents));
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request status updated.'
        ], 200);
    }

    

    public function delete($id) {
        $data = ValuationRequest::find($id);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        $data->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'Valuation Request deleted.'
        ], 200);
    }

    public function uploadDocuments(Request $r) {
        // dd($r->all());
        $r->validate([
            'valuation_request_id' => 'required|exists:valuation_requests,id',
            'document_requirement_id' => 'required|array',
            'document_requirement_id.*' => 'required|exists:document_requirements,id',
            'document_file' => 'nullable|array',
            'document_file.*' => 'nullable|file',
            'document_text_value' => 'nullable|array',
        ]);
    

        $valuationRequest = ValuationRequest::find($r->valuation_request_id);
    
        if (!$valuationRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Valuation Request not found.'
            ], 404);
        }
    
        // Normalize inputs
        $requirementIds = is_array($r->document_requirement_id) ? $r->document_requirement_id : [];
        $files = $r->file('document_file') ?: [];
        $texts = is_array($r->document_text_value) ? $r->document_text_value : [];

        foreach ($requirementIds as $index => $requirementId) {
            if (!$requirementId) {
                continue;
            }

            // Load requirement to know if it expects a file or text
            $requirement = DocumentRequirement::find($requirementId);
            if (!$requirement) {
                continue;
            }

            // If requirement expects a file, look for an uploaded file at same index
            if ($requirement->is_file) {
                $fileInput = $files[$index] ?? null;
                if (!$fileInput) {
                    // no file provided for this requirement, skip
                    continue;
                }

                $file = new File();
                $file_path = $file->saveFile($fileInput);

                $valuationRequest->documents()->create([
                    'document_requirement_id' => $requirementId,
                    'file_id' => $file->id,
                    'text_value' => null,
                ]);
            } else {
                // requirement expects text value
                $textValue = $texts[$index] ?? null;
                if ($textValue === null || $textValue === '') {
                    // no text provided, skip
                    continue;
                }

                $valuationRequest->documents()->create([
                    'document_requirement_id' => $requirementId,
                    'file_id' => null,
                    'text_value' => $textValue,
                ]);
            }
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Documents uploaded successfully.'
        ], 200);
    }
    
}
