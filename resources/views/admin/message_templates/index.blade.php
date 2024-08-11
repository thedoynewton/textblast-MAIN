@extends('layouts.admin')

@section('title', 'Message Templates')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="mb-4">
                <a href="{{ route('message_templates.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out">Add New Template</a>
            </div>

            <div class="overflow-x-auto overflow-y-auto max-h-96 mb-8">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Template Name</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Message Content</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messageTemplates as $template)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="py-3 px-4 border-b text-gray-600">{{ $template->name }}</td>
                                <td class="py-3 px-4 border-b text-gray-600">{{ $template->content }}</td>
                                <td class="py-3 px-4 border-b text-gray-600">
                                    <a href="{{ route('message_templates.edit', $template->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ route('message_templates.destroy', $template->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if($messageTemplates->isEmpty())
                            <p class="text-center text-gray-500 mt-4">No message templates found.</p>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
