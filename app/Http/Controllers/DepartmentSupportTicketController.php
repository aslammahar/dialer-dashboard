<?php

namespace App\Http\Controllers;

use App\Models\DepartmentSupport;
use App\Models\DepartmentSupportTicket;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentSupportTicketController extends Controller
{
    // Show create ticket form
    public function create()
    {
        $departments = DepartmentSupport::select('id', 'title', 'role_id')->get();
        return view('department_support_tickets.create', compact('departments'));
    }

    // Get titles based on department role
    public function getTitlesByDepartment($role_id)
    {
        $titles = DepartmentSupport::where('role_id', $role_id)->pluck('title', 'id');
        return response()->json($titles);
    }

    // Store a new ticket
    public function store(Request $request)
    {
        $request->validate([
            'department_support_id' => 'required',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $support = DepartmentSupport::find($request->department_support_id);
        $assignedUsers = [];

        if ($support) {
            if (is_array($support->user_id)) {
                $assignedUsers = $support->user_id;
            } else {
                $decoded = json_decode($support->user_id, true);
                if (is_array($decoded)) {
                    $assignedUsers = $decoded;
                } elseif (is_string($support->user_id) && str_contains($support->user_id, ',')) {
                    $assignedUsers = explode(',', $support->user_id);
                } else {
                    $assignedUsers = [$support->user_id];
                }
            }
        }

        $ticket = DepartmentSupportTicket::create([
            'department_support_id' => $request->department_support_id,
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'Pending',
            'response' => null,
        ]);

        // Assigned users from the department support
        $ticket->assignedUsers()->sync($support->users->pluck('id')->toArray());

        // Redirect to user_index after create
        return redirect()->route('department_support_tickets.my_tickets')
            ->with('success', 'Support request submitted successfully!');
    }

    // Show tickets assigned to logged-in user
   

public function index()
{
    $userId = Auth::id();

    $tickets = DepartmentSupportTicket::with(['support.users', 'user'])
        ->whereHas('support.users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })
        ->latest()
        ->get();

    // Ab safe hai kyunki sirf valid support wale hi aayenge
    foreach ($tickets as $ticket) {
        $ticket->related_users = $ticket->support->users->pluck('name')->toArray();
    }

    return view('department_support_tickets.index', compact('tickets'));
}
 

public function show($id)
{
    $ticket = DepartmentSupportTicket::with(['support', 'user'])->findOrFail($id);

    // Assigned users
    $assignedIds = json_decode($ticket->assigned_to, true) ?? [];
    if (empty($assignedIds) && $ticket->support) {
        $assignedIds = $ticket->support->users->pluck('id')->toArray();
    }
    $assignedUsers = User::whereIn('id', $assignedIds)->pluck('name')->toArray();

    // My Assigned Tickets Summary + Full List
    $userId = Auth::id();
    $myAssignedTickets = DepartmentSupportTicket::with(['support', 'user'])
        ->whereHas('support.users', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })
        ->latest()
        ->get();

    $myAssignedSummary = [
        'total'     => $myAssignedTickets->count(),
        'pending'   => $myAssignedTickets->where('status', 'Pending')->count(),
        'solved'    => $myAssignedTickets->where('status', 'Solved')->count(),
        'declined'  => $myAssignedTickets->where('status', 'Declined')->count(),
    ];

    return view('department_support_tickets.show', compact(
        'ticket',
        'assignedUsers',
        'myAssignedSummary',
        'myAssignedTickets'  // Yeh naya add kiya
    ));
}
public function updateStatus(Request $request, $id, $status)
{
    $ticket = DepartmentSupportTicket::findOrFail($id);

    $ticket->status = $status;

    $old = json_decode($ticket->response, true);

    if (!is_array($old)) {
        $old = [];
    }

    if ($request->reply) {
        $old[] = [
            'by' => Auth::user()->name,
            'msg' => $request->reply,
            'time' => now()->toDateTimeString()
        ];
    }

    $ticket->response = json_encode($old);
    $ticket->save();

    return redirect()->route('department_support_tickets.index')
        ->with('success', 'Ticket updated successfully!');
}

   

    public function userupdateStatus(Request $request, $id, $status)
{
    $ticket = DepartmentSupportTicket::findOrFail($id);

    // Update status only if request coming from Reopen button
    $ticket->status = $status;

    // Handle multi-response history
    $old = json_decode($ticket->response, true);

    if (!is_array($old)) {
        $old = [];
    }

    if ($request->reply) {
        $old[] = [
            'by' => Auth::user()->name,
            'msg' => $request->reply,
            'time' => now()->toDateTimeString()
        ];
    }

    $ticket->response = json_encode($old);
    $ticket->save();

    return redirect()->route('department_support_tickets.my_tickets')
        ->with('success', 'Ticket updated successfully!');
}

    // Show tickets created by logged-in user
public function myTickets()
{
    $userId = Auth::id();

    // Sirf logged-in user ke tickets
    $myCreatedTickets = DepartmentSupportTicket::with(['support', 'support.role'])
        ->where('user_id', $userId)
        ->latest()
        ->get();

    // AB YE BHI USER KE HISAAB SE — NA KI POORE SYSTEM KE!
    $mySummary = [
        'total'     => $myCreatedTickets->count(),
        'pending'   => $myCreatedTickets->where('status', 'Pending')->count(),
        'solved'    => $myCreatedTickets->where('status', 'Solved')->count(),
        'declined'  => $myCreatedTickets->where('status', 'Declined')->count(),
    ];

    return view('department_support_tickets.user_index', compact(
        'myCreatedTickets',
        'mySummary'  // ← ab yeh bhejo, overallSummary nahi
    ));
}

public function dashboard()
{
    $from = request('from', now()->subDays(30)->format('Y-m-d'));
    $to   = request('to', now()->format('Y-m-d'));
    $roleId = request('department'); // Yeh ab Role ID hai (HR, IT etc)

    // Step 1: Get all tickets in date range
    $query = DepartmentSupportTicket::with(['user', 'support'])
                ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);

    if ($roleId) {
        // Get all department_support entries with this role_id
        $supportIds = DepartmentSupport::where('role_id', $roleId)->pluck('id');
        $query->whereIn('department_support_id', $supportIds);
    }

    $tickets = $query->latest()->get();

    // Overall Summary
    $summary = [
        'total'     => $tickets->count(),
        'pending'   => $tickets->where('status', 'Pending')->count(),
        'solved'    => $tickets->where('status', 'Solved')->count(),
        'declined'  => $tickets->where('status', 'Declined')->count(),
    ];

    // Employee Wise Tickets (Grouped by Creator)
    $employeeTickets = $tickets->groupBy('user_id')->map(function ($group) {
        $first = $group->first();
        $deptSupport = $first->support;
        $role = $deptSupport ? Role::find($deptSupport->role_id) : null;

        return [
            'name'       => $first->user?->name ?? 'Unknown User',
            'department' => $role?->name ?? 'Unknown Department',
            'tickets'    => $group
        ];
    })->sortBy('name');

    // Get All Roles jo DepartmentSupport mein use ho rahe hain (Unique)
    $usedRoleIds = DepartmentSupport::distinct()->pluck('role_id');
    $departments = Role::whereIn('id', $usedRoleIds)->orderBy('name')->get();

    return view('department_support_tickets.ticket_dashboard', compact(
        'summary',
        'employeeTickets',
        'departments',
        'from',
        'to',
        'roleId'
    ));
}

}
