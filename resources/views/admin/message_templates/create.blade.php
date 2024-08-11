@extends('layouts.admin')

@section('title', 'Create Message Template')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Create New Message Template</h2>
            <form action="{{ route('message_templates.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                    <input type="text" name="name" id="name" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Message Content</label>
                    <textarea name="content" id="content" rows="4" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" required></textarea>
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out">Save Template</button>
                </div>
            </form>
        </div>
    </div>
@endsection
