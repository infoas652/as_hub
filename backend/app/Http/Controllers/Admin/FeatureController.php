<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language');

        $query = Feature::query()->ordered();

        // Apply language filter only if specified
        if ($language && $language !== 'all') {
            $query->language($language);
        }

        $features = $query->get();

        return response()->json(['success' => true, 'data' => $features]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:en,ar',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $feature = Feature::create($request->only(['language', 'title', 'description', 'icon', 'order', 'is_active']));
            return response()->json(['success' => true, 'message' => 'Feature created successfully', 'data' => $feature], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create feature', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function show($id)
    {
        $feature = Feature::find($id);
        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $feature]);
    }

    public function update(Request $request, $id)
    {
        $feature = Feature::find($id);
        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'sometimes|in:en,ar',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $feature->update($request->only(['language', 'title', 'description', 'icon', 'order', 'is_active']));
            return response()->json(['success' => true, 'message' => 'Feature updated successfully', 'data' => $feature->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update feature', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function destroy($id)
    {
        $feature = Feature::find($id);
        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }

        try {
            $feature->delete();
            return response()->json(['success' => true, 'message' => 'Feature deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete feature', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function toggle($id)
    {
        $feature = Feature::find($id);
        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found'], 404);
        }

        try {
            $feature->update(['is_active' => !$feature->is_active]);
            return response()->json(['success' => true, 'message' => 'Feature status updated successfully', 'data' => $feature->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to toggle feature status', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
}
