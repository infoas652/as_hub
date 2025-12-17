<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language', 'en');
        $perPage = $request->input('per_page', 15);

        $testimonials = Testimonial::language($language)->ordered()->paginate($perPage);

        return response()->json(['success' => true, 'data' => $testimonials]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:en,ar',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_avatar' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $testimonial = Testimonial::create($request->only([
                'language', 'client_name', 'client_position', 'client_company',
                'client_avatar', 'testimonial', 'rating', 'order', 'is_active'
            ]));
            return response()->json(['success' => true, 'message' => 'Testimonial created successfully', 'data' => $testimonial], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create testimonial', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function show($id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $testimonial]);
    }

    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'sometimes|in:en,ar',
            'client_name' => 'sometimes|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_avatar' => 'nullable|string|max:255',
            'testimonial' => 'sometimes|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $testimonial->update($request->only([
                'language', 'client_name', 'client_position', 'client_company',
                'client_avatar', 'testimonial', 'rating', 'order', 'is_active'
            ]));
            return response()->json(['success' => true, 'message' => 'Testimonial updated successfully', 'data' => $testimonial->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update testimonial', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }

        try {
            $testimonial->delete();
            return response()->json(['success' => true, 'message' => 'Testimonial deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete testimonial', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function toggle($id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }

        try {
            $testimonial->update(['is_active' => !$testimonial->is_active]);
            return response()->json(['success' => true, 'message' => 'Testimonial status updated successfully', 'data' => $testimonial->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to toggle testimonial status', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
}
