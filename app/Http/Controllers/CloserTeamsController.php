<?php

namespace App\Http\Controllers;

use App\Models\CloserTeam;
use App\Models\User;
use App\Models\CloserTeamMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CloserTeamsController extends Controller
{
    /**
     * Display a listing of closer teams
     */
    public function index()
    {
        $teams = CloserTeam::with('members')
                          ->withCount('members')
                          ->orderBy('name')
                          ->paginate(15);

        return view('closer-teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new closer team
     */
    public function create()
    {
        $availableClosers = User::closersWithoutTeam()
                               ->orderBy('name')
                               ->get();

        return view('closer-teams.create', compact('availableClosers'));
    }

    /**
     * Store a newly created closer team
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:closer_teams,name',
            'description' => 'nullable|string|max:1000',
            'team_lead_id' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            $team = CloserTeam::create([
                'name' => $request->name,
                'description' => $request->description,
                'team_lead_id' => $request->team_lead_id,
                'is_active' => true,
            ]);

            if ($request->has('members') && is_array($request->members)) {
                $validMembers = User::whereIn('id', $request->members)
                                   ->where('type', ['Closer', 'Outsourcing'])
                                   ->whereDoesntHave('closerTeamMember')
                                   ->pluck('id');

                foreach ($validMembers as $userId) {
                    CloserTeamMember::create([
                        'closer_team_id' => $team->id,
                        'user_id' => $userId,
                        'joined_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->route('closer-teams.index')
                        ->with('success', 'Closer team created successfully.');
    }

    /**
     * Display the specified closer team
     */
    public function show(CloserTeam $closerTeam)
    {
        $closerTeam->load('members');
        
        return view('closer-teams.show', compact('closerTeam'));
    }

    /**
     * Show the form for editing the specified closer team
     */
    public function edit(CloserTeam $closerTeam)
    {
        $closerTeam->load('members');
        $availableClosers = User::closersWithoutTeam()
                               ->orderBy('name')
                               ->get();

        return view('closer-teams.edit', compact('closerTeam', 'availableClosers'));
    }

    /**
     * Update the specified closer team
     */
    public function update(Request $request, CloserTeam $closerTeam)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('closer_teams', 'name')->ignore($closerTeam->id)
            ],
            'description' => 'nullable|string|max:1000',
            'team_lead_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $closerTeam->update([
            'name' => $request->name,
            'description' => $request->description,
            'team_lead_id' => $request->team_lead_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('closer-teams.index')
                        ->with('success', 'Closer team updated successfully.');
    }

    /**
     * Remove the specified closer team
     */
    public function destroy(CloserTeam $closerTeam)
    {
        $closerTeam->delete();

        return redirect()->route('closer-teams.index')
                        ->with('success', 'Closer team deleted successfully.');
    }

    /**
     * Add member to team
     */
    public function addMember(Request $request, CloserTeam $closerTeam)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // Check if user is a closer
       

        // Check if user is already in a team
        if ($user->isInCloserTeam()) {
            return response()->json(['error' => 'User is already in a team.'], 422);
        }

        CloserTeamMember::create([
            'closer_team_id' => $closerTeam->id,
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Member added successfully.']);
        }

        return redirect()->back()->with('success', 'Member added successfully.');
    }

    /**
     * Remove member from team
     */
    public function removeMember(Request $request, CloserTeam $closerTeam)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $member = CloserTeamMember::where('closer_team_id', $closerTeam->id)
                                 ->where('user_id', $request->user_id)
                                 ->first();

        if (!$member) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'User is not a member of this team.'], 422);
            }
            return redirect()->back()->with('error', 'User is not a member of this team.');
        }

        $member->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Member removed successfully.']);
        }

        return redirect()->back()->with('success', 'Member removed successfully.');
    }

    /**
     * Get available closers for AJAX requests
     */
    public function getAvailableClosers()
    {
        $closers = User::closersWithoutTeam()
                      ->select('id', 'name', 'email')
                      ->orderBy('name')
                      ->get();

        return response()->json($closers);
    }
}