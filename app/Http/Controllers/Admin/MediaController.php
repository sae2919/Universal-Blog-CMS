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
            $extension = strtolower($file->getClientOriginalExtension());
            
            $convertible = in_array($extension, ['jpeg', 'jpg', 'png', 'webp']);
            
            if ($convertible) {
                $extension = 'webp';
                $cleanName = \Str::slug($fileName) . '-' . time() . '.' . $extension;
                
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file)->toWebp(90);
                
                \Storage::disk('public')->put('uploads/' . $cleanName, $image);
                $filePath = 'uploads/' . $cleanName;
                
                $mimeType = 'image/webp';
                $fileSize = strlen($image);
                $originalName = pathinfo($originalName, PATHINFO_FILENAME) . '.webp';
            } else {
                $cleanName = \Str::slug($fileName) . '-' . time() . '.' . $extension;
                $filePath = $file->storeAs('uploads', $cleanName, 'public');
                $mimeType = $file->getClientMimeType();
                $fileSize = $file->getSize();
            }

            // Save record
            $media = Media::create([
                'file_name'   => $originalName,
                'file_path'   => $filePath,
                'mime_type'   => $mimeType,
                'file_size'   => $fileSize,
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
