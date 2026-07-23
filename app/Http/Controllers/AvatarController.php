<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AvatarLead;
use Illuminate\Support\Str;
use App\Exports\AvatarLeadExport;
// use App\Exports\AvatarQALead;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\AvatarQALead;
use App\Models\AvatarFrom;
use App\Models\Team;
use App\Models\Recording;
use App\Http\Controllers\Controller;
use App\Models\Dialerlist;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Log;
use App\Models\DialersUnified;

// use App\Models\AvatarFrom; This is Storing Temp Data Fro inspecto de Team


class AvatarController extends Controller
{
    // function to view the avatar page
    public function index()
    {
        // Get authenticated user's ID
        $dialerId = auth()->user()->dialer_id;

        $pendingCount = AvatarLead::where('dialer_id', $dialerId)->where('QAstatus', 'pending')->count();
        $approvedCount = AvatarLead::where('dialer_id', $dialerId)->where('QAstatus', 'approved')->count();
        $rejectedCount = AvatarLead::where('dialer_id', $dialerId)->where('QAstatus', 'rejected')->count();

        // Fetch leads for the authenticated user with different QAstatus
        $pendingLeads = AvatarLead::where('dialer_id', $dialerId)->where('QAstatus', 'pending')->orderBy('created_at', 'desc')->get();
        $approvedLeads = AvatarLead::where('dialer_id', $dialerId)->where('QAstatus', 'approved')->orderBy('created_at', 'desc')->get();
        $rejectedLeads = AvatarLead::where('dialer_id', $dialerId)->where('QAstatus', 'rejected')->orderBy('created_at', 'desc')->get();
        $otherLeads = AvatarLead::where('dialer_id', $dialerId)
            ->whereIn('QAstatus', ['on review', 'no recording'])
            ->orderBy('created_at', 'desc')
            ->get();


        // Pass the data to the view
        return view('avatar.index', compact('pendingLeads', 'otherLeads', 'approvedLeads', 'rejectedLeads', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }
    public function all_avatar_leads(Request $request)
    {
        if (!auth()->user()->can('view all avatar leads')) {
            return redirect('/')->with('error', 'Unauthorized Page');
        }

        // Fetch the latest 100 AvatarLeads
        $avatarLeads = AvatarLead::latest()->limit(100)->get();

        // Check if there is a search query
        if ($request->has('search')) {
            $search = $request->input('search');
            // Search for leads based on lead_id or phone_number
            $searchResults = AvatarLead::where('lead_id', 'like', "%{$search}%")
                ->orWhere('dialer_id', 'like', "%{$search}%")
                ->get();
        } else {
            $searchResults = collect(); // Empty collection if no search query
        }

        // Return the view with the avatarLeads data and search results
        return view('avatar.all_avatar_leads', compact('avatarLeads', 'searchResults'));
    }








    public function create(Request $request)
    {
        if (auth()->user()->type != 'Avatar') {
            return redirect('/')->with('error', 'Unauthorized Page');
        }
        $agents = User::query()
            ->forCurrentCenter()
            ->where('type', 'avatar')
            ->pluck('name', 'id'); // Assuming 'name' is the field in your users table

        $verifiers = User::query()
            ->forCurrentCenter()
            ->where('type', 'Closer')
            ->get(); // Assuming 'name' is the field in your users table
        $agent_id = $request->input('agent_id');
        $agent_id = $request->input('fullname');
        $lead_id = $request->input('lead_id');
        $center = $request->input('center');
        $server_ip = $request->input('server_ip');
        $agent_name = $request->input('agent_name');
        $smoker = $request->input('smoker');
        $age = $request->input('age');
        $verifier_name = $request->input('verifier_name');
        $dailer_no = $request->input('dailer_no');
        $list_id = $request->input('list_id');
        $recording_filename = $request->input('recording_filename');
        $phone_number = $request->input('phone_number');
        $campaign = $request->input('campaign');
        $closer = $request->input('closer');
        $group_a = $request->input('group_a');
        $dispo = $request->input('dispo');
        $recording_id = $request->input('recording_id');
        $entry_list_id = $request->input('entry_list_id');
        $user_group = $request->input('user_group');
        $list_name = $request->input('list_name');
        $list_description = $request->input('list_description');
        $entry_date = $request->input('entry_date');
        // $closer_name = $request->input('closer_name');
        $dialername = $request->input('dialername');
        $centername = $request->input('centername');
        $recording_link = "http://$server_ip/RECORDINGS/FTP2/$recording_filename-all.mp3";

        // return view('avatar.create', compact('agents', 'verifiers'));
        return view('display', compact('agents', 'verifiers', 'agent_id', 'lead_id', 'center', 'server_ip', 'closer_name', 'agent_name', 'smoker', 'age', 'verifier_name', 'dailer_no', 'list_id', 'recording_filename', 'phone_number', 'campaign', 'closer', 'group_a', 'dispo', 'recording_id', 'entry_list_id', 'user_group', 'list_name', 'list_description', 'entry_date', 'dialername', 'centername'));
    }

    public function manualform()
    {
        // Check if the authenticated user's type is  'Avatar || Voice'

        if (auth()->user()->type == 'Avatar' || auth()->user()->type == 'Voice') {
            return redirect('/')->with('error', 'Please contact your Team Lead to fill manual form');
        }

        // Fetch agents and verifiers
        $agents = User::query()
            ->forCurrentCenter()
            ->whereIn('type', ['avatar', 'voice'])
            ->where('is_active', 1)
            ->pluck('name', 'id');

        $verifiers = User::query()
            ->forCurrentCenter()
            ->where('type', 'Closer')
            ->where('is_active', 1)
            ->pluck('name', 'id');

        // Return the view with agents and verifiers data
        return view('avatar.create', compact('agents', 'verifiers'));
    }




    public function store(Request $request)
    {
        try {
        
            // Validate the request data
            $validatedData = $request->validate([
                'agent_id' => 'nullable|integer',
                'lead_id' => 'required',
                'dialer_id' => 'required',
                'AGE' => 'nullable|integer',
                'Smoker' => 'nullable|string',
                'closer_name' => 'nullable|string',
                'verifier' => 'nullable|string',
                'centername' => 'nullable|string',
                'list_id' => 'nullable|integer',
                'campaign' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'closer' => 'nullable|string',
                'group_a' => 'nullable|string',
                'server_ip' => 'nullable|string',
                'dispo' => 'nullable|string',
                'agent_name' => 'nullable|string',
                'recording_filename' => 'nullable|string',
                'recording' => 'nullable|string',
                'recording_id' => 'nullable|string',
                'recording_link' => 'nullable|string',
                'entry_list_id' => 'nullable|string',
                'user_group' => 'nullable|string',
                'list_name' => 'nullable|string',
                'list_description' => 'nullable|string',
                'entry_date' => 'nullable|date',
                'dialername' => 'nullable|string',
            ]);


            // Convert dialer_id to uppercase
            $validatedData['dialer_id'] = strtoupper($validatedData['dialer_id']);

            // Check if the lead already exists
            $existingLead = AvatarLead::where('lead_id', $validatedData['lead_id'])->first();

            if ($existingLead) {
                Log::warning('Duplicate lead submission attempted', ['lead_id' => $validatedData['lead_id']]);
                return view('avatar.error', [
                    'error_type' => 'duplicate',
                    'error_message' => 'This lead has already been submitted.',
                    'lead_id' => $validatedData['lead_id']
                ]);
            }

            // Extract the first 8 characters for the date part
            $recording_filename = $validatedData['recording_filename'] ?? null;
            $server_ip = $validatedData['server_ip'] ?? null;
            $datePart = substr($recording_filename, 0, 8);

            // Validate and format the date
            $formattedDate = false;
            if (strlen($datePart) === 8 && ctype_digit($datePart)) {
                $dateObject = DateTime::createFromFormat('Ymd', $datePart);
                $formattedDate = $dateObject ? $dateObject->format('Y-m-d') : false;
            }

            // If the date is invalid, set a default date and log the issue
            if (!$formattedDate) {
                $formattedDate = '1970-01-01';
                Log::warning("Invalid date format in recording filename: $recording_filename");
            }

            // Fetch recording_link from the dialers_unified table based on server_ip (if null then leave recording_link unset)
            $server = DialersUnified::where('server_ip', $server_ip)->first();

            if ($server) {
                // 1. Get base components from the database model
                $base_link = $server->recording_link;
                $folder = $server->folder_name;

                // 2. Define the common final part of the path
                $file_name_with_ext = "$recording_filename-all.mp3";

                // 3. Construct the path conditionally
                if (($server->dialer_name ?? '') === 'JsonsD1') {
                    // Path A: /folder_name/recording_filename-all.mp3
                    $recording_link = "$base_link/$folder/$file_name_with_ext";
                } else {
                    // Path B: /folder_name/formattedDate/recording_filename-all.mp3
                    Log::info('Base link: ' . $base_link);
                    Log::info('Folder: ' . $folder);
                    Log::info('Formatted date: ' . $formattedDate);
                    Log::info('File name with extension: ' . $file_name_with_ext);
                    $recording_link = "$base_link/$folder/$file_name_with_ext";
                }
                // 4. Assign the result
                $validatedData['recording_link'] = $recording_link;
            }
            // If no $server is found, $validatedData['recording_link'] is not set.

            // Resolve agent user id: from agent_id or by dialer_id, then get team from team_agent and store in avatar_leads
            $agentUserId = $validatedData['agent_id'] ?? null;
            $agentUser = null;
            if (empty($agentUserId)) {
                $agentUser = User::where('dialer_id', $validatedData['dialer_id'])->first();
                $agentUserId = $agentUser?->id;
            }
            $validatedData['team_id'] = null;
            if (!empty($agentUserId)) {
                $agentUser = $agentUser ?: User::find($agentUserId);
                $team = $agentUser?->teams()->first();
                if ($team) {
                    $validatedData['team_id'] = $team->id;
                }
            }

            // Determine center_id for multi-tenancy
            // Priority 1: dialer_id prefix (SLZ => center 1, EMP => center 2)
            // Priority 2: agent's center_id from users table
            $centerId = null;
            $dialerIdForCenter = $validatedData['dialer_id'] ?? null;
            if (!empty($dialerIdForCenter)) {
                $dialerIdForCenter = strtoupper($dialerIdForCenter);
                if (Str::startsWith($dialerIdForCenter, 'SLZ')) {
                    $centerId = 1;
                } elseif (Str::startsWith($dialerIdForCenter, 'EMP')) {
                    $centerId = 2;
                }
            }
            if ($centerId === null && $agentUser) {
                $centerId = $agentUser->center_id;
            }
            // Manual form or no center from dialer/agent: use auth user's center
            if ($centerId === null && auth()->check()) {
                $centerId = auth()->user()->center_id;
            }
            $validatedData['center_id'] = $centerId;

            // Create the AvatarLead record
            $validatedData['QAstatus'] = ($request->input('isManual') == 1) ? 'no recording' : 'pending';

            DB::beginTransaction();
            try {
                $avatarLead = AvatarLead::create($validatedData);

                if (!$avatarLead) {
                    throw new \Exception('Failed to create AvatarLead record');
                }

                // Create the Recording record
                Recording::create([
                    // 'recording_link' => $recording_link,
                    'recording_link' => $validatedData['recording_link'] ?? null,
                    'server_ip' => $server_ip ?? null,
                    'recording_filename' => $recording_filename ?? null,
                    'lead_id' => $validatedData['lead_id'] ?? null,
                    'dialer_name' => $validatedData['dialername'] ?? null,
                    'dialer_id' => $validatedData['dialer_id'] ?? null,
                ]);

                DB::commit();
                Log::info('Lead and recording created successfully', ['lead_id' => $validatedData['lead_id']]);

                // Return the success view with submitted data
                return view('success', ['lead' => $avatarLead]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create lead and recording', [
                    'error' => $e->getMessage(),
                    'lead_id' => $validatedData['lead_id']
                ]);
                return view('avatar.error', [
                    'error_type' => 'creation_failed',
                    'error_message' => 'Failed to create the lead. Please try again.',
                    'lead_id' => $validatedData['lead_id']
                ]);
            }
        } catch (QueryException $e) {
            Log::error('Database error while creating lead', [
                'error' => $e->getMessage(),
                'lead_id' => $request->input('lead_id')
            ]);

            if ($e->errorInfo[1] === 1062) {
                return view('avatar.error', [
                    'error_type' => 'database_duplicate',
                    'error_message' => 'A duplicate entry was found in the database.',
                    'lead_id' => $request->input('lead_id')
                ]);
            }
            return view('avatar.error', [
                'error_type' => 'database_error',
                'error_message' => 'A database error occurred. Please try again.',
                'lead_id' => $request->input('lead_id')
            ]);
        } catch (\Exception $e) {
            Log::error('Unexpected error in store method', [
                'error' => $e->getMessage(),
                'lead_id' => $request->input('lead_id')
            ]);
            return view('avatar.error', [
                'error_type' => 'unexpected',
                'error_message' => 'An unexpected error occurred. Please try again.',
                'lead_id' => $request->input('lead_id')
            ]);
        }
    }







    public function checked()
    {

        $user = Auth::user();
        $avatarLeads = AvatarLead::where('agent_id', $user->id)->get();

        return view('avatar.checked-leads', ['userLeads' => $avatarLeads]);
    }


    public function Qachecked()
    {
        // Fetch all AvatarLeads
        $userLeads = AvatarLead::all();

        // Fetch all teams
        $teams = Team::all();

        // Initialize an empty array to store total records for each team
        $teamRecords = [];

        // Loop through each team to calculate total records
        foreach ($teams as $team) {
            $teamAgentIds = $team->agents->pluck('id')->toArray();
            $totalRecords = $userLeads->whereIn('agent_id', $teamAgentIds)
                ->where('QAstatus', 'approved') // Adjust this condition as needed
                ->count();
            $teamRecords[$team->id] = $totalRecords;
        }

        return view('qa-section.avatar-checked', compact('userLeads', 'teams', 'teamRecords'));
    }

    public function leaderboards()
    {
        if (auth()->user()->type != 'Avatar') {
            return redirect('/')->with('error', 'You are not authorized to acces this page');
        }
        return view('avatar.leaderboard');
    }


    public function shrinkAvatarLeads(Request $request)
    {
        ini_set('memory_limit', '1G'); // increase memory limit
        set_time_limit(300);           // increase execution time
        Log::info('Shrink Avatar Leads initiated', ['user_id' => Auth::id()]);

        try {
            if (!in_array(Auth::user()->type, ['QA', 'QA Manager', 'Team Lead', 'Director', 'Dialer Support', 'super admin', 'company'])) {
                return redirect()->route('hrm.dashboard')->with('error', 'Permission Denied');
            }
            Log::info('User authorized for Shrink Avatar Leads', ['user_type' => Auth::user()->type]);

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $dialerId = trim((string) $request->input('dialer_id', ''));

            $query = DB::table('avatar_leads')
                ->select(
                    'avatar_leads.id',
                    'avatar_leads.lead_id',
                    'avatar_leads.dialer_id',
                    'avatar_leads.agent_name',
                    'avatar_leads.recording_link',
                    'avatar_leads.center',
                    'avatar_leads.created_at',
                    'avatar_leads.Qadate',
                    'avatar_leads.Qacomments',
                    'avatar_leads.QAstatus',
                    DB::raw("(
                        SELECT users.name FROM users
                        WHERE users.id = avatar_leads.QaPersonId
                        LIMIT 1
                    ) as qa_person_name"),
                    DB::raw("(
                        SELECT users.name FROM users
                        WHERE users.id = avatar_leads.closer_name
                        LIMIT 1
                    ) as closer_name"),
                    DB::raw("(
                        SELECT teams.name FROM users u
                        LEFT JOIN team_agent ta ON u.id = ta.agent_id
                        LEFT JOIN teams ON ta.team_id = teams.id
                        WHERE u.dialer_id = avatar_leads.dialer_id
                        LIMIT 1
                    ) as team_name")
                )
                ->orderBy('avatar_leads.created_at', 'desc');

            // Apply center scope same as AvatarLead global scope
            $authUser = Auth::user();
            if ($authUser && !in_array($authUser->type, ['super admin', 'company']) && !empty($authUser->center_id)) {
                $query->where('avatar_leads.center_id', $authUser->center_id);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('avatar_leads.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            if ($dialerId !== '') {
                $query->where('avatar_leads.dialer_id', $dialerId);
            }

            // Use pagination instead of loading all rows
            $avatarLeads = $query->paginate(20000); // 100 records per page
            // $avatarLeads = $query->get();

            Log::info('Shrink Avatar Leads query executed', ['record_count' => $avatarLeads->total()]);

            return view('avatar.daily-leads', [
                'avatarLeads' => $avatarLeads,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dialerId' => $dialerId,
            ]);
        } catch (\Exception $e) {
            Log::error('shrinkAvatarLeads ERROR: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }





    public function exportAvatarLeads(Request $request)
    {
        if (!auth()->user()->can('export avatar leads')) {
            return redirect('/')->with('error', 'Unauthorized.');
        }

        ini_set('memory_limit', '1G');
        set_time_limit(600); // Increase time limit for large exports

        if ($request->isMethod('post')) {
            // Check if exporting all records
            $exportAll = $request->input('export_all', false);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            if ($exportAll && $startDate && $endDate) {
                // Export all records based on date range
                Log::info('Exporting all avatar leads', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'user_id' => Auth::id()
                ]);
                
                return Excel::download(
                    new AvatarLeadExport([], $startDate, $endDate, true), 
                    'avatar_leads_all_' . date('Y-m-d') . '.csv'
                );
            } else {
                // Get the selected lead IDs from the request
                $selectedLeads = $request->input('selected_leads', '');
                
                // Handle comma-separated string or array
                if (is_string($selectedLeads) && !empty($selectedLeads)) {
                    $selectedLeads = explode(',', $selectedLeads);
                }
                
                if (empty($selectedLeads)) {
                    return redirect()->back()->with('error', 'Please select at least one lead to export or use Export All.');
                }
                
                Log::info('Exporting selected avatar leads', [
                    'count' => count($selectedLeads),
                    'user_id' => Auth::id()
                ]);
                
                // Create the export instance with the selected lead IDs
                return Excel::download(
                    new AvatarLeadExport($selectedLeads), 
                    'avatar_leads_selected_' . date('Y-m-d') . '.csv'
                );
            }
        }
    }

    public function dailyLeads()
    {
        $dailyLeads = AvatarLead::with('users:email')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('avatar.daily-leads', [
            'dailyLeads' => $dailyLeads,
            'currentPage' => $dailyLeads->currentPage(), // Pass the current page number
        ]);
    }



 public function indexedit()
{
    if (Auth::user()->type == 'QA Manager' || Auth::user()->type == 'Director') {

        // Pending unassigned leads — exclude billing_status = 'No'
        $userLeads = AvatarLead::where('QAstatus', 'pending')
            ->whereNull('QapersonId')
            ->where(function ($q) {
                $q->where('billing_status', '!=', 'No')
                  ->orWhereNull('billing_status');
            })
            ->get();

        $idRange = [
            'start' => $userLeads->isEmpty() ? null : $userLeads->first()->id,
            'end'   => $userLeads->isEmpty() ? null : $userLeads->last()->id,
        ];

        // Team counts — exclude billing_status = 'No'
        $teamPendingCounts = AvatarLead::select('team_id', DB::raw('count(*) as cnt'))
            ->where('QAstatus', 'pending')
            ->whereNull('QapersonId')
            ->whereNotNull('team_id')
            ->where(function ($q) {
                $q->where('billing_status', '!=', 'No')
                  ->orWhereNull('billing_status');
            })
            ->groupBy('team_id')
            ->pluck('cnt', 'team_id')
            ->toArray();

        $teams = Team::orderBy('name')->get()->map(function ($team) use ($teamPendingCounts) {
            $team->pending_unassigned_count = (int) ($teamPendingCounts[$team->id] ?? 0);
            return $team;
        });

        $qaPersons = User::query()
            ->forCurrentCenter()
            ->whereIn('type', ['QA', 'QA Manager'])
            ->get();

        $totals = [
            'pending'      => 0,
            'approved'     => 0,
            'rejected'     => 0,
            'on review'    => 0,
            'no recording' => 0,
        ];

        $qaLeadsStatus = [];
        foreach ($qaPersons as $qaPerson) {

            // ── pending count: exclude billing_status = 'No' (same as index()) ──
            $pendingCount = AvatarLead::where('QapersonId', $qaPerson->id)
                ->where('QAstatus', 'pending')
                ->where(function ($q) {
                    $q->where('billing_status', '!=', 'No')
                      ->orWhereNull('billing_status');
                })
                ->count();

            // ── other statuses: no billing filter needed (already past QA) ──
            $otherCounts = AvatarLead::select('QAstatus', DB::raw('count(*) as count'))
                ->where('QapersonId', $qaPerson->id)
                ->whereIn('QAstatus', ['approved', 'rejected', 'on review', 'no recording'])
                ->groupBy('QAstatus')
                ->get()
                ->pluck('count', 'QAstatus')
                ->toArray();

            $approvedCount    = $otherCounts['approved']     ?? 0;
            $rejectedCount    = $otherCounts['rejected']     ?? 0;
            $onreviewCount    = $otherCounts['on review']    ?? 0;
            $norecordingCount = $otherCounts['no recording'] ?? 0;

            $totals['pending']      += $pendingCount;
            $totals['approved']     += $approvedCount;
            $totals['rejected']     += $rejectedCount;
            $totals['on review']    += $onreviewCount;
            $totals['no recording'] += $norecordingCount;

            $qaLeadsStatus[] = [
                'user'         => $qaPerson,
                'pending'      => $pendingCount,
                'approved'     => $approvedCount,
                'rejected'     => $rejectedCount,
                'on review'    => $onreviewCount,
                'no recording' => $norecordingCount,
            ];
        }

        return view('qa-section.edit_avatar', compact(
            'userLeads', 'idRange', 'qaPersons', 'qaLeadsStatus', 'totals', 'teams'
        ));

    } else {
        return redirect()->route('hrm.dashboard')->with('error', 'Unauthorized Page');
    }
}





    public function updateStatus(Request $request, AvatarQALead $lead)
    {
        $lead->update(['status' => $request->input('status')]);

        return redirect()->back()->with('success', 'Status Updated Successfully');
    }

    // update sales here
    public function search(Request $request)
    {
        if (!auth()->user()->can('search avatar leads')) {
            return redirect('/')->with('error', 'Unauthorized.');
        }

        $search = $request->get('search');

        $avatarLeads = AvatarLead::where('lead_id', 'like', "%$search%")
            ->orWhere('phone_number', 'like', "%$search%")
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('avatar.search_avatar_leads', compact('avatarLeads'));
    }


    public function update(Request $request, $id)
    {
        $avatarLead = AvatarLead::findOrFail($id);

        if (!auth()->user()->can('update avatar lead')) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $avatarLead->closer_status = $request->input('closer_status');
        $avatarLead->count = $request->input('count');
        $avatarLead->save();

        return redirect()->back()->with('success', 'Data updated successfully.');
    }
    // update sales ends here



    // search avatar agents stats starts here 


    public function search_stats_index()
    {
        return view('avatar.search_stats');
    }


    public function search_stats(Request $request)
    {
        $dialer_id = $request->input('dialer_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');



        $results = AvatarLead::select('agent_name')
            ->selectRaw('COUNT(*) as total_leads')
            ->selectRaw('SUM(CASE WHEN QAstatus = "approved" THEN 1 ELSE 0 END) as approved_leads')
            ->selectRaw('SUM(CASE WHEN QAstatus = "rejected" THEN 1 ELSE 0 END) as rejected_leads')
            ->selectRaw('SUM(CASE WHEN QAstatus = "pending" THEN 1 ELSE 0 END) as pending_leads')
            ->selectRaw('SUM(CASE WHEN QAstatus = "on review" THEN 1 ELSE 0 END) as review_leads')
            ->selectRaw('SUM(CASE WHEN QAstatus = "no recording" THEN 1 ELSE 0 END) as norec_leads')
            ->where('dialer_id', $dialer_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('agent_name')
            ->get();

        $totalLeads = $results->sum('total_leads');
        $totalApprovedLeads = $results->sum('approved_leads');
        $totalRejectedLeads = $results->sum('rejected_leads');
        $totalPendingLeads = $results->sum('pending_leads');
        $totalReviewLeads = $results->sum('review_leads');
        $totalNoRecLeads = $results->sum('norec_leads');

        return view('avatar.search_stats', compact('results', 'totalLeads', 'totalApprovedLeads', 'totalRejectedLeads', 'totalPendingLeads', 'totalReviewLeads', 'totalNoRecLeads'));
    }

    // search avatar agents stats ends here 



}
