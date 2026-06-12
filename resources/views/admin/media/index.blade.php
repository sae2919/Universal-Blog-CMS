@extends('layouts.admin')

@section('title', 'Media Library')
@section('header', 'Media Library')

@section('content')
<div class="space-y-6">
    {{-- Top Controls & Upload --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Search Form --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6 flex flex-col justify-between">
            <h3 class="font-bold text-gray-800 dark:text-slate-200 text-sm mb-3">Search Files</h3>
            <form action="{{ route('admin.media.index') }}" method="GET" class="space-y-3">
                <input type="text" name="search" placeholder="Search by file name..." value="{{ $search }}"
                       class="w-full px-4 py-2 text-sm border border-gray-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-300">
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-colors">
                        Filter Files
                    </button>
                    @if($search)
                        <a href="{{ route('admin.media.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Upload Form --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-150 dark:border-slate-700 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 dark:text-slate-200 text-sm mb-3">Upload New File</h3>
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-4 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-xl p-4 hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                @csrf
                <div class="flex-1">
                    <input type="file" name="file" required
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-[10px] text-gray-400 mt-2">Supported: Images, PDF, Zip, Excel, Word, Text, MP4 (Max: 10MB)</p>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-colors">
                    Upload
                </button>
            </form>
            @error('file')
                <p class="text-xs text-red-650 mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Media Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
        @forelse($mediaList as $media)
            @php
                $isImage = str_starts_with($media->mime_type, 'image/');
                // Format file size
                $size = $media->file_size;
                $units = ['B', 'KB', 'MB', 'GB'];
                for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
                    $size /= 1024;
                }
                $formattedSize = round($size, 1) . ' ' . $units[$i];
            @endphp
            <div class="bg-white dark:bg-slate-800 border border-gray-150 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm flex flex-col group relative">
                
                {{-- Preview / Icon --}}
                <div class="h-32 bg-gray-50 dark:bg-slate-900 border-b border-gray-100 dark:border-slate-700 flex items-center justify-center overflow-hidden">
                    @if($isImage)
                        <img src="{{ $media->url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $media->file_name }}">
                    @else
                        {{-- Document Icon based on MIME --}}
                        <div class="text-4xl text-gray-400 select-none">
                            @if(str_contains($media->mime_type, 'pdf'))
                                📕
                            @elseif(str_contains($media->mime_type, 'zip') || str_contains($media->mime_type, 'compressed'))
                                📦
                            @elseif(str_contains($media->mime_type, 'video') || str_contains($media->mime_type, 'mp4'))
                                🎬
                            @else
                                📄
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Card Info --}}
                <div class="p-3 flex-1 flex flex-col justify-between min-w-0">
                    <div class="min-w-0">
                        <span class="font-semibold text-gray-800 dark:text-slate-200 text-xs block truncate" title="{{ $media->file_name }}">
                            {{ $media->file_name }}
                        </span>
                        <div class="flex items-center justify-between mt-1 text-[10px] text-gray-400">
                            <span>{{ $formattedSize }}</span>
                            <span class="uppercase font-semibold text-[8px] px-1 bg-gray-100 dark:bg-slate-700 text-gray-500 rounded">{{ pathinfo($media->file_name, PATHINFO_EXTENSION) }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-700 pt-2.5 mt-2.5 gap-2">
                        <button type="button" onclick="copyUrl('{{ $media->url }}', this)"
                                class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex-1 text-left">
                            Copy URL
                        </button>
                        <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this file?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-650 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500">
                No files uploaded to the media library.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($mediaList->hasPages())
        <div class="pt-4">
            {{ $mediaList->links() }}
        </div>
    @endif
</div>

<script>
    function copyUrl(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.remove('text-indigo-600');
            button.classList.add('text-green-600');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('text-green-600');
                button.classList.add('text-indigo-600');
            }, 1500);
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection
