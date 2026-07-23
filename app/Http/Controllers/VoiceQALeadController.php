<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VoiceQALead;

class VoiceQALeadController extends Controller
{
    public function createForm()
    {
        $voiceUsers = User::where('type', 'voice')->get();
        return view('voiceqa.create', compact('voiceUsers'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'agent_id' => 'required|exists:users,id',
            'lead_id' => 'required|integer',
            'dialer_id' => 'required|integer',
            'recording' => 'required|string',
            'GREETINGS' => 'required|string|in:Yes,No',
            'PITCH_Call_About' => 'required|string|in:Yes,No',
            'AGE' => 'required|string|in:Yes,No',
            'Smoker' => 'required|string|in:Yes,No',
            'Health1' => 'required|string|in:Yes,No',
            'Beneficiary' => 'required|string|in:Yes,No',
            'Account' => 'required|string|in:Yes,No',
            'Plan' => 'required|string|in:Yes,No',
            'Transfer_details' => 'required|string|in:Yes,No',
            'Xfer_Consent' => 'nullable|string|in:Yes,No',
            'Rebuttals' => 'required|string|in:Yes,No',
            'COMMENTS' => 'nullable|string',
            'Status' => 'required|string|in:Approved,Rejected',
            'QA_Person' => 'required|string',
            'Use_of_Rebuttals' => 'required|integer',
            'No_of_Refusals' => 'required|integer',
            'count' => 'required|integer',
        ]);

        // Create a new record in the database using the VoiceQALead model
        VoiceQALead::create($validatedData);

        // Redirect the user to a success page or any other appropriate action
        return redirect()->route('voiceqa.create')->with('success', 'Record created successfully!');
    }



    

    public function showUserLeads()
    {
        $userId = Auth::id();
        $userLeads = VoiceQALead::where('agent_id', $userId)->get();

        return view('voiceqa.checked-leads', ['userLeads' => $userLeads]);
    }


        public function __construct()
    {
        $this->middleware('auth');
    }

}