<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language', 'en');
        $category = $request->input('category');
        $perPage = $request->input('per_page', 15);

        $query = Faq::language($language)->ordered();

        if ($category) {
            $query->category($category);
        }

        $faqs = $query->paginate($perPage);

        return response()->json(['success' => true, 'data' => $faqs]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:en,ar',
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $faq = Faq::create($request->only(['language', 'question', 'answer', 'category', 'order', 'is_active']));
            return response()->json(['success' => true, 'message' => 'FAQ created successfully', 'data' => $faq], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create FAQ', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function show($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $faq]);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'sometimes|in:en,ar',
            'question' => 'sometimes|string',
            'answer' => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $faq->update($request->only(['language', 'question', 'answer', 'category', 'order', 'is_active']));
            return response()->json(['success' => true, 'message' => 'FAQ updated successfully', 'data' => $faq->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update FAQ', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }

        try {
            $faq->delete();
            return response()->json(['success' => true, 'message' => 'FAQ deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete FAQ', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    public function toggle($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }

        try {
            $faq->update(['is_active' => !$faq->is_active]);
            return response()->json(['success' => true, 'message' => 'FAQ status updated successfully', 'data' => $faq->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to toggle FAQ status', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
}
