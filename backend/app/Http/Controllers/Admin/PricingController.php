<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PricingController extends Controller
{
    /**
     * Display a listing of pricing plans.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $language = $request->input('language');
        $serviceType = $request->input('service_type');
        $tier = $request->input('tier');

        $query = PricingPlan::query();
        
        // Filter by language if specified
        if ($language && $language !== 'all') {
            $query->where('language', $language);
        }

        // Filter by service type if specified
        if ($serviceType && $serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }

        // Filter by tier if specified
        if ($tier && $tier !== 'all') {
            $query->where('tier', $tier);
        }
        
        $plans = $query->ordered()->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'language' => $plan->language,
                'service_type' => $plan->service_type,
                'tier' => $plan->tier,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price_monthly' => (float) $plan->price_monthly,
                'price_yearly' => (float) $plan->price_yearly,
                'features' => $plan->features,
                'is_popular' => $plan->is_popular,
                'order' => $plan->order,
                'is_active' => $plan->is_active,
                'savings' => $plan->savings,
                'savings_percentage' => $plan->savings_percentage,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    /**
     * Store a newly created pricing plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:en,ar',
            'service_type' => 'required|in:website,app,both',
            'tier' => 'required|in:basic,professional,enterprise',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string',
            'is_popular' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicate (same language, service_type, and tier)
            $exists = PricingPlan::where('language', $request->language)
                ->where('service_type', $request->service_type)
                ->where('tier', $request->tier)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A pricing plan with this language, service type, and tier already exists'
                ], 422);
            }

            $plan = PricingPlan::create([
                'language' => $request->language,
                'service_type' => $request->service_type,
                'tier' => $request->tier,
                'name' => $request->name,
                'slug' => $request->slug ?? Str::slug($request->name),
                'description' => $request->description,
                'price_monthly' => $request->price_monthly,
                'price_yearly' => $request->price_yearly,
                'features' => $request->features,
                'is_popular' => $request->is_popular ?? false,
                'order' => $request->order ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pricing plan created successfully',
                'data' => $plan
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pricing plan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified pricing plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $plan = PricingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing plan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $plan->id,
                'language' => $plan->language,
                'service_type' => $plan->service_type,
                'tier' => $plan->tier,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price_monthly' => (float) $plan->price_monthly,
                'price_yearly' => (float) $plan->price_yearly,
                'features' => $plan->features,
                'is_popular' => $plan->is_popular,
                'order' => $plan->order,
                'is_active' => $plan->is_active,
                'savings' => $plan->savings,
                'savings_percentage' => $plan->savings_percentage,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified pricing plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $plan = PricingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing plan not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'sometimes|in:en,ar',
            'service_type' => 'sometimes|in:website,app,both',
            'tier' => 'sometimes|in:basic,professional,enterprise',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price_monthly' => 'sometimes|numeric|min:0',
            'price_yearly' => 'sometimes|numeric|min:0',
            'features' => 'sometimes|array|min:1',
            'features.*' => 'required|string',
            'is_popular' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicate if language, service_type, or tier is being changed
            if ($request->has('language') || $request->has('service_type') || $request->has('tier')) {
                $language = $request->input('language', $plan->language);
                $serviceType = $request->input('service_type', $plan->service_type);
                $tier = $request->input('tier', $plan->tier);

                $exists = PricingPlan::where('language', $language)
                    ->where('service_type', $serviceType)
                    ->where('tier', $tier)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A pricing plan with this language, service type, and tier already exists'
                    ], 422);
                }
            }

            $updateData = $request->only([
                'language', 'service_type', 'tier', 'name', 'description', 
                'price_monthly', 'price_yearly', 'features', 'is_popular', 
                'order', 'is_active'
            ]);

            if (isset($updateData['name'])) {
                $updateData['slug'] = $request->slug ?? Str::slug($updateData['name']);
            }

            $plan->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Pricing plan updated successfully',
                'data' => $plan->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing plan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified pricing plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $plan = PricingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing plan not found'
            ], 404);
        }

        try {
            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pricing plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pricing plan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Toggle pricing plan active status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $plan = PricingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing plan not found'
            ], 404);
        }

        try {
            $plan->update(['is_active' => !$plan->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Pricing plan status updated successfully',
                'data' => $plan->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle pricing plan status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
