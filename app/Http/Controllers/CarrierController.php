<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\User;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index(Request $request)
{
    // Get all unique states by decoding the JSON arrays in the state column
    $states = Carrier::pluck('state')
        ->flatMap(function ($state) {
            return json_decode($state, true); // Decode JSON to an array
        })
        ->unique()
        ->sort()
        ->values(); // Return only unique and sorted values

    // Initialize query to get all carriers
    $carriers = Carrier::all(); // Fetch all records initially

    // Filter carriers by selected state
    if ($request->filled('state')) {
        $selectedState = $request->state;

        // Filter using PHP after retrieving all records
        $carriers = $carriers->filter(function ($carrier) use ($selectedState) {
            $statesArray = json_decode($carrier->state, true); // Decode the JSON field
            return in_array($selectedState, $statesArray); // Check if the selected state is in the decoded array
        });
    }

    return view('carrier-search.index', compact('carriers', 'states'));
}


    

    // Display the form
    public function create()
    {
        // Fetch all users of type 'client' for the licensed agent dropdown
        $licensedAgents = User::where('type', 'client')->get();
        return view('carrier-search.create', compact('licensedAgents'));
    }

    // Store the form data
    public function store(Request $request)
    {
        // Update validation rules to expect arrays for multiple selections
        $validated = $request->validate([
            'licensed_agency' => 'required|array',
            'state' => 'required|array',
            'licensed_agent_name' => 'required|string',
            'carriers' => 'required|array', // Validate as an array

            'other_agency' => 'nullable|string', // Add this line to validate 'other_agency'
        ]);

        // Create and store the carrier record
        Carrier::create([
            'licensed_agency' => json_encode($request->licensed_agency), // Convert to JSON
            'state' => json_encode($request->state), // Convert to JSON
            'licensed_agent_name' => $request->licensed_agent_name,
            'carriers' => json_encode($request->carriers), // Convert carriers to JSON

        ]);

        return redirect()->back()->with('success', 'Carrier information stored successfully.');
    }
    
}
