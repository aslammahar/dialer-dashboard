<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\AvatarQALead;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\TeamReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AvatarLead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 'Team Lead' || Auth::user()->type == 'Director') {
             $teams = Team::with('leader', 'agents')->get();

            return view('teams.teams', compact('teams'));
        } else {
            return redirect()->route('hrm.dashboard')->with('error', 'Permission Denied');
        }
    }
    // use for testing
//  public function index()
// {
//     if (Auth::user()->type == 'Team Lead' || Auth::user()->type == 'Director') {
//         $teams = Team::withoutGlobalScope('center')
//             ->with([
//                 'leader' => fn($q) => $q->withoutGlobalScope('center'),
//                 'agents' => fn($q) => $q->withoutGlobalScope('center'),
//             ])
//             ->get();

//         return view('teams.teams', compact('teams'));
//     } else {
//         return redirect()->route('hrm.dashboard')->with('error', 'Permission Denied');
//     }
// }
    public function create()
    {

        if (Auth::user()->type == 'Team Lead' || Auth::user()->type == 'Director') {

            $teamLeaders = User::query()
                ->forCurrentCenter()
                ->with('teams')
                ->whereIn('type', ['team lead'])
                ->where('is_active', 1)
                ->get();

            $usersWithTeams = User::query()
                ->forCurrentCenter()
                ->with('teams')
                ->whereIn('type', ['avatar', 'voice'])
                ->where('is_active', 1)
                ->get();

            return view('teams.create-team', compact('teamLeaders', 'usersWithTeams'));
        } else {
            return redirect()->route('hrm.dashboard')->with('error', 'Permission Denied');
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'leader' => 'required|exists:users,id',
            'agents' => 'required|array',
            'agents.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $validatedData['name'],
            'leader_id' => $validatedData['leader'],
            // center_id is auto-assigned by Team model for non-bypass users
            'center_id' => auth()->user()->canBypassCenterScope() ? (auth()->user()->center_id ?? null) : null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        // Log the user who created the team
        Log::info('Team created', [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'created_by' => auth()->user()->id,
            'creator_name' => auth()->user()->name,
        ]);


        $team->agents()->attach($validatedData['agents']);

        return redirect()->route('teams.create')->with('success', 'Team created successfully.');
    }

    public function showAssignmentForm()
    {

        if (Auth::user()->type == 'Team Lead' || Auth::user()->type == 'Director') {
            // Fetch the list of agents, teams, and agents in teams
            $agents = User::query()
                ->forCurrentCenter()
                ->with('teams')
                ->whereIn('type', ['avatar', 'voice'])
                ->where('is_active', 1)
                ->get();
            $teams = Team::all();
            // NOTE: team_agent is a pivot table; teams don't have an agent_id column.
            // Keeping behavior stable by listing all active agents who have any team.
            $agentsInTeams = User::query()
                ->forCurrentCenter()
                ->whereHas('teams')
                ->where('is_active', 1)
                ->get();

            return view('teams.teams-managements', compact('agents', 'teams', 'agentsInTeams'));
        } else {
            return redirect()->route('hrm.dashboard')->with('error', 'Permission Denied');
        }
    }

    public function assignToTeam(Request $request)
    {
        $data = $request->validate([
            'team' => 'required|exists:teams,id',
            'agents' => 'required|array', // Array of agent IDs
        ]);

        $team = Team::findOrFail($data['team']);

        // Create an array to collect agents who were not assigned
        $notAssignedAgents = [];

        // Iterate through the selected agents and check if they are already in a team
        foreach ($data['agents'] as $agentId) {
            $agent = User::findOrFail($agentId);

            // Check if the agent is already in a team
            if ($agent->teams->isNotEmpty()) {
                // Add the agent to the list of not assigned agents
                $notAssignedAgents[] = $agent->name;
                continue; // Skip assigning this agent
            }

            // If the agent is not in a team, assign them to the selected team
            $team->agents()->attach($agentId);
        }

        Log::info('Agents assigned to team', [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'assigned_by' => auth()->user()->id,
            'assigner_name' => auth()->user()->name,
            'assigned_agents' => $data['agents'], // Log assigned agent IDs
            'not_assigned_agents' => $notAssignedAgents, // Log agents who were not assigned
        ]);


        if (!empty($notAssignedAgents)) {
            // Agents were not assigned to the team, show an error message
            return redirect()->route('team.assignmentForm')
                ->with('error', 'The following agents are already part of a team: ' . implode(', ', $notAssignedAgents));
        }

        return redirect()->route('team.assignmentForm')
            ->with('success', 'Agents assigned to the team successfully.');
    }

    public function showTeamsOverview()
    {
        // Retrieve all teams with their leaders and agents
        $teams = Team::with('leader', 'agents')->get();
        $agentsInTeams = User::whereHas('teams')->where('is_active', 1)->get();

        return view('teams.overview', compact('teams', 'agentsInTeams'));
    }



    public function removeAgentsFromTeam(Request $request)
    {
        // Validate the form data (e.g., selected agents to remove)
        $data = $request->validate([
            'agentsToRemove' => 'required|array',
            'agentsToRemove.*' => 'exists:users,id',
        ]);

        // Loop through the selected agents and detach them from their teams

        foreach ($data['agentsToRemove'] as $agentId) {
            $agent = User::find($agentId);
            // Loop through the selected agents and detach them from their teams
            Log::info('Agents removed from team', [
                'team_id' => $agent->teams(),
                'team_name' => $agent->teams(),
                'removed_by' => auth()->user()->id,
                'remover_name' => auth()->user()->name,
            ]);
            // Detach the agent from all teams
            $agent->teams()->detach();
        }


        return redirect()->route('teams.overview')->with('success', 'Agents removed from the team successfully.');
    }


    public function listTeams()
    {
        // Fetch all teams with their leader information
        $teams = Team::with('leader')->get();

        // Fetch a list of available leaders (you can adjust this query as needed)
        $leaders = User::where('type', 'team lead')->get();

        return view('teams.teams-update', compact('teams', 'leaders'));
    }

    // Update the team's name
    public function updateTeamName(Request $request, $id)
    {
        // Validate the input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Find the team by its ID
        $team = Team::findOrFail($id);

        // Update the team's name
        $team->name = $validatedData['name'];
        $team->updated_by = auth()->id();
        $team->save();

        Log::info('Team name updated by ', [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'team id updated_by' => auth()->user()->id,
            'team name updated_by' => auth()->user()->name,
            // Log agents who were not assigned
        ]);

        return redirect()->route('list_teams')->with('success', 'Team name updated successfully');
    }

    // Update the team's leader
    public function updateTeamLeader(Request $request, $id)
    {
        // Validate the input
        $validatedData = $request->validate([
            'leader_id' => 'required|exists:users,id',
        ]);

        // Find the team by its ID
        $team = Team::findOrFail($id);

        // Update the team's leader
        $team->leader_id = $validatedData['leader_id'];
        $team->updated_by = auth()->id();
        $team->save();
        Log::info('Team Leader Updated by ', [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'team leader updated_by user id' => auth()->user()->id,
            'team leader updated_by user name' => auth()->user()->name,
            // Log agents who were not assigned
        ]);
        return redirect()->route('list_teams')->with('success', 'Team leader updated successfully');
    }

    // Delete the team
    public function deleteTeam($id)
    {
        // Find the team by its ID
        $team = Team::findOrFail($id);

        // Store the ID of the user deleting the team
        $team->deleted_by = auth()->id();
        $team->save(); // Save before soft deleting

        // Log deletion details
        Log::info('Team deleted', [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'deleted_by_user_id' => auth()->user()->id,
            'deleted_by_user_name' => auth()->user()->name,
            'deleted_at' => now(),
        ]);

        // Soft delete the team
        $team->delete();

        return redirect()->route('list_teams')->with('success', 'Team deleted successfully.');
    }



    //auth user type == Team Lead for function agetnReports
    // public function agentReports()
    //    {
    //
    //        if(Auth::user()->type == 'Team Lead'){
    //
    //        return view('teams.agent-reports');
    //
    //    }
    //
    //    else{
    //        return redirect()->route('hrm.dashboard')->with('error', 'Permission Denied');
    //    }
    //    }


    public function agentReports()
    {
        // Fetch all agents
        $agents = User::whereIn('type', ['Avatar', 'Voice'])->where('is_active', 1)->get();

        // Get a range of dates for the current month
        $dates = [];
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $numberOfDays = now()->daysInMonth;

        for ($day = 1; $day <= $numberOfDays; $day++) {
            $dates[] = Carbon::create($currentYear, $currentMonth, $day)->toDateString();
        }

        // Query to fetch lead counts for each agent from the "avatar_leads" table for each date
        $mergedLeadsCount = DB::table('users')
            ->leftJoinSub(
                DB::table('avatar_leads')
                    ->select('agent_id', DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as lead_count'))
                    ->whereDate('created_at', '>=', Carbon::today()->startOfMonth())
                    ->whereDate('created_at', '<=', Carbon::today()->endOfMonth())
                    ->groupBy('agent_id', 'date'),
                'avatar_leads_counts',
                function ($join) {
                    $join->on('users.id', '=', 'avatar_leads_counts.agent_id');
                }
            )
            ->select('users.name', 'avatar_leads_counts.date', DB::raw('COALESCE(avatar_leads_counts.lead_count, 0) as total_lead_count'))
            ->get();


        // Debugging: Dump the contents of $mergedLeadsCount

        // Return the view with agent data and dates
        //        dd($mergedLeadsCount);
        return view('teams.agent-reports', compact('agents', 'mergedLeadsCount', 'dates'));
    }







    public function qALeadReports()
    {
        // Fetch all agents
        $agents = User::whereIn('type', ['Avatar', 'Voice'])->get();

        // Get a range of dates for the current month
        $dates = [];
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $numberOfDays = now()->daysInMonth;

        for ($day = 1; $day <= $numberOfDays; $day++) {
            $dates[] = Carbon::create($currentYear, $currentMonth, $day)->toDateString();
        }

        // Query to fetch lead counts for each agent from the "avatar_leads" table for each date
        $mergedLeadsCount = AvatarLead::query()
            ->select('agent_id', DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_lead_count'))
            ->whereDate('created_at', '>=', Carbon::today()->startOfMonth())
            ->whereDate('created_at', '<=', Carbon::today()->endOfMonth())
            ->where('QAstatus', '=', 'approved')
            ->groupBy('agent_id', 'date')
            ->with('agent:id,name') // Eager load agent data
            ->get();
        // dd($mergedLeadsCount);

        // Return the view with agent data and dates
        return view('teams.approved_report', compact('agents', 'mergedLeadsCount', 'dates'));
    }





   public function all_team_reports(Request $request)
{
    // Fetch all agents
    $agents = User::whereIn('type', ['Avatar', 'Voice'])->get();

    // Fetch all teams with leaders and agents
    $teams = Team::with('leader', 'agents')->get();

    // Get the selected start and end dates from the request or set them to null
    $startDate = $request->input('start_date', null);
    $endDate = $request->input('end_date', null);

    // Validate and set default date range if not provided
    if (!$startDate || !$endDate) {
        $currentDate = now();
        if (!$startDate) {
            $startDate = $currentDate->copy()->subMonth()->setDay(16)->toDateString();
        }
        if (!$endDate) {
            $endDate = $currentDate->copy()->setDay(15)->toDateString();
        }
    }

    // Get a range of dates for the custom period
    $dates = [];
    for ($date = Carbon::parse($startDate); $date <= Carbon::parse($endDate); $date->addDay()) {
        $dates[] = $date->toDateString();
    }

    // Get selected team ID and QA status from the request
    $selectedTeamId = $request->input('team_id');
    $selectedQAStatus = $request->input('qa_status', 'total');
    $withoutTeamMode = ($selectedTeamId === 'without_team');

    // Fetch individual records exactly like AvatarLeadExport
    $query = DB::table('avatar_leads')
        ->select(
            'avatar_leads.dialer_id',
            DB::raw('DATE(avatar_leads.created_at) as date')
        )
        ->whereBetween('avatar_leads.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

    // Same exact center scope as AvatarLeadExport
    $authUser = Auth::user();
    if ($authUser && !in_array($authUser->type, ['super admin', 'company']) && !empty($authUser->center_id)) {
        $query->where('avatar_leads.center_id', $authUser->center_id);
    }

    // Same exact QA status filter as AvatarLeadExport
    if ($selectedQAStatus != 'total') {
        $query->where('avatar_leads.QAstatus', '=', $selectedQAStatus);
    }

    // Filter by selected team (skip in "without team" mode)
    if ($selectedTeamId && !$withoutTeamMode) {
        $selectedTeam = Team::find($selectedTeamId);
        if ($selectedTeam) {
            $teamAgentIds = $selectedTeam->agents->pluck('dialer_id')->toArray();
            $query->whereIn('avatar_leads.dialer_id', $teamAgentIds);
        } else {
            $query->whereRaw('1=0');
        }
    }

    // Fetch all individual records like AvatarLeadExport
    $allRecords = $query->get();

    // Group by dialer_id and date then count individual records
    $groupedLeadsCount = $allRecords
        ->groupBy('dialer_id')
        ->map(function ($byDialer) {
            return $byDialer->groupBy('date')->map(function ($byDate) {
                return $byDate->count();
            });
        });

    // Build teams to render/export:
    // - without_team => one synthetic team with all agents
    // - selected team => that team only
    // - otherwise => all teams
    if ($withoutTeamMode) {
        $reportTeams = collect([(object) [
            'id' => 'without_team',
            'name' => 'Without Team',
            'agents' => $agents,
        ]]);
    } elseif ($selectedTeamId) {
        $reportTeams = $teams->where('id', (int) $selectedTeamId)->values();
    } else {
        $reportTeams = $teams;
    }

    // Handle export request
    if ($request->has('export')) {
        $exportData = [];
        $processedDialerIds = [];

        foreach ($reportTeams as $team) {
            foreach ($team->agents as $agent) {
                // Skip if this agent was already processed (agent in multiple teams)
                if (in_array($agent->dialer_id, $processedDialerIds)) {
                    continue;
                }
                $processedDialerIds[] = $agent->dialer_id;

                $dateLeadCounts = [];

                foreach ($dates as $date) {
                    $leadCount = isset($groupedLeadsCount[$agent->dialer_id][$date])
                        ? $groupedLeadsCount[$agent->dialer_id][$date]
                        : 0;
                    $dateLeadCounts[] = $leadCount;
                }

                $exportData[] = array_merge(
                    [
                        'team' => $team->name,
                        'agent' => $agent->name,
                    ],
                    $dateLeadCounts,
                    [array_sum($dateLeadCounts)]
                );
            }
        }

        return Excel::download(new TeamReportsExport($exportData, $dates), 'team_reports.xlsx');
    }

    return view('teams.all_team_reports', compact('agents', 'groupedLeadsCount', 'dates', 'teams', 'reportTeams', 'selectedTeamId', 'selectedQAStatus', 'startDate', 'endDate'));
}











    //
    public function all_report(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch all teams with leader and agents
        $teams = Team::with('leader', 'agents')->get();

        // Query to retrieve lead counts for each user
        $mergedLeadsCount = DB::table('users')
            ->leftJoinSub(
                DB::table('leads')
                    ->select('user_id', DB::raw('COUNT(*) as lead_count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('user_id'),
                'leads_counts',
                function ($join) {
                    $join->on('users.id', '=', 'leads_counts.user_id');
                }
            )
            ->leftJoinSub(
                DB::table('avatar_leads')
                    ->select('agent_id', DB::raw('COUNT(*) as lead_count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('agent_id'),
                'avatar_leads_counts',
                function ($join) {
                    $join->on('users.id', '=', 'avatar_leads_counts.agent_id');
                }
            )
            ->select('users.id', 'users.name', DB::raw('COALESCE(leads_counts.lead_count, 0) + COALESCE(avatar_leads_counts.lead_count, 0) as total_lead_count'))
            ->get();

        // Query to retrieve counts of AvatarQALeads with status "approved" and "rejected" and join with users table
        $approvedAvatarQALeadCount = DB::table('users')
            ->leftJoin('avatar_q_a_leads', 'users.email', '=', 'avatar_q_a_leads.agent_email')
            ->where('avatar_q_a_leads.status', 'approved')
            ->whereBetween('avatar_q_a_leads.created_at', [$startDate, $endDate])
            ->select('users.id', 'users.email', DB::raw('COUNT(*) as approved_count'))
            ->groupBy('users.id', 'users.email')
            ->get();

        $rejectedAvatarQALeadCount = DB::table('users')
            ->leftJoin('avatar_q_a_leads', 'users.email', '=', 'avatar_q_a_leads.agent_email')
            ->where('avatar_q_a_leads.status', 'rejected')
            ->whereBetween('avatar_q_a_leads.created_at', [$startDate, $endDate])
            ->select('users.id', 'users.email', DB::raw('COUNT(*) as rejected_count'))
            ->groupBy('users.id', 'users.email')
            ->get();

        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $workingDays = $startCarbon->diffInDaysFiltered(function (Carbon $date) {
            return $date->isWeekday();
        }, $endCarbon);

        return view('teams.all_report', compact('teams', 'mergedLeadsCount', 'approvedAvatarQALeadCount', 'rejectedAvatarQALeadCount', 'workingDays'));
    }




    public function allTeamReports()
    {
        $teams = Team::with('leader', 'agents')->get();

        // Query to fetch leads along with associated team
        $mergedLeads = AvatarLead::with('team')
            ->select('id', 'agent_name', 'lead_id', 'dialer_id', 'Isgreetings', 'Ispitch_call_about', 'Isage', 'Issmoker', 'Ishealth1', 'Isbeneficiary', 'Isaccount', 'Isplan', 'Istransfer_details', 'Isxfer_consent', 'rebuttals', 'Qacomments', 'QAstatus', 'use_of_rebuttals', 'phone_number', 'recording_link')
            ->get();

        return view('teams.teams-report', compact('teams', 'mergedLeads'));
    }
}
