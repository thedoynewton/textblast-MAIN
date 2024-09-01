@extends('layouts.admin')

@section('title', 'Edit Message Template')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-lg">
    <form action="{{ route('message_templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
            <input type="text" name="name" id="name" value="{{ $template->name }}" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700">Message Content</label>
            <textarea name="content" id="content" rows="4" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" required>{{ $template->content }}</textarea>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out">Update Template</button>
        </div>
    </form>
</div>
@endsection