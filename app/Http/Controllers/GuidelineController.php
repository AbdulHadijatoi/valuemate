<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Guideline;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuidelineController extends Controller
{
    // create api function for both types of guidelines
    public function getData() {
        $guidelines = Guideline::get();
        $guidelines = $guidelines->map(function($item, $index) {
            $data = [];
            $data['index'] = ++$index;
            $data['id'] = $item->id;
            $data['title'] = $item->title;
            $data['title_ar'] = $item->title_ar;
            $data['description'] = $item->description;
            $data['description_ar'] = $item->description_ar;
            $data['type'] = $item->type;
            return $data;
        });

        return response()->json([
            'status' => true,
            'data' => $guidelines
        ], 200);
    }

    public function getTerms() {
        $type = 'terms_of_service';
        $guidelines = Guideline::where('type', $type)->first(['title','title_ar','description','description_ar']);

        return response()->json([
            'status' => true,
            'data' => [
                'title' => $guidelines->title,
                'title_ar' => $guidelines->title_ar,
                'content' => $guidelines->description,
                'content_ar' => $guidelines->description_ar
            ]
        ], 200);
    }

    public function showPrivacyPolicy() {
        $type = 'privacy_policy';
        $guidelines = Guideline::where('type', $type)->first(['title','description']);

        return view('privacy-policy', compact('guidelines'));
    }
    
    public function getPrivacyPolicy() {
        $type = 'privacy_policy';
        $guidelines = Guideline::where('type', $type)->first(['title','title_ar','description','description_ar']);

        return response()->json([
            'status' => true,
            'data' => [
                'title' => $guidelines->title,
                'title_ar' => $guidelines->title_ar,
                'content' => $guidelines->description,
                'content_ar' => $guidelines->description_ar
            ]
        ], 200);
    }

    // create api function to store guidelines
    public function storeGuideline(Request $request) {
        $request->validate([
            'title' => 'required|string',
            'title_ar' => 'nullable|string',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'type' => 'required|in:privacy_policy,terms_of_service',
        ]);

        Guideline::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Guideline created successfully'
        ], 201);
    }

    public function updateGuideline(Request $request, $id) {
        $request->validate([
            'title' => 'sometimes|required|string',
            'title_ar' => 'nullable|string',
            'description' => 'sometimes|required|string',
            'description_ar' => 'nullable|string',
        ]);

        $guideline = Guideline::findOrFail($id);
        $guideline->update([
            'title' => $request->input('title', $guideline->title),
            'title_ar' => $request->input('title_ar', $guideline->title_ar),
            'description' => $request->input('description', $guideline->description),
            'description_ar' => $request->input('description_ar', $guideline->description_ar),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Guideline updated successfully'
        ], 200);
    }
    
}
