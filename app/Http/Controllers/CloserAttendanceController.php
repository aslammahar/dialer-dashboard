<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesCloser;
use App\Models\ClosersAttendance;

class CloserAttendanceController extends Controller
{
    // public function index(Request $request)
    // {
    //     $date = $request->get('date', now()->toDateString());

    //     $closers = SalesCloser::with('team')
    //         ->where('active', true)
    //         ->orderBy('name')
    //         ->get();

    //     // Existing marks for this date, keyed by closer id
    //     $existing = ClosersAttendance::whereDate('attendance_date', $date)
    //         ->pluck('status', 'sales_closer_id');

    //     return view('attendance-closer.index', [
    //         'date'     => $date,
    //         'closers'  => $closers,
    //         'existing' => $existing,
    //     ]);
    // }
public function index(Request $request)
{
    $date = $request->get('date', now('America/New_York')->toDateString());

    $closers = SalesCloser::with('team')
        ->where('active', true)
        ->whereNotNull('sales_team_id')
        ->orderBy('name')
        ->get();

    $existing = ClosersAttendance::whereDate('attendance_date', $date)
        ->pluck('status', 'sales_closer_id');

    return view('attendance-closer.index', [
        'date'              => $date,
        'closers'           => $closers,
        'existing'          => $existing,
        'monthlySummary'    => app(\App\Services\SalesService::class)->attendanceMonthlySummary(),
    ]);
}
    public function store(Request $request)
    {
        $data = $request->validate([
            'date'              => ['required', 'date'],
            'status'            => ['required', 'array'],
            'status.*' => ['required', 'in:present,absent,leave,half_day'],
        ]);

        foreach ($data['status'] as $closerId => $status) {
            ClosersAttendance::updateOrCreate(
                ['sales_closer_id' => $closerId, 'attendance_date' => $data['date']],
                ['status' => $status, 'marked_by' => auth()->id()]
            );
        }

        return redirect()
            ->route('attendance-closer.index', ['date' => $data['date']])
            ->with('status', 'Attendance marked for '.$data['date'].'.');
    }
}