<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use App\Models\HikVisionAttendance;
use Carbon\Carbon;

class HikVisionAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Permission check
        $user = auth()->user();
        if (!$user || !$user->can('attendances')) {
            abort(403, 'Unauthorized access.');
        }

        $startDate = $request->input('start_date', now()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // ✅ Company timings
        $settings     = Utility::settings();
        $companyStart = isset($settings['company_start_time'])
            ? Carbon::createFromTimeString($settings['company_start_time'])
            : Carbon::createFromTimeString('19:45'); // default 7:45 PM
        $companyEnd   = isset($settings['company_end_time'])
            ? Carbon::createFromTimeString($settings['company_end_time'])
            : Carbon::createFromTimeString('06:00'); // default 6:00 AM

        // ✅ Fetch attendance
        $records = HikVisionAttendance::whereBetween('event_time', [$start, $end])
            ->orderBy('employee_no')
            ->orderBy('event_time')
            ->get();

        if ($records->isEmpty()) {
            session()->flash('info', 'No attendance found for the selected range.');
            return view('attendances.index', [
                'attendanceData' => [],
                'dates'          => [],
                'startDate'      => $startDate,
                'endDate'        => $endDate,
            ]);
        }

        // ✅ Adjust early morning CheckOuts (night-shift)
        foreach ($records as $record) {
            $eventTime = Carbon::parse($record->event_time);
            if (strcasecmp($record->status, 'checkOut') === 0 && $eventTime->hour < $companyStart->hour) {
                $record->event_time = $eventTime->copy()->subDay();
            }
        }

        // ✅ Group by employee and date
        $grouped = $records->groupBy(function ($item) {
            return trim($item->employee_no) . '|' . Carbon::parse($item->event_time)->toDateString();
        });

        // ✅ Prepare date range
        $dates = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates->push($date->toDateString());
        }

        $attendanceData = [];
        $employees      = $records->groupBy(fn($r) => trim($r->employee_no));

        foreach ($employees as $empNo => $empRecords) {
            $employeeName = optional($empRecords->first())->employee_name;

            foreach ($dates as $date) {
                $key        = trim($empNo) . '|' . $date;
                $dayRecords = $grouped->get($key, collect());

                if ($dayRecords->isEmpty()) {
                    $attendanceData[$employeeName][$date] = [
                        'check_in_out' => 'N/A',
                        'late_by'      => 'N/A',
                        'early_by'     => 'N/A',
                    ];
                    continue;
                }

                // ✅ Find CheckIn & CheckOut
                $checkIn  = $dayRecords->first(fn($r) => strcasecmp(trim($r->status), 'CheckIn') === 0);
                $checkOut = $dayRecords
                    ->sortByDesc('event_time')
                    ->first(fn($r) => strcasecmp(trim($r->status), 'CheckOut') === 0);

                $checkInFormatted  = $checkIn ? Carbon::parse($checkIn->event_time)->format('h:i A') : 'N/A';
                $checkOutFormatted = $checkOut ? Carbon::parse($checkOut->event_time)->format('h:i A') : 'N/A';

                $lateBy  = 'N/A';
                $earlyBy = 'N/A';

                // ---- ✅ LATE BY (ignore <5 min delays) ----
                if ($checkIn) {
                    $expectedStart = Carbon::parse($date)->setTimeFromTimeString($companyStart->format('H:i'));
                    $actualCheckIn = Carbon::parse($checkIn->event_time);

                    if ($actualCheckIn->gt($expectedStart)) {
                        $diffMinutes = $expectedStart->diffInMinutes($actualCheckIn);
                        $lateBy = $diffMinutes <= 5
                            ? 'On Time'
                            : $expectedStart->diff($actualCheckIn)->format('%Hh %Im');
                    } else {
                        $lateBy = 'On Time';
                    }
                }

                // ---- ✅ EARLY BY (fixed AM/PM + night-shift safe) ----
                if ($checkOut) {
                    $expectedEnd = Carbon::parse($date)->setTimeFromTimeString($companyEnd->format('H:i'));

                    // If night shift (end < start), move expected end to next day
                    if ($companyEnd->lessThan($companyStart)) {
                        $expectedEnd->addDay();
                    }

                    $actualCheckOut = Carbon::parse($checkOut->event_time);

                    // Fix for early morning AM checkouts (after midnight)
                    if ($actualCheckOut->lessThan($companyStart)) {
                        $actualCheckOut->addDay();
                    }

                    // ✅ If checkout is after or equal expected end → On Time
                    if ($actualCheckOut->greaterThanOrEqualTo($expectedEnd)) {
                        $earlyBy = 'On Time';
                    } else {
                        // Left early → show difference
                        $earlyBy = $actualCheckOut->diff($expectedEnd)->format('%Hh %Im');
                    }
                }

                $attendanceData[$employeeName][$date] = [
                    'check_in_out' => "$checkInFormatted / $checkOutFormatted",
                    'late_by'      => $lateBy,
                    'early_by'     => $earlyBy,
                ];
            }
        }

        return view('attendances.index', compact('attendanceData', 'dates', 'startDate', 'endDate'));
    }
}
