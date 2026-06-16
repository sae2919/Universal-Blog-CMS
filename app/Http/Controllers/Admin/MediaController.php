<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $mediaList = Media::with('uploader')
            ->when($search, function ($query) use ($search) {
                $query->where('file_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(18);

        return view('admin.media.index', compact('mediaList', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,png,jpg,gif,svg,webp,pdf,zip,doc,docx,xls,xlsx,txt,mp4',
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
            Media::create([
                'file_name'   => $originalName,
                'file_path'   => $filePath,
                'mime_type'   => $file->getClientMimeType(),
                'file_size'   => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            return redirect()->route('admin.media.index')->with('success', 'File uploaded successfully!');
        }

        return redirect()->route('admin.media.index')->with('error', 'Failed to upload file.');
    }

    public function destroy(Media $media)
    {
        // Delete physical file from storage/app/public/uploads
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        // Delete from database
        $media->delete();

        return redirect()->route('admin.media.index')->with('success', 'File deleted successfully!');
    }

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
            ]);
        }

        return response()->json(['error' => 'Failed to upload file.'], 400);
    }
}
