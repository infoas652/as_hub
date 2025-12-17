<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Get all media files.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $type = $request->input('type'); // 'image' or 'document'

        $query = Media::orderBy('created_at', 'desc');

        if ($type === 'image') {
            $query->images();
        } elseif ($type === 'document') {
            $query->documents();
        }

        $media = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $media
        ]);
    }

    /**
     * Upload a new media file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            
            // Validate file type
            $allowedMimes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File type not allowed. Allowed types: JPG, PNG, GIF, SVG, PDF, DOC, DOCX'
                ], 422);
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . time() . '.' . $extension;

            // Store file
            $path = $file->storeAs('uploads', $filename, 'public');
            $url = Storage::url($path);

            // Create media record
            $media = Media::create([
                'filename' => $filename,
                'original_name' => $originalName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'url' => $url,
                'alt_text' => $request->alt_text,
                'title' => $request->title ?? pathinfo($originalName, PATHINFO_FILENAME),
                'uploaded_by' => auth('api')->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'id' => $media->id,
                    'filename' => $media->filename,
                    'original_name' => $media->original_name,
                    'url' => $media->url,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'human_size' => $media->human_size,
                    'alt_text' => $media->alt_text,
                    'title' => $media->title,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a media file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found'
            ], 404);
        }

        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            // Delete database record
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
