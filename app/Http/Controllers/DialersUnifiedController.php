<?php

namespace App\Http\Controllers;

use App\Models\DialersUnified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DialersUnifiedController extends Controller
{
    public function index()
    {
        $dialers = DialersUnified::orderBy('created_at', 'desc')->get();
        return view('dialers-unified.index', compact('dialers'));
    }

    public function create()
    {
        return view('dialers-unified.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dialer_ip' => 'nullable|string|max:150',
            'dialer_weblink' => 'nullable|string|max:150',
            'dialer_access' => 'nullable|string|max:300',
            'dialer_no' => 'nullable|string|max:40',
            'dialer_team' => 'nullable|string|max:40',
            'dialer_name' => 'nullable|string|max:255',
            'server_no' => 'nullable|string|max:255',
            'server_ip' => 'nullable|string|max:255',
            'folder_name' => 'nullable|string|max:255',
            'server_status' => 'nullable|string|max:1|in:0,1',
            'recording_link' => 'nullable|string',
        ]);

        $validated['server_status'] = $request->has('server_status') && $request->server_status ? '1' : '0';

        DialersUnified::create($validated);

        Log::info('DialersUnifiedController@store - Record created', ['dialer_no' => $validated['dialer_no'] ?? null]);

        return redirect()->route('dialers-unified.index')->with('success', 'Record created successfully.');
    }

    public function edit($id)
    {
        $dialer = DialersUnified::findOrFail($id);
        return view('dialers-unified.edit', compact('dialer'));
    }

    public function update(Request $request, $id)
    {
        $dialer = DialersUnified::findOrFail($id);

        $validated = $request->validate([
            'dialer_ip' => 'nullable|string|max:150',
            'dialer_weblink' => 'nullable|string|max:150',
            'dialer_access' => 'nullable|string|max:300',
            'dialer_no' => 'nullable|string|max:40',
            'dialer_team' => 'nullable|string|max:40',
            'dialer_name' => 'nullable|string|max:255',
            'server_no' => 'nullable|string|max:255',
            'server_ip' => 'nullable|string|max:255',
            'folder_name' => 'nullable|string|max:255',
            'server_status' => 'nullable|string|max:1|in:0,1',
            'recording_link' => 'nullable|string',
        ]);

        if ($request->has('server_status')) {
            $validated['server_status'] = $request->server_status ? '1' : '0';
        }

        $dialer->update($validated);

        Log::info('DialersUnifiedController@update - Record updated', ['id' => $id]);

        return redirect()->route('dialers-unified.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        $dialer = DialersUnified::findOrFail($id);
        $dialer->delete();

        Log::info('DialersUnifiedController@destroy - Record deleted', ['id' => $id]);

        return redirect()->route('dialers-unified.index')->with('success', 'Record deleted successfully.');
    }
}
