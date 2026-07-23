<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recruitment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <-- Make sure this line is here
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecruitmentReportExport;
use App\Models\User;
use App\Exports\AllRecruitmentsExport;


class RecruitmentController extends Controller
{
    /**
     * Show the recruitment form.
     *
     * @return \Illuminate\View\View
     */


    

     public function deleteAll()
     {
         try {
             Recruitment::truncate(); // Deletes all records & resets auto-increment ID
             return redirect()->route('recruitments.reports')->with('success', 'All records deleted successfully.');
         } catch (\Exception $e) {
             return redirect()->route('recruitments.reports')->with('error', 'Failed to delete records.');
         }
     }

public function exportAll()
{
    return Excel::download(new AllRecruitmentsExport, 'all_recruitments.xlsx');
}
    public function create()
    {    

        $excludedTypes = ['Avatar', 'Closer', 'Accountant', 'Dialer Support', 'Agant', 'Voice', 'IT', 'QA', 'Client', 'Director'];

$employees = User::where('created_by', \Auth::user()->creatorId())
    ->whereNotIn('type', $excludedTypes)
    ->pluck('name', 'id');


    $excludedinter = ['Avatar', 'Closer', 'Accountant', 'Dialer Support', 'Agant', 'Voice', 'IT', 'QA', 'Client','Team Lead'];

    $interviewer = User::where('created_by', \Auth::user()->creatorId())
    ->whereIn('type', ['HR', 'recruiter', 'recruiter head'])
    ->pluck('name', 'id');
        
        // Pass dropdown options to the view if needed
        $sources = ['Instagram', 'Facebook', 'Tiktok', 'LinkedIn', 'Indeed', 'WhatsApp', 'Facebook Leads'];
        $workFromOptions = ['Home', 'Office J.sons', 'Office Sellers'];
        $designations = [
            'Avatar-CSR', 'Voice-CSR', 'WFH Voice', 'Junior Closer', 'WFH JC', 'Closer', 'WFH Closer',
            'Dialer & Data Manager', 'Trainer', 'QA', 'HR', 'Recruiter', 'SEO', 'Digital Marketer', 'IT',
            'Web Developer', 'Ads Manager', 'Social Media Expert', 'Accountant', 'Manager', 'Content Writer', 'Graphic Designer'
        ];
        $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];
        $interviewerRemarks = [ 'Excellent', 'Good', 'Average', 'Poor'];

        return view('recruitement.create', compact('sources', 'workFromOptions', 'designations', 'statuses', 'interviewerRemarks','employees','interviewer'));
    }

    public function sendOfferLetter($id)
    {
        try {
            $recruitment = Recruitment::findOrFail($id);
            $recruitment->offer_letter_sent = true; // Add this column in your database
            $recruitment->save();
    
            return redirect()->route('recruitments.index')->with('success', 'Offer Letter sent successfully.');
        } catch (\Exception $e) {
            return redirect()->route('recruitments.index')->with('error', 'An error occurred while sending the offer letter.');
        }
    }
    
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10); // Default to 10 records per page
        $recruitments = Recruitment::orderBy('date', 'desc')->paginate($perPage);
    
        return view('recruitement.index', compact('recruitments'));
    }
    

