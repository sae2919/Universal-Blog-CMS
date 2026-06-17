@extends('layouts.admin')

@section('title', 'Manage Users')
@section('header', 'Manage Users')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">All System Users</h2>
            <p class="text-sm text-gray-500 mt-1">Manage admins, editors, authors, and system permissions.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all">
            + Add New User
        </a>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-row gap-4 items-center justify-between w-full">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                       class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full">
            </div>

            <div class="flex items-center gap-3 justify-end flex-shrink-0">
                @if(request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold whitespace-nowrap mr-2">
                        Clear Filters
                    </a>
                @endif

                <select name="role" class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="editor" {{ request('role') === 'editor' ? 'selected' : '' }}>Editor</option>
                    <option value="author" {{ request('role') === 'author' ? 'selected' : '' }}>Author</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-sm rounded-lg transition-colors whitespace-nowrap">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table Panel --}}
    <div class="bg-white rounded-xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-55 border-b border-gray-150 text-xs font-bold text-gray-550 uppercase tracking-wider">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Posts Count</th>
                        <th class="px-6 py-4">Last Login</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-10 h-10 object-cover rounded-full border border-gray-200" alt="">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold text-gray-900 block leading-tight">{{ $user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase
                                    @if($user->role === 'super_admin') bg-purple-50 text-purple-700 border border-purple-100
                                    @elseif($user->role === 'admin') bg-blue-50 text-blue-700 border border-blue-100
                                    @elseif($user->role === 'editor') bg-yellow-50 text-yellow-750 border border-yellow-100
                                    @else bg-gray-100 text-gray-700 border border-gray-200
                                    @endif">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-150">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-750 border border-red-150">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-650">
                                <span class="font-semibold text-gray-900">{{ $user->posts_count }}</span> posts
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d Y, H:i') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                No users found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-150 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
