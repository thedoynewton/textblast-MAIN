<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $messageTemplates = MessageTemplate::all();
        return view('admin.message_templates.index', compact('messageTemplates'));
    }

    public function create()
    {
        return view('admin.message_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        MessageTemplate::create([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
        ]);
        return redirect()->route('admin.app-management', ['tab' => 'messageTemplates'])
        ->with('success', 'Message Template created successfully.');
    }

    public function edit($id)
    {
        $template = MessageTemplate::findOrFail($id);
        return view('admin.message_templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template = MessageTemplate::findOrFail($id);
        $template->update([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.app-management', ['tab' => 'messageTemplates'])
        ->with('success', 'Template updated successfully.');
    }
    
    public function destroy($id)
    {
        $template = MessageTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.app-management', ['tab' => 'messageTemplates'])
        ->with('success', 'Message Template deleted successfully.');

    }
}