public function filter(Request $request)
{
    $query = Recruitment::query();

    // Filter by Status
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    // Filter by Name
    if ($request->has('name') && $request->name != '') {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    // Filter by Form No (exact match or partial match)
    if ($request->has('formNo') && $request->formNo != '') {
        $query->where('formid', 'like', '%' . $request->formNo . '%');
    }

    // Retrieve results
    $recruitments = $query->get();

    return response()->json($recruitments);
}


    /**
     * Store recruitment data in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */


     public function reports(Request $request)
     {
         // Default filters
         $filterType = $request->query('filter', 'custom'); 
         $selectedRecruiter = $request->query('recruiter', ''); 
     
         // Set start date based on filter type
         if ($filterType === 'weekly') {
             $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
         } elseif ($filterType === 'monthly') {
             $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
         } else {
             $startDate = $request->query('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
         }
     
         $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
     
         // Define possible statuses
         $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];
     
         // Get all unique recruiters for dropdown
         $allRecruiters = Recruitment::select('interview_taken_by')->distinct()->pluck('interview_taken_by');
     
         // Query recruiters based on selection
         $recruiterQuery = Recruitment::query();
     
         if (!empty($selectedRecruiter)) {
             $recruiterQuery->where('interview_taken_by', $selectedRecruiter);
         }
     
         // Get recruiters
         $recruiters = $recruiterQuery->select('interview_taken_by')->distinct()->pluck('interview_taken_by');
     
         // Prepare report data
         $reportData = [];
     
         foreach ($recruiters as $recruiter) {
             $totalCalls = Recruitment::where('interview_taken_by', $recruiter)
                 ->whereDate('created_at', '>=', $startDate)
                 ->whereDate('created_at', '<=', $endDate)
                 ->count();
     
             $statusCounts = Recruitment::where('interview_taken_by', $recruiter)
                 ->whereDate('created_at', '>=', $startDate)
                 ->whereDate('created_at', '<=', $endDate)
                 ->selectRaw('status, COUNT(*) as count')
                 ->groupBy('status')
                 ->pluck('count', 'status')
                 ->toArray();
     
             foreach ($statuses as $status) {
                 $statusCounts[$status] = $statusCounts[$status] ?? 0;
             }
     
             $reportData[] = [
                 'recruiter' => $recruiter,
                 'total_calls' => $totalCalls,
                 'statuses' => $statusCounts,
             ];
         }
     
         return view('recruitement.reports', compact('reportData', 'statuses', 'startDate', 'endDate', 'allRecruiters', 'selectedRecruiter'));
     }
     
     // ✅ Function to Export to Excel
     


     public function exportExcel(Request $request)
{
    return Excel::download(
        new RecruitmentReportExport($request->all()), 
        'recruitment_report_' . date('Y-m-d') . '.xlsx'
    );
}
     
     public function store(Request $request)
     {
         try {
             // Validate the request data
             $request->validate([
                 'name' => 'required|string|max:255',
                 'contact_no' => 'required|string|max:20',
                 'email' => 'nullable|email|unique:recruitments,email', // Make email optional
                 'experience' => 'nullable|string|max:255',
                 'location' => 'nullable|string|max:255',
                 'interview_taken_by' => 'nullable|string|max:255',
                 'remarks' => 'nullable|string',
                 'date' => 'nullable|date',
                 'formid' => 'nullable|string|unique:recruitments,formid',
                 'pin' => 'nullable|string|unique:recruitments,pin',
                 'source' => 'nullable|string|in:Instagram,Facebook,Tiktok,LinkedIn,Indeed,WhatsApp,Facebook Leads',
                 'work_from' => 'nullable|string|in:Home,Office J.sons,Office Sellers',
                 'designation' => 'nullable|string',
                 'status' => 'nullable|string|in:No Answer,Not Interested,Call Back,Interested,Hired,Rejected,Left,Pending',
                 'interviewer_remarks' => 'nullable|string|in:Question type,Excellent,Good,Average,Poor',
                 'refer_to' => 'nullable|exists:employees,id',

             ]);
     
             // Generate formid in the format "ddmmyy" with a random 2-digit suffix (only digits)
             $currentDate = now();
             $formid = $currentDate->format('dmy'); // Example: 201124 for 20th November 2024
             
             // Generate a random 2-digit suffix using rand()
             $suffix = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // Example: '01' or '99'
     
             // Combine the date with the suffix to make formid unique
             $formid .= $suffix;
     
             // Ensure formid is unique
             while (Recruitment::where('formid', $formid)->exists()) {
                 // Regenerate the suffix if formid already exists
                 $suffix = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // Regenerate the suffix
                 $formid = $currentDate->format('dmy') . $suffix; // Regenerate formid
             }
     
             // Generate pin dynamically
             $lastPin = Recruitment::orderBy('id', 'desc')->value('pin');
             $newPin = $this->generatePin($lastPin);
     
             // Add formid and pin to the request data
             $data = $request->all();
             $data['formid'] = $formid; // Use the new unique formid format
             $data['pin'] = $newPin;
     
             // Store data in the database
             $recruitment = Recruitment::create($data);
     
             if (!$recruitment) {
                 // Log error if data is not saved
                 Log::error('Recruitment data not stored. Data: ', $data);
                 return redirect()->route('recruitment.create')->with('error', 'Failed to save recruitment data.');
             }
     
             // Redirect with a success message
             return redirect()->route('recruitment.create')->with('success', 'Recruitment data saved successfully.');
     
         } catch (\Exception $e) {
             // Log the exception if something goes wrong
             Log::error('Error storing recruitment data: ' . $e->getMessage(), [
                 'exception' => $e,
                 'data' => $request->all()
             ]);
             
             // Return error message to the user
             return redirect()->route('recruitment.create')->with('error', 'An error occurred while saving recruitment data. Please try again.');
         }
     }
     


    /**
     * Generate a new PIN based on the last PIN.
     */
    private function generatePin($lastPin)
{
    // If there is no last PIN (first generation), generate a random PIN
    if (!$lastPin) {
        $newPin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    } else {
        // Generate a truly random PIN within the range 1000-9999
        $newPin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    // Ensure the new PIN is unique
    while (Recruitment::where('pin', $newPin)->exists()) {
        // Regenerate the PIN if it already exists
        $newPin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    return $newPin;
}

    
    
    

    public function show($id)
{
    $recruitment = Recruitment::findOrFail($id);
    return view('recruitement.show', compact('recruitment'));
}

public function edit($id)
{        $sources = ['Instagram', 'Facebook', 'Tiktok', 'LinkedIn', 'Indeed', 'WhatsApp', 'Facebook Leads'];
    $workFromOptions = ['Home', 'Office J.sons', 'Office Sellers'];
    $designations = [
        'Avatar-CSR', 'Voice-CSR', 'WFH Voice', 'Junior Closer', 'WFH JC', 'Closer', 'WFH Closer',
        'Dialer & Data Manager', 'Trainer', 'QA', 'HR', 'Recruiter', 'SEO', 'Digital Marketer', 'IT',
        'Web Developer', 'Ads Manager', 'Social Media Expert', 'Accountant', 'Manager', 'Content Writer', 'Graphic Designer'
    ];
    $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];
    $interviewerRemarks = ['Question type', 'Excellent', 'Good', 'Average', 'Poor'];

    $recruitment = Recruitment::findOrFail($id);
    return view('recruitement.edit', compact('recruitment','sources','workFromOptions', 'designations', 'statuses', 'interviewerRemarks'));
}

public function final($id)
{        $sources = ['Instagram', 'Facebook', 'Tiktok', 'LinkedIn', 'Indeed', 'WhatsApp', 'Facebook Leads'];
    $workFromOptions = ['Home', 'Office J.sons', 'Office Sellers'];
    $designations = [
        'Avatar-CSR', 'Voice-CSR', 'WFH Voice', 'Junior Closer', 'WFH JC', 'Closer', 'WFH Closer',
        'Dialer & Data Manager', 'Trainer', 'QA', 'HR', 'Recruiter', 'SEO', 'Digital Marketer', 'IT',
        'Web Developer', 'Ads Manager', 'Social Media Expert', 'Accountant', 'Manager', 'Content Writer', 'Graphic Designer'
    ];
    $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];
    $interviewerRemarks = ['Question type', 'Excellent', 'Good', 'Average', 'Poor'];

    $recruitment = Recruitment::findOrFail($id);
    return view('recruitement.interview', compact('recruitment','sources','workFromOptions', 'designations', 'statuses', 'interviewerRemarks'));
}



public function update(Request $request, $id)
{
    $recruitment = Recruitment::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'contact_no' => 'required|string|max:20',
        'email' => 'nullable|email|unique:recruitments,email', // Make email optional
        'experience' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:255',
        'interview_taken_by' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
        'age' => 'nullable|integer|min:18|max:100',
        'alternate_number' => 'nullable|string|max:20',
        'date' => 'nullable|date',
        'emergency_number' => 'nullable|string|max:20',
        'relation' => 'nullable|string|max:255',

        // Questions
        'willing_to_work_night_shift' => 'required|boolean',
        'telemarketing_experience' => 'required|boolean',
        'total_work_experience' => 'nullable|string|max:255',
        'job_reason' => 'nullable|string',
        'currently_student' => 'required|boolean',
        'strength' => 'nullable|string',
        'weakness' => 'nullable|string',
        'self_description' => 'nullable|string',

        // Dropdowns
        'source' => 'nullable|string|in:Instagram,Facebook,Tiktok,LinkedIn,Indeed,WhatsApp,Facebook Leads',
        'work_from' => 'nullable|string|in:Home,Office J.sons,Office Sellers',
        'designation' => 'nullable|string',
        'status' => 'nullable|string|in:No Answer,Not Interested,Call Back,Interested,Hired,Rejected,Left,Pending',
        'interviewer_remarks' => 'nullable|string|in:Question type,Excellent,Good,Average,Poor',

        // Scores
        'communication_score' => 'nullable|integer|min:0|max:10',
        'accent_score' => 'nullable|integer|min:0|max:10',
        'energy_score' => 'nullable|integer|min:0|max:10',
        'comprehension_score' => 'nullable|integer|min:0|max:10',
        'experience_score' => 'nullable|integer|min:0|max:10',

        // Hired fields
        'hired_comments' => 'nullable|string',
        'project_assigned' => 'nullable|string',
        'signing_bonus' => 'nullable|string',
        'salary_expectation' => 'nullable|string',
        'final_status' => 'nullable|string',
        'joining_date' => 'nullable|date',

        // Rejected fields
        'rejection_reason' => 'nullable|string',
        'rejection_comments' => 'nullable|string',
        'rejection_communication' => 'nullable|integer|min:0|max:10',
        'rejection_energy' => 'nullable|integer|min:0|max:10',
        'rejection_accent' => 'nullable|integer|min:0|max:10',
        'rejection_comprehension' => 'nullable|integer|min:0|max:10',
        // Add more validations as needed
    ]);

    $recruitment->update($request->all());

    return redirect()->route('recruitments.index')->with('success', 'Recruitment record updated successfully.');
}


