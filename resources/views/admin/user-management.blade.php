@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold mb-6">User Management</h1>

    <h2 class="text-2xl font-bold mb-4">Add New User</h2>
    {{-- @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.add-user') }}" method="POST" class="mb-6">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-300">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-300" pattern="[a-zA-Z0-9._%+-]+@usep\.edu\.ph$" title="Must be a @usep.edu.ph email">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">Add User</button>
    </form>

    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Name</th>
                <th class="py-2 px-4 border-b">Email</th>
                <th class="py-2 px-4 border-b">Role</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->role }}</td>
                    <td class="py-2 px-4 border-b">
                        <form action="{{ route('admin.change-role', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <select name="role" class="border rounded p-1">
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="subadmin" {{ $user->role == 'subadmin' ? 'selected' : '' }}>Subadmin</option>
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Change Role</button>
                        </form>
                        <form action="{{ route('admin.remove-access', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Remove Access</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
