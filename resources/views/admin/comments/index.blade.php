@extends('layouts.admin')

@section('title', 'Manage Comments')
@section('header', 'Manage Comments')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Comments Moderation</h2>
        <p class="text-sm text-gray-500 mt-1">Approve, reject, or delete visitor comments across all articles.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 border-b border-gray-150 text-xs font-bold text-gray-550 uppercase tracking-wider">
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4">Comment</th>
                        <th class="px-6 py-4">Article</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($comments as $comment)
                        <tr class="hover:bg-gray-55/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 leading-tight">{{ $comment->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $comment->email }}</div>
                                <div class="text-[10px] text-gray-500 font-mono mt-1">IP: {{ $comment->ip_address }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-xs md:max-w-md">
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $comment->comment }}</p>
                                <span class="text-[10px] text-gray-400 block mt-2">{{ $comment->created_at->format('M d, Y \a\t H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('blog.show', [$comment->post->category->slug, $comment->post->slug]) }}" target="_blank" class="text-indigo-650 hover:underline font-semibold text-xs leading-snug block truncate max-w-[180px]">
                                    {{ $comment->post->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase
                                    @if($comment->status === 'approved') bg-green-50 text-green-700 border border-green-100
                                    @elseif($comment->status === 'pending') bg-yellow-50 text-yellow-750 border border-yellow-100
                                    @else bg-red-50 text-red-700 border border-red-100
                                    @endif">
                                    {{ $comment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($comment->status !== 'approved')
                                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 rounded text-xs font-semibold">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    @if($comment->status !== 'rejected')
                                        <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border border-yellow-200 rounded text-xs font-semibold">
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded text-xs font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No comments posted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 bg-gray-50">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