public function destroy($id)
{
    $recruitment = Recruitment::findOrFail($id);
    $recruitment->delete();

    return redirect()->route('recruitments.index')->with('success', 'Recruitment record deleted successfully.');
}


public function search(Request $request)
{
    // Retrieve formid and pin from the request
    $formid = $request->input('id');
    $pin = $request->input('pin');


    // Search for the recruitment record by formid and pin simultaneously
    $recruitment = Recruitment::where('formid', $formid)
                              ->where('pin', $pin)
                              ->first();


    return view('recruitement.search', compact('recruitment'));
}


public function new()
{
    // Pass dropdown options to the view if needed
    $sources = ['Instagram', 'Facebook', 'Tiktok', 'LinkedIn', 'Indeed', 'WhatsApp', 'Facebook Leads'];
    $workFromOptions = ['Home', 'Office J.sons', 'Office Sellers'];
    $designations = [
        'Avatar-CSR', 'Voice-CSR', 'WFH Voice', 'Junior Closer', 'WFH JC', 'Closer', 'WFH Closer',
        'Dialer & Data Manager', 'Trainer', 'QA', 'HR', 'Recruiter', 'SEO', 'Digital Marketer', 'IT',
        'Web Developer', 'Ads Manager', 'Social Media Expert', 'Accountant', 'Manager', 'Content Writer', 'Graphic Designer'
    ];
    $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];
    $interviewerRemarks = ['Question type', 'Excellent', 'Good', 'Average', 'Poor'];

    return view('recruitement.new', compact('sources', 'workFromOptions', 'designations', 'statuses', 'interviewerRemarks'));
}


