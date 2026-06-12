@extends('layouts.admin')

@section('title', 'Create Tag')
@section('header', 'Create Tag')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.tags.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Add New Tag</h2>
    </div>

    <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-6">
        <form action="{{ route('admin.tags.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700">Tag Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Tutorial"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-650">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-750 shadow-sm">
                    Create Tag
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
