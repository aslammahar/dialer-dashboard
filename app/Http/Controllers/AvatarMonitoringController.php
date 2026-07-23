<?php

namespace App\Http\Controllers;

use App\Models\AvatarMonitoring;
use App\Models\User;
use Illuminate\Http\Request;

class AvatarMonitoringController extends Controller
{
    public function index()
    {
        $monitorings = AvatarMonitoring::with(['employee', 'filledBy'])->paginate(10);
        return view('avatar_monitoring.index', compact('monitorings'));
    }
    
    public function show($id)
    {
        $avatarMonitoring = AvatarMonitoring::find($id);
    
        if (!$avatarMonitoring) {
            return redirect()->route('avatar_monitoring.index')->withErrors('Record not found.');
        }
    
        $previous = AvatarMonitoring::where('id', '<', $id)->orderBy('id', 'desc')->first();
        $next = AvatarMonitoring::where('id', '>', $id)->orderBy('id', 'asc')->first();
    
        return view('avatar_monitoring.show', compact('avatarMonitoring', 'previous', 'next'));
    }
    
    public function myAvatarNotifications()
    {
        $userId = auth()->id(); // Get the currently logged-in user ID

        // Fetch Avatar Monitoring records where the user is in the 'notify_to' array
        $notifications = AvatarMonitoring::where('employee_id', $userId)->paginate(10);

        return view('avatar_monitoring.my_notification', compact('notifications'));
    }
    

    public function create()
    {
        $excludedTypes = [ 'Accountant', 'Dialer Support', 'Agant',  'IT', 'QA', 'Client'];

        $employees = User::where('created_by', \Auth::user()->creatorId())
        ->whereNotIn('type', $excludedTypes)
        ->orderBy('name', 'asc') // Sort alphabetically by 'name' column
        ->get();
            return view('avatar_monitoring.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'monitor_date' => 'required|date',
            'monitor_from' => 'nullable|date_format:H:i',
            'monitor_to' => 'nullable|date_format:H:i',
            'greeting' => 'nullable|string',
            'response_on_answering_machine' => 'nullable|string',
            'response_time' => 'nullable|string',
            'customer_response' => 'nullable|string',
            'leave_3_way' => 'nullable|string',
            'questions' => 'nullable|string',
            'dispositions' => 'nullable|string',
            'comments_suggestions' => 'nullable|string',
            'disposition_records' => 'nullable|array',
            'score' => 'nullable|in:Good,Avg,Bad,Worst',
            'notify_to' => 'nullable|array',
        ]);

        $validated['filled_by'] = auth()->id();

        AvatarMonitoring::create($validated);

        return redirect()->route('avatar_monitoring.index')->with('success', 'Record added successfully.');
    }

    public function edit($id)
{
    $avatarMonitoring = AvatarMonitoring::findOrFail($id); // Fetch the record
    $employees = User::where('type', 'employee')->get(); // Fetch employees for dropdown
    $filledBy = auth()->user(); // Current user who filled the form

    return view('avatar_monitoring.edit', compact('avatarMonitoring', 'employees', 'filledBy'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'employee_id' => 'required|exists:users,id',
        'monitor_date' => 'required|date',
        'monitor_from' => 'required',
        'monitor_to' => 'required',
        'greeting' => 'nullable|string',
        'response_on_answering_machine' => 'nullable|string',
        'response_time' => 'nullable|string',
        'customer_response' => 'nullable|string',
        'leave_3_way' => 'nullable|string',
        'questions' => 'nullable|string',
        'dispositions' => 'nullable|string',
        'comments' => 'nullable|string',
    ]);

    $avatarMonitoring = AvatarMonitoring::findOrFail($id);
    $avatarMonitoring->update($validated);
    $avatarMonitoring->filled_by = auth()->id(); // Save who edited the record
    $avatarMonitoring->save();

    return redirect()->route('avatar_monitoring.index')
                     ->with('success', 'Avatar Monitoring record updated successfully.');
}

public function export($type)
{
    // Export logic for PDF or PNG
    if ($type === 'pdf') {
        // Export records to PDF
    } elseif ($type === 'png') {
        // Export records to PNG
    }

    return redirect()->route('avatar_monitoring.index')->with('success', 'Records exported successfully.');
}



public function destroy(AvatarMonitoring $avatarMonitoring)
{
    try {
        $avatarMonitoring->delete();

        return redirect()->route('avatar_monitoring.index')
            ->with('success', 'Record deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->route('avatar_monitoring.index')
            ->with('error', 'Failed to delete record: ' . $e->getMessage());
    }
}

}

