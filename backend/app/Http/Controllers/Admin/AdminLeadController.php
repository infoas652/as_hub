<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminLeadController extends Controller
{
    /**
     * Get all leads.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $processed = $request->input('processed');
        $search = $request->input('search');

        $query = Lead::with('processedBy:id,name,email')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->status($status);
        }

        if ($processed === 'true') {
            $query->processed();
        } elseif ($processed === 'false') {
            $query->unprocessed();
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads
        ]);
    }

    /**
     * Get a specific lead.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $lead = Lead::with('processedBy:id,name,email')->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lead
        ]);
    }

    /**
     * Update a lead.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:new,contacted,qualified,converted,closed',
            'notes' => 'nullable|string',
            'is_processed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['status', 'notes', 'is_processed']);

            if (isset($updateData['is_processed']) && $updateData['is_processed'] && !$lead->is_processed) {
                $updateData['processed_at'] = now();
                $updateData['processed_by'] = auth('api')->id();
            }

            $lead->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully',
                'data' => $lead->fresh()->load('processedBy:id,name,email')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a lead.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found'
            ], 404);
        }

        try {
            $lead->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lead',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark a lead as processed.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsProcessed($id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found'
            ], 404);
        }

        try {
            $lead->markAsProcessed(auth('api')->id());

            return response()->json([
                'success' => true,
                'message' => 'Lead marked as processed',
                'data' => $lead->fresh()->load('processedBy:id,name,email')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark lead as processed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Export leads to CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $status = $request->input('status');
        $processed = $request->input('processed');

        $query = Lead::with('processedBy:id,name')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->status($status);
        }

        if ($processed === 'true') {
            $query->processed();
        } elseif ($processed === 'false') {
            $query->unprocessed();
        }

        $leads = $query->get();

        $filename = 'leads-export-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Company', 'Message', 
                'Plan', 'Status', 'Processed', 'Processed By', 'Created At'
            ]);

            // Add data rows
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->email,
                    $lead->phone,
                    $lead->company,
                    $lead->message,
                    $lead->plan,
                    $lead->status,
                    $lead->is_processed ? 'Yes' : 'No',
                    $lead->processedBy ? $lead->processedBy->name : '',
                    $lead->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
