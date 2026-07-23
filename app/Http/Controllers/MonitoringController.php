<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitoring;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class MonitoringController extends Controller
{
  

    public function create()
    {
        $excludedTypes = ['Avatar',  'Accountant', 'Dialer Support', 'Agant',  'IT', 'QA', 'Client'];

        $employees = User::where('created_by', \Auth::user()->creatorId())
        ->whereNotIn('type', $excludedTypes)
        ->orderBy('name', 'asc') // Sort alphabetically by 'name' column
        ->get();
    
    return view('monitoring.create', compact('employees'));
    
    }


    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'monitor_from' => 'required|date_format:H:i',
            'monitor_to' => 'required|date_format:H:i|after:monitor_from',
            'monitor_date' => 'required|date',
            'call_rapport_building' => 'nullable|string',
            'qualifying_part' => 'nullable|string',
            'agents_efforts' => 'nullable|string',
            'rebuttals' => 'nullable|string',
            'overall_call_details' => 'nullable|string',
            'vocabulary' => 'nullable|string',
            'customer_response' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'score' => 'required|in:Good,Avg,Bad,Worst',
            'notify_to' => 'required|array',
            'notify_to.*' => 'exists:users,id',
            // New Dropdown Inputs
            'greeting' => 'nullable|string',
    'energy' => 'nullable|string',
    'qa' => 'nullable|string',
            'focus' => 'required|in:Excellent,Satisfied,Not Satisfied',
            'positivity' => 'required|in:Excellent,Satisfied,Not Satisfied',
            'confidence' => 'required|in:Excellent,Satisfied,Not Satisfied',
            'motivation' => 'required|in:Excellent,Satisfied,Not Satisfied',
            'energy_level' => 'required|in:Excellent,Satisfied,Not Satisfied',
            'smile' => 'required|in:Excellent,Satisfied,Not Satisfied',
        ]);
    
        // Save the data
        Monitoring::create([
            'employee_id' => $request->employee_id,
            'monitor_from' => $request->monitor_from,
            'monitor_to' => $request->monitor_to,
            'monitor_date' => $request->monitor_date,
            'call_rapport_building' => $request->call_rapport_building,
            'qualifying_part' => $request->qualifying_part,
            'agents_efforts' => $request->agents_efforts,
            'rebuttals' => $request->rebuttals,
            'overall_call_details' => $request->overall_call_details,
            'vocabulary' => $request->vocabulary,
            'customer_response' => $request->customer_response,
            'suggestions' => $request->suggestions,
            'score' => $request->score,
            'notify' => json_encode($request->notify_to), // Convert array to JSON
            // New Dropdown Fields
            'greeting' => $request->greeting,
    'energy' => $request->energy,
    'qa' => $request->qa,
            'focus' => $request->focus,
            'positivity' => $request->positivity,
            'confidence' => $request->confidence,
            'motivation' => $request->motivation,
            'energy_level' => $request->energy_level,
            'smile' => $request->smile,
            'filled_by' => auth()->id(), // Save the authenticated user's ID

        ]);
    
        return redirect()->route('monitoring.index')->with('success', 'Monitoring data submitted successfully!');
    }
    
    
    

    public function index()
{
    // Fetch all monitoring data with employee details (eager loading)
    $monitorings = Monitoring::with('employee','filledBy')->orderBy('created_at', 'desc')->paginate(10);
    return view('monitoring.index', compact('monitorings'));
}


public function destroy($id)
{
    $monitoring = Monitoring::findOrFail($id);
    $monitoring->delete();
    return redirect()->route('monitoring.index')->with('success', 'Monitoring data deleted successfully!');
}


public function myMonitoring()
{
    $userId = auth()->id(); // Get the current user's ID

    // Fetch records where the employee_id matches the current user's ID
    $monitorings = Monitoring::where('employee_id', $userId)->paginate(10);

    return view('monitoring.my-monitoring', compact('monitorings'));
}


public function show($id)
{
    $monitoring = Monitoring::findOrFail($id);

    // Get the current user's monitoring records, sorted by ID
    $userMonitoringRecords = Monitoring::where('employee_id', $monitoring->employee_id)
        ->orderBy('id', 'asc')
        ->pluck('id')
        ->toArray();

    // Find the current record's position in the array
    $currentIndex = array_search($monitoring->id, $userMonitoringRecords);

    // Get the previous and next record IDs
    $previous = $currentIndex > 0 ? Monitoring::find($userMonitoringRecords[$currentIndex - 1]) : null;
    $next = $currentIndex < count($userMonitoringRecords) - 1 ? Monitoring::find($userMonitoringRecords[$currentIndex + 1]) : null;

    return view('monitoring.show', compact('monitoring', 'previous', 'next'));
}


public function edit($id)
{
    $monitoring = Monitoring::findOrFail($id);
    $employees = User::all();
    return view('monitoring.edit', compact('monitoring', 'employees'));
}


public function update(Request $request, $id)
{
    $monitoring = Monitoring::findOrFail($id);

    $validatedData = $request->validate([
        'employee_id' => 'required|exists:users,id',
        'monitor_from' => 'required',
        'monitor_to' => 'required',
        'monitor_date' => 'required|date',
        'call_rapport_building' => 'nullable|string',
        'qualifying_part' => 'nullable|string',
        'agents_efforts' => 'nullable|string',
        'rebuttals' => 'nullable|string',
        'overall_call_details' => 'nullable|string',
        'vocabulary' => 'nullable|string',
        'customer_response' => 'nullable|string',
        'suggestions' => 'nullable|string',
        'score' => 'required|in:Good,Avg,Bad,Worst',
        
        
    ]);

    $monitoring->update($validatedData);

    return redirect()->route('monitoring.index')->with('success', 'Monitoring record updated successfully.');
}


}

