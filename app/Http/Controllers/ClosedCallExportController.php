<?php

namespace App\Http\Controllers;

use App\Exports\ClosedCallExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ClosedCallExportController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();

        // Optional: additional gate here if you want a hard block on the form
        if (!Gate::forUser($user)->allows('export closed calls')) {
            abort(403, 'You do not have permission to export closed calls.');
        }

        // Policy/status column plus agent-facing labels stored on agent_status (not status)
        $availableStatuses = [
            'pending',
            'approved',
            'rejected',
            'Need to Reach',
            'NSF',
            'Cancelled',
            'DNC',
            'Underwriting',
            'Funded',
            'charged_backed',
            'Potential Lapsed',
            'Scheduled Call Back',
        ];

        // Default: all policy statuses checked; Scheduled Call Back unchecked (filters agent_status)
        $statusesCheckedByDefault = array_values(array_diff($availableStatuses, ['Scheduled Call Back']));

        // Known centers in closed_calls.center_name
        $availableCenters = ['jsons', 'sellerz'];

        return view('reporting.closed-calls-export', compact('availableStatuses', 'availableCenters', 'statusesCheckedByDefault'));
    }

    public function runExport(Request $request)
    {
        $user = Auth::user();

        if (!Gate::forUser($user)->allows('export closed calls')) {
            abort(403, 'You do not have permission to export closed calls.');
        }

        $validated = $request->validate([
            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['string'],
            'centers' => ['nullable', 'array'],
            'centers.*' => ['string'],
            'include_center' => ['nullable', 'boolean'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        // End date inclusive: include all records on that day
        $startDate = $validated['start_date'];
        $endDate = Carbon::parse($validated['end_date'])->endOfDay()->toDateTimeString();

        $filters = [
            'statuses' => $validated['statuses'] ?? [],
            'centers' => $validated['centers'] ?? [],
            'include_center' => (bool)($validated['include_center'] ?? true),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        // Raise limits so large exports (5000+) don't hit 500 / timeouts
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        // Use system export; no row limit when filtering by date range.
        $export = new ClosedCallExport(null, PHP_INT_MAX, $filters, true);

        $fileName = 'closed_calls_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download($export, $fileName);
    }
}

