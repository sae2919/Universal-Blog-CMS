<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function jsonList(Request $request)
    {
        $search = $request->input('search');

        $mediaList = Media::latest()
            ->when($search, function ($query) use ($search) {
                $query->where('file_name', 'like', "%{$search}%");
            })
            ->where('mime_type', 'like', 'image/%')
            ->get()
            ->map(function ($media) {
                return [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'url' => $media->url,
                    'file_path' => $media->file_path,
                    'mime_type' => $media->mime_type,
                    'file_size' => $media->file_size,
                ];
            });

        return response()->json($mediaList);
    }

    public function jsonUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        if ($request->file('file')) {
            $file = $request->file('file');
            
            // Generate clean name
            $originalName = $file->getClientOriginalName();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $cleanName = \Str::slug($fileName) . '-' . time() . '.' . $extension;

            // Store in storage/app/public/uploads
            $filePath = $file->storeAs('uploads', $cleanName, 'public');

            // Save record
            $media = Media::create([
                'file_name'   => $originalName,
                'file_path'   => $filePath,
                'mime_type'   => $file->getClientMimeType(),
                'file_size'   => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            return response()->json([
                'location' => $media->url,
                'path' => $media->file_path,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'file_size' => $media->file_size,
            ]);
        }

        return response()->json(['error' => 'Failed to upload file.'], 400);
    }
}
