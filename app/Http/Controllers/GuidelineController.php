<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Guideline;
use App\Models\Setting;
use App\Traits\Cacheable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GuidelineController extends Controller
{
    use Cacheable;

    // create api function for both types of guidelines
    public function getData() {
        return $this->remember('guidelines_data', function () {
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
        }, 3600);
    }

    public function getTerms() {
        return $this->remember('guideline_terms', function () {
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
        }, 3600);
    }

    public function showPrivacyPolicy() {
        $type = 'privacy_policy';
        $guidelines = Guideline::where('type', $type)->first(['title','description']);

        return view('privacy-policy', compact('guidelines'));
    }
    
    public function getPrivacyPolicy() {
        return $this->remember('guideline_privacy', function () {
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
        }, 3600);
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

        // Clear related caches
        $this->clearResourceCache('guidelines');
        $this->clearConstantCaches();
        Cache::forget('guideline_terms');
        Cache::forget('guideline_privacy');

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

        // Clear related caches
        $this->clearResourceCache('guidelines');
        $this->clearConstantCaches();
        Cache::forget('guideline_terms');
        Cache::forget('guideline_privacy');

        return response()->json([
            'status' => true,
            'message' => 'Guideline updated successfully'
        ], 200);
    }
    
}
