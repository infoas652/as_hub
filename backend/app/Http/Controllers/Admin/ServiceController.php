<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $language = $request->input('language');
            $perPage = $request->input('per_page', 100);

            $query = Service::query();

            // Filter by language if provided
            if ($language && $language !== 'all') {
                $query->where('language', $language);
            }

            // Order by order field
            $query->orderBy('order', 'asc')->orderBy('id', 'desc');

            $services = $query->get();

            return response()->json([
                'success' => true,
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load services',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Convert features from JSON string to array if needed
            $data = $request->all();
            if (isset($data['features']) && is_string($data['features'])) {
                $data['features'] = json_decode($data['features'], true);
            }

            $validator = Validator::make($data, [
                'language' => 'required|in:en,ar',
                'title' => 'required|string|max:255',
                'icon' => 'nullable|string|max:255',
                'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'features.*' => 'string',
                'order' => 'nullable|integer',
                'is_active' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $iconPath = $request->icon ?? '🛠️';

            // Handle file upload
            if ($request->hasFile('icon_file')) {
                $file = $request->file('icon_file');
                $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/services'), $filename);
                $iconPath = '/uploads/services/' . $filename;
            }

            $service = Service::create([
                'language' => $data['language'],
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'icon' => $iconPath,
                'description' => $data['description'] ?? '',
                'features' => $data['features'] ?? [],
                'order' => $data['order'] ?? 0,
                'is_active' => filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Update the specified service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        try {
            // Convert features from JSON string to array if needed
            $data = $request->all();
            if (isset($data['features']) && is_string($data['features'])) {
                $data['features'] = json_decode($data['features'], true);
            }

            $validator = Validator::make($data, [
                'language' => 'sometimes|in:en,ar',
                'title' => 'sometimes|string|max:255',
                'icon' => 'nullable|string|max:255',
                'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'features.*' => 'string',
                'order' => 'nullable|integer',
                'is_active' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];

            if (isset($data['language'])) $updateData['language'] = $data['language'];
            if (isset($data['title'])) {
                $updateData['title'] = $data['title'];
                $updateData['slug'] = Str::slug($data['title']);
            }
            if (isset($data['icon'])) $updateData['icon'] = $data['icon'];
            if (isset($data['description'])) $updateData['description'] = $data['description'];
            if (isset($data['features'])) $updateData['features'] = $data['features'];
            if (isset($data['order'])) $updateData['order'] = $data['order'];
            if (isset($data['is_active'])) $updateData['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);

            // Handle file upload
            if ($request->hasFile('icon_file')) {
                // Delete old file if exists
                if ($service->icon && file_exists(public_path($service->icon))) {
                    @unlink(public_path($service->icon));
                }

                $file = $request->file('icon_file');
                $filename = time() . '_' . Str::slug($data['title'] ?? $service->title) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/services'), $filename);
                $updateData['icon'] = '/uploads/services/' . $filename;
            }

            $service->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        try {
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Toggle service active status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        try {
            $service->update(['is_active' => !$service->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Service status updated successfully',
                'data' => $service->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle service status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