public function newstore(Request $request)
     {
         try {
             // Validate the request data
             $request->validate([
                 'name' => 'required|string|max:255',
                 'contact_no' => 'required|string|max:20',
                 'email' => 'nullable|email|unique:recruitments,email', // Make email optional
                 'experience' => 'nullable|string|max:255',
                 'location' => 'nullable|string|max:255',
                 'date' => 'nullable|date',
                 'formid' => 'nullable|string|unique:recruitments,formid',
                 'pin' => 'nullable|string|unique:recruitments,pin',
                 'source' => 'nullable|string|in:Instagram,Facebook,Tiktok,LinkedIn,Indeed,WhatsApp,Facebook Leads',
                 'designation' => 'nullable|string',
             ]);
     
             // Generate formid in the format "ddmmyy" with a random 2-digit suffix (only digits)
             $currentDate = now();
             $formid = $currentDate->format('dmy'); // Example: 201124 for 20th November 2024
             
             // Generate a random 2-digit suffix using rand()
             $suffix = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // Example: '01' or '99'
     
             // Combine the date with the suffix to make formid unique
             $formid .= $suffix;
     
             // Ensure formid is unique
             while (Recruitment::where('formid', $formid)->exists()) {
                 // Regenerate the suffix if formid already exists
                 $suffix = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // Regenerate the suffix
                 $formid = $currentDate->format('dmy') . $suffix; // Regenerate formid
             }
     
             // Generate pin dynamically
             $lastPin = Recruitment::orderBy('id', 'desc')->value('pin');
             $newPin = $this->generatePin($lastPin);
     
             // Add formid and pin to the request data
             $data = $request->all();
             $data['formid'] = $formid; // Use the new unique formid format
             $data['pin'] = $newPin;
     
             // Store data in the database
             $recruitment = Recruitment::create($data);
     
             if (!$recruitment) {
                 // Log error if data is not saved
                 Log::error('Recruitment data not stored. Data: ', $data);
                 return redirect()->route('recruitment.create')->with('error', 'Failed to save recruitment data.');
             }
     
             // Redirect with a success message
             return redirect()->route('recruitment.create')->with('success', 'Recruitment data saved successfully.');
     
         } catch (\Exception $e) {
             // Log the exception if something goes wrong
             Log::error('Error storing recruitment data: ' . $e->getMessage(), [
                 'exception' => $e,
                 'data' => $request->all()
             ]);
             
             // Return error message to the user
             return redirect()->route('recruitment.create')->with('error', 'An error occurred while saving recruitment data. Please try again.');
         }
     }
}
