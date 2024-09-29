@extends('layouts.admin')

@section('content')

    <div class="bg-white p-6 rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl">

        <!-- Success Message Popup -->
        @if (session('success'))
            <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 1000)" x-show="open"
                class="fixed inset-0 flex items-center justify-center z-50">
                <div class="bg-black bg-opacity-50 absolute inset-0 backdrop-blur-sm"></div>
                <div class="bg-green-500 text-white px-6 py-4 rounded-md shadow-lg z-10">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-6 text-center sm:text-left" style="color: var(--primary-color);">
            Add New User
        </h2>

        @if ($errors->any())
            <div x-data="{ open: true }" class="relative z-50">
                <div x-show="open"
                    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity ease-out duration-300">
                </div>
                <div x-show="open" x-transition:enter="transition ease-out duration-300"
                    x-transition:leave="transition ease-in duration-300"
                    class="fixed inset-0 flex items-center justify-center">
                    <div
                        class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-auto transition-transform duration-300 hover:scale-105">
                        <div class="flex justify-center items-center border-b pb-2 mb-4">
                            <h2 class="text-lg font-semibold text-red-600">Input Error</h2>
                        </div>
                        <ul class="text-red-700 text-center">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <div class="mt-6 text-center">
                            <button @click="open = false"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-transform duration-300 hover:scale-105">Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.add-user') }}" method="POST" class="mb-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" placeholder="e.g. Juan DELA CRUZ"
                        class="mt-2 w-full h-10 pl-3 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 hover:border-indigo-500">
                    <p class="mt-2 text-xs text-gray-500 text-opacity-35">
                        Please make sure the name matches in USeP Email.
                    </p>
                </div>
                <div class="relative">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" placeholder="juandelacruz12345@usep.edu.ph"
                        class="mt-2 w-full h-10 pl-3 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 hover:border-indigo-500"
                        pattern="[a-zA-Z0-9._%+-]+@usep\.edu\.ph$" title="Must be a @usep.edu.ph email">
                    <p class="mt-2 text-xs text-gray-500 text-opacity-35">
                        Only USeP emails are accepted.
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-end mt-8 space-y-4 sm:space-y-0 sm:space-x-4">
                <x-button type="reset" color="red">Clear Fields</x-button>
                <x-button type="submit" color="green">Add User</x-button>
            </div>
        </form>

        <h2 class="text-2xl font-bold mb-6 text-center sm:text-left" style="color: var(--primary-color);">
            List of Users
        </h2>
        <form action="{{ route('admin.user-management') }}" method="GET" class="mb-6">
            <div class="flex items-center w-full border border-transparent rounded-lg">
                <input type="text" name="search" id="search" placeholder="Search by name"
                    class="w-full shadow-md h-10 pl-3 pr-3 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 hover:border-indigo-500"
                    onkeyup="filterTable()">
            </div>
        </form>

        <div class="overflow-x-auto border border-b">
            <!-- Added a wrapper div for scrollable functionality -->
            <div class="max-h-64 overflow-y-auto">
                <table id="userTable"
                    class="min-w-full bg-white border rounded-md overflow-hidden shadow-sm transition-shadow duration-300 hover:shadow-md">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-100 transition-colors duration-300">
                                <td class="py-2 px-4 text-xs text-gray-700">{{ $user->name }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700">{{ $user->email }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700">{{ $user->role }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700 text-center">
                                    <div x-data="{ open: false }" class="relative inline-flex items-center">
                                        <button @click="open = !open"
                                            class="inline-flex items-center justify-center p-1 transition-transform duration-300 hover:scale-105">
                                            <div class="rounded-full bg-[#9d1e18] p-2 hover:bg-yellow-500" title="Change Role">
                                                <img src="/svg/switch user.svg" alt="Change Role" class="h-5 w-5"
                                                    style="filter: brightness(0) invert(1);">
                                            </div>
                                        </button>

                                        <div x-show="open" @click.outside="open = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="origin-top-right absolute right-full mr-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="width: 6rem;">
                                            <form action="{{ route('admin.change-role', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="py-1">
                                                    <button name="role" value="admin"
                                                        class="text-gray-700 block py-2 px-4 text-sm w-full text-center hover:bg-gray-100">Admin</button>
                                                    <button name="role" value="subadmin"
                                                        class="text-gray-700 block py-2 px-4 text-sm w-full text-center hover:bg-gray-100">Subadmin</button>
                                                </div>
                                            </form>
                                        </div>

                                        <form action="{{ route('admin.remove-access', $user->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center p-1 transition-transform duration-300 hover:scale-105">
                                                <div class="rounded-full bg-[#4b5563] p-2 hover:bg-[#6b7280]" title="Remove Access">
                                                    <img src="/svg/remove access.svg" alt="Remove Access" class="h-5 w-5"
                                                        style="filter: brightness(0) invert(1);">
                                                </div>
                                            </button>
                                        </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Import Alpine.js and your scripts --}}
    @vite(['resources/js/app.js'])

@endsection
