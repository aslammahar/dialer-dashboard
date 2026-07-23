<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClosedCall;
use App\Models\User;
use App\Models\VerifierAssignment;
use Illuminate\Support\Facades\Auth;

class VerifierController extends Controller
{
    // ─── Admin: list closed calls & assign to verifier ─────────────────────────
    public function assignIndex(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        $verifiers = User::role('Verifier')->orderBy('name')->get(['id', 'name']);

        $search    = $request->input('search');
        $status    = $request->input('status');
        $verifier  = $request->input('verifier_filter');
        $perPage   = $request->input('per_page', 20);

        $query = ClosedCall::withoutGlobalScopes()
            ->with(['verifierAssignment.verifier'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                  ->orWhere('policy_id',         'LIKE', "%{$search}%")
                  ->orWhere('phone_number',       'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($verifier === 'unassigned') {
            $query->doesntHave('verifierAssignment');
        } elseif ($verifier && $verifier !== 'all') {
            $query->whereHas('verifierAssignment', fn($q) => $q->where('verifier_id', $verifier));
        }

        $calls = $query->paginate($perPage);

        $statuses = ['pending', 'Approved', 'Funded', 'charged_backed', 'Potential Lapsed', 'Rejected'];

        return view('verifier.assign', compact('calls', 'verifiers', 'search', 'status', 'verifier', 'perPage', 'statuses'));
    }

    // ─── Admin: bulk assign selected calls to a verifier ──────────────────────
    public function store(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        $request->validate([
            'verifier_id'      => 'required|exists:users,id',
            'closed_call_ids'  => 'required|array|min:1',
            'closed_call_ids.*'=> 'exists:closed_calls,id',
            'reasons'          => 'nullable|array',
            'reasons.*'        => 'nullable|string|max:1000',
        ]);

        $verifierId = $request->input('verifier_id');
        $callIds    = $request->input('closed_call_ids');
        $reasons    = $request->input('reasons', []);
        $now        = now();

        foreach ($callIds as $callId) {
            VerifierAssignment::updateOrCreate(
                ['closed_call_id' => $callId],
                [
                    'verifier_id' => $verifierId,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => $now,
                    'reason'      => $reasons[$callId] ?? null,
                ]
            );
        }

        return back()->with('success', count($callIds) . ' call(s) assigned to verifier successfully.');
    }

    // ─── Admin: remove assignment ──────────────────────────────────────────────
    public function unassign($id)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        VerifierAssignment::where('closed_call_id', $id)->delete();

        return back()->with('success', 'Assignment removed.');
    }

    // ─── Verifier: dashboard – see only assigned calls ─────────────────────────
    public function dashboard(Request $request)
    {
        if (!auth()->user()->hasRole('Verifier')) {
            abort(403, 'Access restricted to Verifier role.');
        }

        $userId  = Auth::id();
        $search  = $request->input('search');
        $status  = $request->input('status');
        $perPage = $request->input('per_page', 20);

        $query = ClosedCall::withoutGlobalScopes()
            ->whereHas('verifierAssignment', fn($q) => $q->where('verifier_id', $userId))
            ->with(['verifierAssignment.assigner'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                  ->orWhere('policy_id',         'LIKE', "%{$search}%")
                  ->orWhere('phone_number',       'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $calls    = $query->paginate($perPage);
        $statuses = ['pending', 'Approved', 'Funded', 'charged_backed', 'Potential Lapsed', 'Rejected'];

        return view('verifier.dashboard', compact('calls', 'search', 'status', 'perPage', 'statuses'));
    }

    // ─── Verifier: update only the remarks field ───────────────────────────────
    public function updateRemarks(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Verifier')) {
            abort(403);
        }

        $userId = Auth::id();

        $assigned = VerifierAssignment::where('closed_call_id', $id)
            ->where('verifier_id', $userId)
            ->exists();

        if (!$assigned) {
            abort(403, 'This call is not assigned to you.');
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        ClosedCall::withoutGlobalScopes()->where('id', $id)->update([
            'remarks' => $request->input('remarks'),
        ]);

        return back()->with('success', 'Remarks updated successfully.');
    }
}
