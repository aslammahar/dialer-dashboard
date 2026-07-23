<?php
// app/Http/Controllers/ReportingController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ReportingData;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingController extends Controller
{
    /**
     * Display the main reporting page
     */
   /**
 * Display the main reporting page - FIXED DATE HANDLING
 */

   /**
 * Display the main reporting page - COMPLETELY FIXED
 */
/**
 * FIXED Controller Index Method - Handle Multiple Month Parameters
 */
public function index(Request $request)
{
    // Check if user has permission to view real reports
    if (!auth()->user()->can('real reports')) {
        // Handle AJAX requests differently
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to view this page.',
                'redirect' => route('dashboard') // or any other route you want to redirect to
            ], 403);
        }
        
        // For regular requests, you can either:
        // Option 1: Abort with 403 error
        abort(403, 'You don\'t have permission to view this page.');
        
        // Option 2: Redirect back with error message (uncomment if you prefer this)
        // return redirect()->back()->with('error', 'You don\'t have permission to view this page.');
        
        // Option 3: Redirect to dashboard with error (uncomment if you prefer this)
        // return redirect()->route('dashboard')->with('error', 'You don\'t have permission to view this page.');
    }

    $viewType = $request->get('view_type', 'monthly');
    
    // FIXED: Get the correct month based on view type
    if ($viewType === 'monthly') {
        $selectedMonth = $request->get('month_selector', $request->get('month', date('Y-m')));
    } else {
        $selectedMonth = $request->get('month_selector_daily', $request->get('month', date('Y-m')));
    }
    
    // FIXED: Get date and handle "all" case
    $selectedDate = $request->get('date_selector', $request->get('date', date('Y-m-d')));
    $showAllDates = $request->get('show_all_dates', false);
    
    // Handle AJAX request for dates only
    if ($request->ajax() && $request->get('get_dates_only')) {
        $monthDates = $this->getMonthDates($selectedMonth);
        return response()->json(['dates' => $monthDates]);
    }
    
    // Handle "all" date selection
    if ($selectedDate === 'all') {
        $showAllDates = true;
        $selectedDate = date('Y-m-d'); // Fallback for parsing
    }
    
    // Convert string to boolean
    if ($showAllDates === '1' || $showAllDates === 1) {
        $showAllDates = true;
    }
    
    $availableDates = $this->getAvailableDates();
    $availableMonths = $this->getAvailableMonths();
    $monthDates = $this->getMonthDates($selectedMonth);
    
    // Determine what data to show
    if ($viewType === 'daily') {
        if ($showAllDates) {
            // Show all dates from the selected month
            $reportingData = $this->getReportingDataByMonth($selectedMonth);
            $displayDate = $selectedMonth . ' (All dates)';
        } else {
            // Show specific date only
            $reportingData = $this->getReportingDataByDate($selectedDate);
            $displayDate = $selectedDate;
        }
    } else {
        // Monthly view - show ONLY that month's data
        $reportingData = $this->getReportingDataByMonth($selectedMonth);
        $displayDate = $selectedMonth;
    }
    
    return view('reporting.index', compact(
        'reportingData', 
        'selectedDate', 
        'selectedMonth',
        'viewType',
        'showAllDates',
        'availableDates', 
        'availableMonths',
        'monthDates',
        'displayDate'
    ));
}

/**
 * FIXED Monthly Data Method - Show ONLY that month's records
 */
 private function getReportingDataByMonth($month)
{
    // Get all users with type 'closer' and dialer_id starting with EMP or SLZ
    $closers = User::where('type', 'closer')
                  ->where(function($query) {
                      $query->where('dialer_id', 'like', 'EMP%')
                            ->orWhere('dialer_id', 'like', 'SLZ%');
                  })
                  ->whereNotNull('dialer_id')
                  ->select('id', 'name', 'dialer_id')
                  ->get();

    $reportingData = [];

    foreach ($closers as $closer) {
        // Get cumulative data for entire month
        $monthlyData = ReportingData::where('employee_id', $closer->dialer_id)
                                   ->whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$month])
                                   ->selectRaw('
                                       employee_id,
                                       MAX(name) as name,
                                       SUM(talktime_seconds) as total_talktime_seconds,
                                       SUM(total_avatar_jcs_xfers) as total_calls,
                                       SUM(working_days) as total_working_days,
                                       SUM(late_min) as total_late_min,
                                       SUM(total_submitted_sales) as total_submitted_sales,
                                       SUM(underwriting_ho) as total_underwriting_ho,
                                       SUM(total_approved) as total_approved,
                                       AVG(average_approved) as avg_approved,
                                       SUM(premium_approved_spd) as total_premium_approved_spd,
                                       AVG(total_conv_calls_submission) as avg_conv_calls_submission,
                                       AVG(total_conv_approved_submission) as avg_conv_approved_submission,
                                       SUM(avatar_xfer) as total_avatar_xfer,
                                       SUM(avatar_xfer_submitted_sales) as total_avatar_xfer_submitted_sales,
                                       SUM(avatar_xfer_approved_sales) as total_avatar_xfer_approved_sales,
                                       AVG(avatar_xfer_conv_calls_submission) as avg_avatar_xfer_conv_calls_submission,
                                       AVG(avatar_xfer_conv_approved_submission) as avg_avatar_xfer_conv_approved_submission,
                                       SUM(jcs_xfers) as total_jcs_xfers,
                                       SUM(jcs_submitted) as total_jcs_submitted,
                                       SUM(jcs_approved) as total_jcs_approved,
                                       AVG(jcs_conv_calls_submission) as avg_jcs_conv_calls_submission,
                                       AVG(jcs_conv_approved_submission) as avg_jcs_conv_approved_submission,
                                       SUM(calls_dur_less_than_200_secs) as total_calls_dur_less_than_200_secs,
                                       SUM(calls_dur_between_200_400_secs) as total_calls_dur_between_200_400_secs,
                                       SUM(calls_dur_greater_than_400_secs) as total_calls_dur_greater_than_400_secs
                                   ')
                                   ->groupBy('employee_id')
                                   ->first();
        
        // Get attendance data for entire month
        $attendanceData = $this->getAttendanceDataForMonth($closer->id, $month);
        
        // Get sales data from closed_calls table for this closer and month
        $salesData = $this->getCloserSalesData($closer->dialer_id, $month);
        
        // FIXED: Get recording samples for monthly view
        $recordingSamples = $this->getMonthlyRecordingSamples($closer->dialer_id, $month);
        
        // Calculate average talktime for the month
        $totalCalls = $monthlyData->total_calls ?? 0;
        $avgTalktimeSeconds = $totalCalls > 0 ? 
            intval($monthlyData->total_talktime_seconds / $totalCalls) : 0;

        // Calculate TOTAL Conv% (Approved/Submission) from actual sales data
        $totalApprovedConv = 0;
        if ($salesData['total_submissions'] > 0) {
            $totalApprovedConv = round(($salesData['total_approved'] / $salesData['total_submissions']) * 100, 2);
        }

        // ALWAYS include the closer, even with 0 calls
        $reportingData[] = [
            'Names' => $closer->name,
            'employee_id' => $closer->dialer_id,
            'report_date' => $month,
            'Working Days' => $attendanceData['present_days'],
            'Late Min' => $attendanceData['late_days'],
            'Talktime' => ReportingData::secondsToTime($monthlyData->total_talktime_seconds ?? 0),
            'Avg Talktime' => ReportingData::secondsToTime($avgTalktimeSeconds),
            'Total Avatar/JCs XFERS' => $totalCalls,
            'TOTAL Submitted Sales' => $salesData['total_submissions'] ?? 0,
            'Underwriting/HO' => $salesData['underwriting_ho'] ?? 0,
            'TOTAL Approved' => $salesData['total_approved'] ?? 0,
            'Average Approved' => $salesData['avg_premium'] ?? 0,
            'Premium Approved SPD' => $salesData['yearly_premium'] ?? 0,
            'TOTAL Conv% (Calls/Submission)' => $salesData['conversion_rate'] ?? 0,
            'TOTAL Conv% (Approved/Submission)' => $totalApprovedConv,
            'Avatar Xfer' => $monthlyData->total_avatar_xfer ?? 0,
            'Avatar Xfer Submitted Sales' => $salesData['avatar_submitted_sales'] ?? 0,
            'Avatar Xfer approved Sales' => $salesData['avatar_approved_sales'] ?? 0,
            'Avatar Xfer Conv% (Calls/Submission)' => round($monthlyData->avg_avatar_xfer_conv_calls_submission ?? 0, 2),
            'Avatar Xfer Conv% (Approved/Submission)' => round($monthlyData->avg_avatar_xfer_conv_approved_submission ?? 0, 2),
            'JCs Xfers' => $monthlyData->total_jcs_xfers ?? 0,
            'JCs Submitted' => $salesData['jcs_submitted_sales'] ?? 0,
            'JCs Approved' => $salesData['jcs_approved_sales'] ?? 0,
            'JCs Conv% (Calls/Submission)' => round($monthlyData->avg_jcs_conv_calls_submission ?? 0, 2),
            'JCs Conv% (Approved/Submission)' => round($monthlyData->avg_jcs_conv_approved_submission ?? 0, 2),
            'Calls Dur Less Than 200 secs' => $monthlyData->total_calls_dur_less_than_200_secs ?? 0,
            'Between 200 secs & 400 secs' => $monthlyData->total_calls_dur_between_200_400_secs ?? 0,
            'Calls Dur Greater Than 400 secs' => $monthlyData->total_calls_dur_greater_than_400_secs ?? 0,
            // FIXED: Include actual recording samples from database
            'Rec 1 200Sec Duration' => $recordingSamples['rec_1'] ?? '',
            'Rec 2 400 sec Duration' => $recordingSamples['rec_2'] ?? '',
            'Rec 3 600 Sec Duration' => $recordingSamples['rec_3'] ?? '',
        ];
    }

    // Sort by Present Days (highest first), then by Total Calls, then by name
    usort($reportingData, function($a, $b) {
        $presentDaysA = $a['Working Days'];
        $presentDaysB = $b['Working Days'];
        
        if ($presentDaysA != $presentDaysB) {
            return $presentDaysB - $presentDaysA;
        }
        
        $callsA = $a['Total Avatar/JCs XFERS'];
        $callsB = $b['Total Avatar/JCs XFERS'];
        
        if ($callsA != $callsB) {
            return $callsB - $callsA;
        }
        
        return strcmp($a['Names'], $b['Names']);
    });

    return $reportingData;
}

   /**
     * Get all available report dates
     */
    private function getAvailableDates()
    {
        return ReportingData::select('report_date')
            ->distinct()
            ->orderBy('report_date', 'desc')
            ->pluck('report_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Get all available report months
     */
    private function getAvailableMonths()
    {
        return ReportingData::selectRaw('DATE_FORMAT(report_date, "%Y-%m") as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month')
            ->toArray();
    }

    /**
     * Get available dates for a specific month
     */
    private function getMonthDates($month)
    {
        return ReportingData::whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$month])
            ->select('report_date')
            ->distinct()
            ->orderBy('report_date', 'desc')
            ->pluck('report_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Get reporting data for a specific date only
     */
private function getReportingDataByDate($date)
{
    // Get all users with type 'closer' and dialer_id starting with EMP or SLZ
    $closers = User::where('type', 'closer')
                  ->where(function($query) {
                      $query->where('dialer_id', 'like', 'EMP%')
                            ->orWhere('dialer_id', 'like', 'SLZ%');
                  })
                  ->whereNotNull('dialer_id')
                  ->select('id', 'name', 'dialer_id')
                  ->get();

    $reportingData = [];

    foreach ($closers as $closer) {
        // Get existing data from database for specific date
        $dbData = ReportingData::where('employee_id', $closer->dialer_id)
                              ->where('report_date', $date)
                              ->first();
        
        // Get attendance data for this date
        $attendanceData = $this->getAttendanceDataForDate($closer->id, $date);
        
        // ALWAYS include the closer, even with 0 calls
        $reportingData[] = $this->formatReportingRow($closer, $dbData, $date, $attendanceData);
    }

    // NEW: Sort by Present Days (highest first), then by Total Calls, then by name
    usort($reportingData, function($a, $b) {
        $presentDaysA = $a['Working Days'];
        $presentDaysB = $b['Working Days'];
        
        // First sort by present days (highest first)
        if ($presentDaysA != $presentDaysB) {
            return $presentDaysB - $presentDaysA;
        }
        
        // If present days are equal, sort by total calls (highest first)
        $callsA = $a['Total Avatar/JCs XFERS'];
        $callsB = $b['Total Avatar/JCs XFERS'];
        
        if ($callsA != $callsB) {
            return $callsB - $callsA;
        }
        
        // If both are equal, sort by name alphabetically
        return strcmp($a['Names'], $b['Names']);
    });

    return $reportingData;
}

/**
 * Get reporting data for entire month (cumulative) - SHOW ALL CLOSERS
 */

/**
 * Get reporting data for entire month (cumulative) - FIXED to include recordings
 */


/**
 * Get recording samples for monthly view
 */
private function getMonthlyRecordingSamples($employeeId, $month)
{
    // Get ALL recording data from this month for this employee
    $recordingData = ReportingData::where('employee_id', $employeeId)
                                  ->whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$month])
                                  ->select([
                                      'rec_1_200_sec_duration',
                                      'rec_2_400_sec_duration',
                                      'rec_3_600_sec_duration',
                                      'report_date'
                                  ])
                                  ->orderBy('report_date', 'desc')
                                  ->get();

    $samples = [
        'rec_1' => '',
        'rec_2' => '',
        'rec_3' => ''
    ];

    // Find the first non-empty recording for each category
    foreach ($recordingData as $record) {
        // Check rec_1 (200 sec)
        if (empty($samples['rec_1'])) {
            $rec1 = trim($record->rec_1_200_sec_duration ?? '');
            if (!empty($rec1) && $rec1 !== 'N/A' && $rec1 !== 'null' && strlen($rec1) > 5) {
                $samples['rec_1'] = $rec1;
            }
        }
        
        // Check rec_2 (400 sec)
        if (empty($samples['rec_2'])) {
            $rec2 = trim($record->rec_2_400_sec_duration ?? '');
            if (!empty($rec2) && $rec2 !== 'N/A' && $rec2 !== 'null' && strlen($rec2) > 5) {
                $samples['rec_2'] = $rec2;
            }
        }
        
        // Check rec_3 (600 sec)
        if (empty($samples['rec_3'])) {
            $rec3 = trim($record->rec_3_600_sec_duration ?? '');
            if (!empty($rec3) && $rec3 !== 'N/A' && $rec3 !== 'null' && strlen($rec3) > 5) {
                $samples['rec_3'] = $rec3;
            }
        }
        
        // Stop if we found all recordings
        if (!empty($samples['rec_1']) && !empty($samples['rec_2']) && !empty($samples['rec_3'])) {
            break;
        }
    }
    
    return $samples;
}

/**
 * Format reporting row - UPDATED to handle null data
 */
    private function formatReportingRow($closer, $dbData, $date, $attendanceData = null)
{
    if ($attendanceData === null) {
        $attendanceData = $this->getAttendanceDataForDate($closer->id, $date);
    }

    // Get sales data for this specific date
    $salesData = $this->getCloserSalesDataForDate($closer->dialer_id, $date);

    // NEW: Calculate TOTAL Conv% (Approved/Submission) from actual sales data
    $totalApprovedConv = 0;
    if ($salesData['total_submissions'] > 0) {
        $totalApprovedConv = round(($salesData['total_approved'] / $salesData['total_submissions']) * 100, 2);
    }

    return [
        'Names' => $closer->name,
        'employee_id' => $closer->dialer_id,
        'report_date' => $date,
        'Working Days' => $attendanceData['present_days'],
        'Late Min' => $attendanceData['late_days'],
        'Talktime' => $dbData->talktime ?? '0:00:00',
        'Avg Talktime' => $dbData->avg_talktime ?? '0:00:00',
        'Total Avatar/JCs XFERS' => $dbData->total_avatar_jcs_xfers ?? 0,
        'TOTAL Submitted Sales' => $salesData['total_submissions'] ?? 0,
        'Underwriting/HO' => $salesData['underwriting_ho'] ?? 0,
        'TOTAL Approved' => $salesData['total_approved'] ?? 0,
        'Average Approved' => $salesData['avg_premium'] ?? 0,
        'Premium Approved SPD' => $salesData['yearly_premium'] ?? 0,
        'TOTAL Conv% (Calls/Submission)' => $salesData['conversion_rate'] ?? 0,
        'TOTAL Conv% (Approved/Submission)' => $totalApprovedConv, // UPDATED: From actual sales data
        'Avatar Xfer' => $dbData->avatar_xfer ?? 0,
        'Avatar Xfer Submitted Sales' => $salesData['avatar_submitted_sales'] ?? 0,
        'Avatar Xfer approved Sales' => $salesData['avatar_approved_sales'] ?? 0,
        'Avatar Xfer Conv% (Calls/Submission)' => $dbData->avatar_xfer_conv_calls_submission ?? 0,
        'Avatar Xfer Conv% (Approved/Submission)' => $dbData->avatar_xfer_conv_approved_submission ?? 0,
        'JCs Xfers' => $dbData->jcs_xfers ?? 0,
        'JCs Submitted' => $salesData['jcs_submitted_sales'] ?? 0,
        'JCs Approved' => $salesData['jcs_approved_sales'] ?? 0,
        'JCs Conv% (Calls/Submission)' => $dbData->jcs_conv_calls_submission ?? 0,
        'JCs Conv% (Approved/Submission)' => $dbData->jcs_conv_approved_submission ?? 0,
        'Calls Dur Less Than 200 secs' => $dbData->calls_dur_less_than_200_secs ?? 0,
        'Between 200 secs & 400 secs' => $dbData->calls_dur_between_200_400_secs ?? 0,
        'Calls Dur Greater Than 400 secs' => $dbData->calls_dur_greater_than_400_secs ?? 0,
        'Rec 1 200Sec Duration' => $dbData->rec_1_200_sec_duration ?? '',
        'Rec 2 400 sec Duration' => $dbData->rec_2_400_sec_duration ?? '',
        'Rec 3 600 Sec Duration' => $dbData->rec_3_600_sec_duration ?? '',
    ];
}
    /**
     * Get attendance data for a specific date
     */

    private function getAttendanceDataForDate($userId, $date)
{
    $attendance = DB::table('attendances')
        ->where('employee_id', $userId)
        ->where('attendance_date', $date)
        ->where('status', 1)
        ->whereNotNull('attendance_time')
        ->where('attendance_time', '!=', '')
        ->where('attendance_time', '!=', '00:00:00')
        ->orderBy('attendance_time', 'asc')
        ->first();

    if (!$attendance) {
        return ['present_days' => 0, 'late_days' => 0];
    }

    try {
        $attendanceTime = \Carbon\Carbon::parse($attendance->attendance_time);
        $officeTime = \Carbon\Carbon::parse('19:45:00');
        
        $lateMinutes = $attendanceTime->gt($officeTime) ? 
            $attendanceTime->diffInMinutes($officeTime) : 0;

        return ['present_days' => 1, 'late_days' => $lateMinutes];
    } catch (\Exception $e) {
        return ['present_days' => 1, 'late_days' => 0];
    }
}

private function getAttendanceDataForMonth($userId, $month)
{
    $records = DB::table('attendances')
        ->where('employee_id', $userId)
        ->whereRaw('DATE_FORMAT(attendance_date, "%Y-%m") = ?', [$month])
        ->where('status', 1)
        ->whereNotNull('attendance_time')
        ->where('attendance_time', '!=', '')
        ->where('attendance_time', '!=', '00:00:00')
        ->select('attendance_date', 'attendance_time')
        ->orderBy('attendance_date', 'asc')
        ->orderBy('attendance_time', 'asc')
        ->get();

    if ($records->isEmpty()) {
        return ['present_days' => 0, 'late_days' => 0];
    }

    $dailyRecords = $records->groupBy('attendance_date')->map(function($dayRecords) {
        return $dayRecords->first();
    });

    $presentDays = $dailyRecords->count();
    $totalLateMinutes = 0;
    $officeTime = \Carbon\Carbon::parse('19:45:00');

    foreach ($dailyRecords as $record) {
        try {
            $attendanceTime = \Carbon\Carbon::parse($record->attendance_time);
            
            if ($attendanceTime->gt($officeTime)) {
                $lateMinutes = $attendanceTime->diffInMinutes($officeTime);
                if ($lateMinutes > 0 && $lateMinutes < 1440) {
                    $totalLateMinutes += $lateMinutes;
                }
            }
        } catch (\Exception $e) {
            continue;
        }
    }

    return ['present_days' => $presentDays, 'late_days' => $totalLateMinutes];
}

    /**
     * Get closer sales data from closed_calls table
     */
     private function getCloserSalesData($dialerId, $month)
    {
        $approvedStatuses = [
            'Funded', 
        'Charge Back',
        'Approved',
        ];

        $pendingStatuses = [
           'Pending',
        'Underwriting',
        'Need to Reach',
        'NSF',
        ];

        try {
            $user = User::where('dialer_id', $dialerId)->first();
            
            if (!$user) {
                return $this->getEmptySalesData();
            }

            // Get all sales data for this closer
            // 🔒 SECURITY: Escape user input to prevent SQL injection
            $escapedDialerId = DB::connection()->getPdo()->quote($dialerId);
            $escapedUserId = DB::connection()->getPdo()->quote((string)$user->id);
            $escapedUserName = DB::connection()->getPdo()->quote($user->name);
            
            $salesData = DB::table('closed_calls')
                ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
                ->where(function($query) use ($dialerId, $user) {
                    $query->where('closername', $dialerId)
                          ->orWhere('closername', (string)$user->id)
                          ->orWhere('closername', $user->id)
                          ->orWhere('closername', $user->name);
                })
                ->selectRaw('
                    COUNT(*) as total_submissions,
                    SUM(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) as total_approved,
                    SUM(CASE WHEN status IN ("' . implode('","', $pendingStatuses) . '") THEN 1 ELSE 0 END) as underwriting_ho,
                    ROUND(AVG(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium,
                    SUM(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium,
                    ROUND((SUM(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2) as conversion_rate,
                    
                    -- Avatar Sales (when junior_closer_name matches closer ID)
                    -- 🔒 SECURITY: Properly escape values to prevent SQL injection
                    SUM(CASE WHEN (junior_closer_name = ' . $escapedDialerId . ' OR junior_closer_name = ' . $escapedUserId . ' OR junior_closer_name = ' . $escapedUserName . ') THEN 1 ELSE 0 END) as avatar_submitted_sales,
                    SUM(CASE WHEN (junior_closer_name = ' . $escapedDialerId . ' OR junior_closer_name = ' . $escapedUserId . ' OR junior_closer_name = ' . $escapedUserName . ') AND status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) as avatar_approved_sales,
                    
                    -- JCs Sales (when junior_closer_name is different or empty)
                    SUM(CASE WHEN (junior_closer_name != ' . $escapedDialerId . ' AND junior_closer_name != ' . $escapedUserId . ' AND junior_closer_name != ' . $escapedUserName . ') OR junior_closer_name IS NULL OR junior_closer_name = "" THEN 1 ELSE 0 END) as jcs_submitted_sales,
                    SUM(CASE WHEN ((junior_closer_name != ' . $escapedDialerId . ' AND junior_closer_name != ' . $escapedUserId . ' AND junior_closer_name != ' . $escapedUserName . ') OR junior_closer_name IS NULL OR junior_closer_name = "") AND status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) as jcs_approved_sales
                ')
                ->first();

            return [
                'total_submissions' => $salesData->total_submissions ?? 0,
                'total_approved' => $salesData->total_approved ?? 0,
                'underwriting_ho' => $salesData->underwriting_ho ?? 0,
                'avg_premium' => $salesData->avg_premium ?? 0,
                'total_premium' => $salesData->total_premium ?? 0,
                'conversion_rate' => $salesData->conversion_rate ?? 0,
                'yearly_premium' => round(($salesData->total_premium ?? 0) * 12, 2),
                
                // NEW: Avatar and JCs breakdowns
                'avatar_submitted_sales' => $salesData->avatar_submitted_sales ?? 0,
                'avatar_approved_sales' => $salesData->avatar_approved_sales ?? 0,
                'jcs_submitted_sales' => $salesData->jcs_submitted_sales ?? 0,
                'jcs_approved_sales' => $salesData->jcs_approved_sales ?? 0,
            ];

        } catch (\Exception $e) {
            return $this->getEmptySalesData();
        }
    }

    /**
     * Get closer sales data for a specific date
     */
    private function getCloserSalesDataForDate($dialerId, $date)
    {
        $approvedStatuses = [
            'Approved', 'Policy Issued', 'Active', 'Paid', 'Delivered',
            'Completed', 'Issued', 'In Force', 'Confirmed'
        ];

        $pendingStatuses = [
            'Pending', 'Under Review', 'Processing', 'Submitted', 'In Process',
            'Awaiting Approval', 'Review', 'Underwriting'
        ];

        try {
            $user = User::where('dialer_id', $dialerId)->first();
            
            if (!$user) {
                return $this->getEmptySalesData();
            }

            // Get all sales data for this closer and date
            // 🔒 SECURITY: Escape user input to prevent SQL injection
            $escapedDialerId = DB::connection()->getPdo()->quote($dialerId);
            $escapedUserId = DB::connection()->getPdo()->quote((string)$user->id);
            $escapedUserName = DB::connection()->getPdo()->quote($user->name);
            
            $salesData = DB::table('closed_calls')
                ->whereDate('created_at', $date)
                ->where(function($query) use ($dialerId, $user) {
                    $query->where('closername', $dialerId)
                          ->orWhere('closername', (string)$user->id)
                          ->orWhere('closername', $user->id)
                          ->orWhere('closername', $user->name);
                })
                ->selectRaw('
                    COUNT(*) as total_submissions,
                    SUM(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) as total_approved,
                    SUM(CASE WHEN status IN ("' . implode('","', $pendingStatuses) . '") THEN 1 ELSE 0 END) as underwriting_ho,
                    ROUND(AVG(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium,
                    SUM(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium,
                    ROUND((SUM(CASE WHEN status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2) as conversion_rate,
                    
                    -- Avatar Sales (when junior_closer_name matches closer ID)
                    -- 🔒 SECURITY: Properly escape values to prevent SQL injection
                    SUM(CASE WHEN (junior_closer_name = ' . $escapedDialerId . ' OR junior_closer_name = ' . $escapedUserId . ' OR junior_closer_name = ' . $escapedUserName . ') THEN 1 ELSE 0 END) as avatar_submitted_sales,
                    SUM(CASE WHEN (junior_closer_name = ' . $escapedDialerId . ' OR junior_closer_name = ' . $escapedUserId . ' OR junior_closer_name = ' . $escapedUserName . ') AND status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) as avatar_approved_sales,
                    
                    -- JCs Sales (when junior_closer_name is different or empty)
                    SUM(CASE WHEN (junior_closer_name != ' . $escapedDialerId . ' AND junior_closer_name != ' . $escapedUserId . ' AND junior_closer_name != ' . $escapedUserName . ') OR junior_closer_name IS NULL OR junior_closer_name = "" THEN 1 ELSE 0 END) as jcs_submitted_sales,
                    SUM(CASE WHEN ((junior_closer_name != ' . $escapedDialerId . ' AND junior_closer_name != ' . $escapedUserId . ' AND junior_closer_name != ' . $escapedUserName . ') OR junior_closer_name IS NULL OR junior_closer_name = "") AND status IN ("' . implode('","', $approvedStatuses) . '") THEN 1 ELSE 0 END) as jcs_approved_sales
                ')
                ->first();

            return [
                'total_submissions' => $salesData->total_submissions ?? 0,
                'total_approved' => $salesData->total_approved ?? 0,
                'underwriting_ho' => $salesData->underwriting_ho ?? 0,
                'avg_premium' => $salesData->avg_premium ?? 0,
                'total_premium' => $salesData->total_premium ?? 0,
                'conversion_rate' => $salesData->conversion_rate ?? 0,
                'yearly_premium' => round(($salesData->total_premium ?? 0) * 12, 2),
                
                // NEW: Avatar and JCs breakdowns
                'avatar_submitted_sales' => $salesData->avatar_submitted_sales ?? 0,
                'avatar_approved_sales' => $salesData->avatar_approved_sales ?? 0,
                'jcs_submitted_sales' => $salesData->jcs_submitted_sales ?? 0,
                'jcs_approved_sales' => $salesData->jcs_approved_sales ?? 0,
            ];

        } catch (\Exception $e) {
            return $this->getEmptySalesData();
        }
    }


  private function getEmptySalesData()
    {
        return [
            'total_submissions' => 0,
            'total_approved' => 0,
            'underwriting_ho' => 0,
            'avg_premium' => 0,
            'total_premium' => 0,
            'conversion_rate' => 0,
            'yearly_premium' => 0,
            // NEW fields
            'avatar_submitted_sales' => 0,
            'avatar_approved_sales' => 0,
            'jcs_submitted_sales' => 0,
            'jcs_approved_sales' => 0,
        ];
    }


    

    /**
     * Show upload form
     */
    public function uploadForm()
    {
        $availableDates = $this->getAvailableDates();
        return view('reporting.upload', compact('availableDates'));
    }

    /**
     * Handle Excel file upload and process data
     */
   public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['xlsx', 'xls', 'csv'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('The file must be a CSV, XLS, or XLSX file.');
                    }
                }
            ],
            'file_type' => 'required|in:talktime,avatar,jcs,sales,duration', // ADDED 'jcs'
            'report_date' => 'required|date'
        ]);

        try {
            $file = $request->file('excel_file');
            $fileType = $request->input('file_type');
            $reportDate = $request->input('report_date');

            $data = $this->readExcelFile($file);
            
            if (empty($data) || empty($data[0])) {
                return back()->withInput()->with('error', 'File is empty or could not be read.');
            }

            $rows = $data[0];

            switch ($fileType) {
                case 'talktime':
                    $result = $this->processTalktimeData($rows, $reportDate);
                    break;
                case 'avatar':
                    $result = $this->processAvatarExportData($rows, $reportDate);
                    break;
                case 'jcs': // NEW CASE
                    $result = $this->processJcsExportData($rows, $reportDate);
                    break;
                case 'sales':
                    $result = $this->processSalesData($rows, $reportDate);
                    break;
                case 'duration':
                    $result = $this->processDurationData($rows, $reportDate);
                    break;
                default:
                    return back()->withInput()->with('error', 'Invalid file type selected.');
            }

            if ($result['processed'] == 0) {
                return back()->withInput()->with('error', 'No valid data found in the file.');
            }

            return redirect()->route('reporting.index', ['date' => $reportDate])
                           ->with('success', "File uploaded successfully! {$result['processed']} records processed, {$result['updated']} records updated.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }


    /**
     * Read Excel file data
     */
    private function readExcelFile($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($extension === 'csv') {
            return $this->readCsvFile($file);
        } else {
            return $this->readExcelFileEnhanced($file);
        }
    }

    /**
     * Enhanced Excel file reader
     */
    private function readExcelFileEnhanced($file)
    {
        $data = [];
        $filePath = $file->getRealPath();
        
        if (!file_exists($filePath)) {
            throw new \Exception('Excel file does not exist: ' . $filePath);
        }

        try {
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                
                foreach ($worksheet->getRowIterator() as $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getFormattedValue();
                    }
                    
                    if (!empty(array_filter($rowData, function($value) { return trim($value) !== ''; }))) {
                        $data[] = $rowData;
                    }
                }
                
                return [$data];
            }
        } catch (\Exception $e) {
            // Fall back to CSV reading
        }

        return $this->readCsvFile($file);
    }

    /**
     * Read CSV file
     */
    private function readCsvFile($file)
    {
        $data = [];
        $filePath = $file->getRealPath();
        
        if (!file_exists($filePath)) {
            throw new \Exception('File does not exist: ' . $filePath);
        }
        
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception('Could not open file for reading');
        }
        
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (!empty(array_filter($row, function($value) { return trim($value) !== ''; }))) {
                $data[] = $row;
            }
        }
        
        fclose($handle);
        
        return [$data];
    }

    /**
     * Process talktime data from Excel
     */
    private function processTalktimeData($rows, $reportDate)
    {
        $processed = 0;
        $updated = 0;

        $dataRows = $rows;
        
        // Skip title and header rows
        if (count($dataRows) > 0 && count($dataRows[0]) == 1 && 
            (stripos($dataRows[0][0], 'AGENT STATS') !== false || stripos($dataRows[0][0], 'AGENT') !== false)) {
            array_shift($dataRows);
        }
        
        if (count($dataRows) > 0 && count($dataRows[0]) >= 4 && 
            (stripos($dataRows[0][0], 'AGENT') !== false || in_array('AGENT', $dataRows[0]))) {
            array_shift($dataRows);
        }

        foreach ($dataRows as $row) {
            if (empty($row) || count($row) < 4) {
                continue;
            }
            
            $agentColumn = trim($row[0] ?? '');
            $callsColumn = intval($row[1] ?? 0);
            $timeColumn = trim($row[2] ?? '0:00:00');
            $averageColumn = trim($row[3] ?? '0:00:00');

            if (empty($agentColumn)) {
                continue;
            }

            preg_match('/^(EMP\d+|SLZ\d+)/', $agentColumn, $matches);
            if (empty($matches)) {
                continue;
            }
            
            $employeeId = $matches[0];
            $nameParts = explode(' - ', $agentColumn, 2);
            $name = count($nameParts) > 1 ? trim($nameParts[1]) : '';

            $talktimeSeconds = ReportingData::timeToSeconds($timeColumn);
            $avgTalktimeSeconds = ReportingData::timeToSeconds($averageColumn);

            $existingRecord = ReportingData::where('employee_id', $employeeId)
                                         ->where('report_date', $reportDate)
                                         ->first();

            if ($existingRecord) {
                $newTalktimeSeconds = $existingRecord->talktime_seconds + $talktimeSeconds;
                $newTotalCalls = $existingRecord->total_avatar_jcs_xfers + $callsColumn;
                $newAvgTalktimeSeconds = $newTotalCalls > 0 ? intval($newTalktimeSeconds / $newTotalCalls) : 0;

                $existingRecord->update([
                    'name' => $name ?: $existingRecord->name,
                    'talktime' => ReportingData::secondsToTime($newTalktimeSeconds),
                    'talktime_seconds' => $newTalktimeSeconds,
                    'avg_talktime' => ReportingData::secondsToTime($newAvgTalktimeSeconds),
                    'avg_talktime_seconds' => $newAvgTalktimeSeconds,
                    'total_avatar_jcs_xfers' => $newTotalCalls,
                ]);
                $updated++;
            } else {
                ReportingData::create([
                    'employee_id' => $employeeId,
                    'report_date' => $reportDate,
                    'name' => $name,
                    'talktime' => $timeColumn,
                    'talktime_seconds' => $talktimeSeconds,
                    'avg_talktime' => $averageColumn,
                    'avg_talktime_seconds' => $avgTalktimeSeconds,
                    'total_avatar_jcs_xfers' => $callsColumn,
                    'avatar_xfer' => 0,
                    'jcs_xfers' => 0,
                ]);
            }
            
            $processed++;
        }

        return ['processed' => $processed, 'updated' => $updated];
    }

/**
 * FRESH Avatar Export Processing - New Strategy
 * Processes Excel with headers: call_date, phone_number_dialed, status, user, full_name, ..., length_in_sec, ..., recording_location
 */
private function processAvatarExportData($rows, $reportDate)
{
    $processed = 0;
    $updated = 0;

    if (empty($rows)) {
        return ['processed' => 0, 'updated' => 0];
    }

    // Step 1: Find column indexes by analyzing the header row
    $columnIndexes = $this->findAvatarColumnIndexes($rows);
    if (!$columnIndexes) {
        throw new \Exception('Could not find required columns (user, length_in_sec, recording_location) in Excel file');
    }

    // Step 2: Remove header row and get data rows
    $dataRows = array_slice($rows, 1);

    // Step 3: Process each row and group by employee
    $employeeData = [];
    
    foreach ($dataRows as $rowIndex => $row) {
        if (!is_array($row) || count($row) < max(array_values($columnIndexes))) {
            continue; // Skip incomplete rows
        }

        // Extract key data using column indexes
        $user = trim($row[$columnIndexes['user']] ?? '');
        $lengthInSec = (int)($row[$columnIndexes['length_in_sec']] ?? 0);
        $recordingLocation = trim($row[$columnIndexes['recording_location']] ?? '');
        $callDate = trim($row[$columnIndexes['call_date']] ?? '');

        // Skip if missing essential data
        if (empty($user) || $lengthInSec <= 0) {
            continue;
        }

        // Find the user in database
        $dbUser = $this->findEmployeeByIdentifier($user);
        if (!$dbUser) {
            continue; // Skip if user not found
        }

        $employeeId = $dbUser->dialer_id;

        // Initialize employee data structure
        if (!isset($employeeData[$employeeId])) {
            $employeeData[$employeeId] = [
                'user' => $dbUser,
                'total_calls' => 0,
                'duration_categories' => [
                    'less_than_200' => [],
                    'between_200_400' => [],
                    'greater_than_400' => []
                ]
            ];
        }

        // Count total calls for avatar_xfer
        $employeeData[$employeeId]['total_calls']++;

        // Categorize call by duration and store call details
        $callDetails = [
            'duration' => $lengthInSec,
            'recording_location' => $recordingLocation,
            'call_date' => $callDate,
            'row_index' => $rowIndex
        ];

        if ($lengthInSec <= 200) {
            $employeeData[$employeeId]['duration_categories']['less_than_200'][] = $callDetails;
        } elseif ($lengthInSec > 200 && $lengthInSec <= 400) {
            $employeeData[$employeeId]['duration_categories']['between_200_400'][] = $callDetails;
        } else {
            $employeeData[$employeeId]['duration_categories']['greater_than_400'][] = $callDetails;
        }
    }

    // Step 4: Save data to database for each employee
    foreach ($employeeData as $employeeId => $data) {
        $this->saveEmployeeAvatarData($employeeId, $data, $reportDate);
        $processed++;
    }

    return ['processed' => $processed, 'updated' => count($employeeData)];
}

/**
 * Find column indexes in the Excel header row
 */
private function findAvatarColumnIndexes($rows)
{
    if (empty($rows) || !is_array($rows[0])) {
        return false;
    }

    $headerRow = array_map('strtolower', array_map('trim', $rows[0]));
    
    $requiredColumns = [
        'call_date' => ['call_date', 'date'],
        'user' => ['user', 'agent', 'employee'],
        'length_in_sec' => ['length_in_sec', 'duration', 'call_length'],
        'recording_location' => ['recording_location', 'recording', 'audio_file']
    ];

    $columnIndexes = [];

    foreach ($requiredColumns as $key => $possibleNames) {
        $found = false;
        foreach ($possibleNames as $name) {
            $index = array_search($name, $headerRow);
            if ($index !== false) {
                $columnIndexes[$key] = $index;
                $found = true;
                break;
            }
        }
        
        if (!$found && in_array($key, ['user', 'length_in_sec'])) {
            // These are required columns
            return false;
        }
    }

    // If recording_location not found, set to last column as fallback
    if (!isset($columnIndexes['recording_location'])) {
        $columnIndexes['recording_location'] = count($headerRow) - 1;
    }

    return $columnIndexes;
}

/**
 * Find employee by various identifiers
 */
private function findEmployeeByIdentifier($identifier)
{
    $identifier = trim($identifier);
    
    if (empty($identifier)) {
        return null;
    }

    // Try exact dialer_id match first
    $user = User::where('dialer_id', $identifier)
                ->where('type', 'closer')
                ->whereIn('dialer_id', function($query) {
                    $query->select('dialer_id')
                          ->from('users')
                          ->where(function($q) {
                              $q->where('dialer_id', 'like', 'EMP%')
                                ->orWhere('dialer_id', 'like', 'SLZ%');
                          });
                })
                ->first();
    
    if ($user) {
        return $user;
    }

    // Try numeric ID match
    if (is_numeric($identifier)) {
        $user = User::find($identifier);
        if ($user && $user->type === 'closer' && !empty($user->dialer_id)) {
            return $user;
        }
    }

    // Try name-based search
    $user = User::where('type', 'closer')
                ->where(function($query) use ($identifier) {
                    $query->where('name', 'like', '%' . $identifier . '%')
                          ->orWhere('dialer_id', 'like', '%' . $identifier . '%');
                })
                ->whereNotNull('dialer_id')
                ->first();

    return $user;
}

/**
 * Save employee avatar data to database
 */
private function saveEmployeeAvatarData($employeeId, $data, $reportDate)
{
    // Get counts for each duration category
    $lessCount = count($data['duration_categories']['less_than_200']);
    $betweenCount = count($data['duration_categories']['between_200_400']);
    $greaterCount = count($data['duration_categories']['greater_than_400']);

    // Get random recording samples
    $rec1 = $this->selectRandomRecording($data['duration_categories']['less_than_200']);
    $rec2 = $this->selectRandomRecording($data['duration_categories']['between_200_400']);
    $rec3 = $this->selectRandomRecording($data['duration_categories']['greater_than_400']);

    // Check if record exists
    $existingRecord = ReportingData::where('employee_id', $employeeId)
                                   ->where('report_date', $reportDate)
                                   ->first();

    if ($existingRecord) {
        // Update existing record
        $updateData = [
            'avatar_xfer' => ($existingRecord->avatar_xfer ?? 0) + $data['total_calls'],
            'calls_dur_less_than_200_secs' => ($existingRecord->calls_dur_less_than_200_secs ?? 0) + $lessCount,
            'calls_dur_between_200_400_secs' => ($existingRecord->calls_dur_between_200_400_secs ?? 0) + $betweenCount,
            'calls_dur_greater_than_400_secs' => ($existingRecord->calls_dur_greater_than_400_secs ?? 0) + $greaterCount,
        ];

        // Add recording samples if available and field is empty
        if ($rec1 && empty($existingRecord->rec_1_200_sec_duration)) {
            $updateData['rec_1_200_sec_duration'] = $rec1;
        }
        if ($rec2 && empty($existingRecord->rec_2_400_sec_duration)) {
            $updateData['rec_2_400_sec_duration'] = $rec2;
        }
        if ($rec3 && empty($existingRecord->rec_3_600_sec_duration)) {
            $updateData['rec_3_600_sec_duration'] = $rec3;
        }

        $existingRecord->update($updateData);
    } else {
        // Create new record
        ReportingData::create([
            'employee_id' => $employeeId,
            'report_date' => $reportDate,
            'name' => $data['user']->name,
            'avatar_xfer' => $data['total_calls'],
            'calls_dur_less_than_200_secs' => $lessCount,
            'calls_dur_between_200_400_secs' => $betweenCount,
            'calls_dur_greater_than_400_secs' => $greaterCount,
            'rec_1_200_sec_duration' => $rec1,
            'rec_2_400_sec_duration' => $rec2,
            'rec_3_600_sec_duration' => $rec3,
        ]);
    }
}

/**
 * Select random recording from call array
 */
private function selectRandomRecording($callsArray)
{
    if (empty($callsArray)) {
        return null;
    }

    // Filter calls with valid recording locations
    $validRecordings = array_filter($callsArray, function($call) {
        $recording = trim($call['recording_location'] ?? '');
        
        // Must be non-empty and reasonable length
        if (empty($recording) || strlen($recording) > 500) {
            return false;
        }

        // Should look like a valid file/URL (basic validation)
        return (
            strpos($recording, 'http') !== false ||
            strpos($recording, '.mp3') !== false ||
            strpos($recording, '.wav') !== false ||
            strpos($recording, 'recording') !== false ||
            strpos($recording, '/') !== false
        );
    });

    if (empty($validRecordings)) {
        return null;
    }

    // Pick random recording and truncate if too long
    $randomCall = $validRecordings[array_rand($validRecordings)];
    $recording = $randomCall['recording_location'];
    
    return strlen($recording) <= 255 ? $recording : substr($recording, 0, 255);
}

    /**
     * Process sales data (placeholder)
     */
    private function processSalesData($rows, $reportDate)
    {
        return ['processed' => 0, 'updated' => 0];
    }

   

    
    private function processJcsExportData($rows, $reportDate)
    {
        $processed = 0;
        $updated = 0;

        if (empty($rows)) {
            return ['processed' => 0, 'updated' => 0];
        }

        // Step 1: Find column indexes by analyzing the header row
        $columnIndexes = $this->findJcsColumnIndexes($rows);
        if (!$columnIndexes) {
            throw new \Exception('Could not find required user column in Excel file for JCs export');
        }

        // Step 2: Remove header row and get data rows
        $dataRows = array_slice($rows, 1);

        // Step 3: Process each row and count by employee
        $employeeData = [];
        
        foreach ($dataRows as $rowIndex => $row) {
            if (!is_array($row) || count($row) < max(array_values($columnIndexes))) {
                continue; // Skip incomplete rows
            }

            // Extract user identifier using column index
            $user = trim($row[$columnIndexes['user']] ?? '');

            // Skip if missing essential data
            if (empty($user)) {
                continue;
            }

            // Find the user in database
            $dbUser = $this->findEmployeeByIdentifier($user);
            if (!$dbUser) {
                continue; // Skip if user not found
            }

            $employeeId = $dbUser->dialer_id;

            // Initialize or increment count for this employee
            if (!isset($employeeData[$employeeId])) {
                $employeeData[$employeeId] = [
                    'user' => $dbUser,
                    'total_calls' => 0
                ];
            }

            // Simply count the calls - no duration categorization needed
            $employeeData[$employeeId]['total_calls']++;
        }

        // Step 4: Save data to database for each employee
        foreach ($employeeData as $employeeId => $data) {
            $this->saveEmployeeJcsData($employeeId, $data, $reportDate);
            $processed++;
        }

        return ['processed' => $processed, 'updated' => count($employeeData)];
    }

    /**
     * NEW: Find column indexes for JCs Export (similar to Avatar but only needs user column)
     */
    private function findJcsColumnIndexes($rows)
    {
        if (empty($rows) || !is_array($rows[0])) {
            return false;
        }

        $headerRow = array_map('strtolower', array_map('trim', $rows[0]));
        
        // Look for user/agent/employee identifier column
        $userColumnNames = ['user', 'agent', 'employee', 'closer', 'dialer_id'];
        
        foreach ($userColumnNames as $name) {
            $index = array_search($name, $headerRow);
            if ($index !== false) {
                return ['user' => $index];
            }
        }

        // If exact match not found, look for partial matches
        foreach ($headerRow as $index => $header) {
            foreach ($userColumnNames as $name) {
                if (strpos($header, $name) !== false) {
                    return ['user' => $index];
                }
            }
        }

        return false; // No user column found
    }

    /**
     * NEW: Save employee JCs data to database (only count)
     */
    private function saveEmployeeJcsData($employeeId, $data, $reportDate)
    {
        // Check if record exists
        $existingRecord = ReportingData::where('employee_id', $employeeId)
                                       ->where('report_date', $reportDate)
                                       ->first();

        if ($existingRecord) {
            // Update existing record - add to existing JCs count
            $existingRecord->update([
                'jcs_xfers' => ($existingRecord->jcs_xfers ?? 0) + $data['total_calls']
            ]);
        } else {
            // Create new record
            ReportingData::create([
                'employee_id' => $employeeId,
                'report_date' => $reportDate,
                'name' => $data['user']->name,
                'jcs_xfers' => $data['total_calls'],
                // Set other fields to default values
                'avatar_xfer' => 0,
                'talktime' => '0:00:00',
                'talktime_seconds' => 0,
                'avg_talktime' => '0:00:00',
                'avg_talktime_seconds' => 0,
                'total_avatar_jcs_xfers' => 0
            ]);
        }
    }


    /**
     * Export reporting data to Excel
     */
    public function exportExcel(Request $request)
    {
        $selectedDate = $request->get('date', date('Y-m-d'));
        $reportingData = $this->getReportingDataByDate($selectedDate);
        
        $csvContent = $this->generateCsvContent($reportingData);
        
        $filename = 'reporting_data_' . $selectedDate . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate CSV content
     */
    private function generateCsvContent($data)
    {
        $output = fopen('php://temp', 'w');
        
        $headers = [
            'Names', 'Working Days', 'Late Min', 'Talktime', 'Avg Talktime',
            'Total Avatar/JCs XFERS', 'TOTAL Submitted Sales', 'Underwriting/HO',
            'TOTAL Approved', 'Average Approved', 'Premium Approved SPD',
            'TOTAL Conv% (Calls/Submission)', 'TOTAL Conv% (Approved/Submission)',
            'Avatar Xfer', 'Avatar Xfer Submitted Sales', 'Avatar Xfer approved Sales',
            'Avatar Xfer Conv% (Calls/Submission)', 'Avatar Xfer Conv% (Approved/Submission)',
            'JCs Xfers', 'JCs Submitted', 'JCs Approved',
            'JCs Conv% (Calls/Submission)', 'JCs Conv% (Approved/Submission)',
            'Calls Dur Less Than 200 secs', 'Between 200 secs & 400 secs',
            'Calls Dur Greater Than 400 secs', 'Rec 1 200Sec Duration',
            'Rec 2 400 sec Duration', 'Rec 3 600 Sec Duration'
        ];
        
        fputcsv($output, $headers);
        
        foreach ($data as $row) {
            $csvRow = [
                $row['Names'], $row['Working Days'], $row['Late Min'],
                $row['Talktime'], $row['Avg Talktime'], $row['Total Avatar/JCs XFERS'],
                $row['TOTAL Submitted Sales'], $row['Underwriting/HO'], $row['TOTAL Approved'],
                $row['Average Approved'], $row['Premium Approved SPD'],
                $row['TOTAL Conv% (Calls/Submission)'], $row['TOTAL Conv% (Approved/Submission)'],
                $row['Avatar Xfer'], $row['Avatar Xfer Submitted Sales'], $row['Avatar Xfer approved Sales'],
                $row['Avatar Xfer Conv% (Calls/Submission)'], $row['Avatar Xfer Conv% (Approved/Submission)'],
                $row['JCs Xfers'], $row['JCs Submitted'], $row['JCs Approved'],
                $row['JCs Conv% (Calls/Submission)'], $row['JCs Conv% (Approved/Submission)'],
                $row['Calls Dur Less Than 200 secs'], $row['Between 200 secs & 400 secs'],
                $row['Calls Dur Greater Than 400 secs'], $row['Rec 1 200Sec Duration'],
                $row['Rec 2 400 sec Duration'], $row['Rec 3 600 Sec Duration']
            ];
            fputcsv($output, $csvRow);
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }

    /**
     * API endpoint to get reporting data as JSON
     */
    public function apiData(Request $request)
    {
        $selectedDate = $request->get('date', date('Y-m-d'));
        $reportingData = $this->getReportingDataByDate($selectedDate);
        
        return response()->json([
            'success' => true,
            'date' => $selectedDate,
            'data' => $reportingData
        ]);
    }

    /**
     * Get summary statistics
     */
    public function getSummary(Request $request)
    {
        $viewType = $request->get('view_type', 'daily');
        $selectedDate = $request->get('date', date('Y-m-d'));
        $selectedMonth = $request->get('month', date('Y-m'));
        
        if ($viewType === 'monthly') {
            $summary = ReportingData::whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$selectedMonth])
                ->selectRaw('
                    COUNT(DISTINCT employee_id) as total_employees,
                    SUM(total_avatar_jcs_xfers) as total_calls,
                    SUM(talktime_seconds) as total_talktime_seconds,
                    AVG(avg_talktime_seconds) as avg_talktime_seconds,
                    SUM(avatar_xfer) as total_avatar_xfers,
                    SUM(jcs_xfers) as total_jcs_xfers
                ')
                ->first();
        } else {
            $summary = ReportingData::where('report_date', $selectedDate)
                ->selectRaw('
                    COUNT(*) as total_employees,
                    SUM(total_avatar_jcs_xfers) as total_calls,
                    SUM(talktime_seconds) as total_talktime_seconds,
                    AVG(avg_talktime_seconds) as avg_talktime_seconds,
                    SUM(avatar_xfer) as total_avatar_xfers,
                    SUM(jcs_xfers) as total_jcs_xfers
                ')
                ->first();
        }

        return response()->json([
            'success' => true,
            'view_type' => $viewType,
            'period' => $viewType === 'monthly' ? $selectedMonth : $selectedDate,
            'summary' => [
                'total_employees' => $summary->total_employees ?? 0,
                'total_calls' => $summary->total_calls ?? 0,
                'total_talktime' => ReportingData::secondsToTime($summary->total_talktime_seconds ?? 0),
                'avg_talktime' => ReportingData::secondsToTime($summary->avg_talktime_seconds ?? 0),
                'total_avatar_xfers' => $summary->total_avatar_xfers ?? 0,
                'total_jcs_xfers' => $summary->total_jcs_xfers ?? 0,
            ]
        ]);
    }

    /**
     * Debug file content
     */
    public function debugFileContent(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return response()->json(['error' => 'No file uploaded']);
        }

        try {
            $file = $request->file('excel_file');
            
            $fileInfo = [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];

            $data = $this->readExcelFile($file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'error' => 'Could not read file data',
                    'file_info' => $fileInfo
                ]);
            }

            $rows = $data[0];
            
            $sampleData = [];
            for ($i = 0; $i < min(10, count($rows)); $i++) {
                $rowData = [];
                for ($j = 0; $j < count($rows[$i]); $j++) {
                    $rowData["col_$j"] = $rows[$i][$j] ?? '';
                }
                $sampleData["row_$i"] = $rowData;
            }

            $possibleHeaders = [];
            for ($i = 0; $i < min(3, count($rows)); $i++) {
                $possibleHeaders["row_$i"] = $rows[$i];
            }

            return response()->json([
                'file_info' => $fileInfo,
                'total_rows' => count($rows),
                'total_columns' => count($rows[0] ?? []),
                'possible_headers' => $possibleHeaders,
                'sample_data' => $sampleData
            ], 200, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error reading file: ' . $e->getMessage(),
                'file_info' => $fileInfo ?? []
            ]);
        }
    }

    public function dataManagement(Request $request)
{
    $query = ReportingData::query();
    
    // Apply filters
    if ($request->filled('month')) {
        $query->whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$request->month]);
    }
    
    if ($request->filled('employee')) {
        $query->where('employee_id', $request->employee);
    }
    
    // Get filtered data with pagination
    $uploadedData = $query->orderBy('report_date', 'desc')
                         ->orderBy('employee_id', 'asc')
                         ->paginate(50);
    
    // Get available months for filter
    $availableMonths = ReportingData::selectRaw('DATE_FORMAT(report_date, "%Y-%m") as month')
                                   ->distinct()
                                   ->orderBy('month', 'desc')
                                   ->pluck('month')
                                   ->toArray();
    
    // Get available employees for filter
    $availableEmployees = ReportingData::select('employee_id')
                                      ->distinct()
                                      ->whereNotNull('employee_id')
                                      ->orderBy('employee_id', 'asc')
                                      ->pluck('employee_id')
                                      ->toArray();
    
    return view('reporting.data-management', compact(
        'uploadedData',
        'availableMonths',
        'availableEmployees'
    ));
}public function viewRecord($id)
{
    try {
        $record = ReportingData::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'record' => [
                'id' => $record->id,
                'employee_id' => $record->employee_id,
                'name' => $record->name,
                'report_date' => Carbon::parse($record->report_date)->format('F j, Y'),
                'working_days' => $record->working_days,
                'talktime' => $record->talktime,
                'avg_talktime' => $record->avg_talktime,
                'total_avatar_jcs_xfers' => $record->total_avatar_jcs_xfers,
                'avatar_xfer' => $record->avatar_xfer,
                'jcs_xfers' => $record->jcs_xfers,
                'calls_dur_less_than_200_secs' => $record->calls_dur_less_than_200_secs,
                'calls_dur_between_200_400_secs' => $record->calls_dur_between_200_400_secs,
                'calls_dur_greater_than_400_secs' => $record->calls_dur_greater_than_400_secs,
                'rec_1_200_sec_duration' => $record->rec_1_200_sec_duration,
                'rec_2_400_sec_duration' => $record->rec_2_400_sec_duration,
                'rec_3_600_sec_duration' => $record->rec_3_600_sec_duration,
                'created_at' => $record->created_at->format('F j, Y g:i A'),
                'updated_at' => $record->updated_at->format('F j, Y g:i A'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Record not found'
        ], 404);
    }
}

/**
 * Delete single record
 */
public function deleteRecord($id)
{
    try {
        $record = ReportingData::findOrFail($id);
        $employeeId = $record->employee_id;
        $reportDate = $record->report_date;
        
        $record->delete();
        
        return redirect()->route('reporting.data-management')
                        ->with('success', "Record for {$employeeId} on {$reportDate} has been deleted successfully.");
    } catch (\Exception $e) {
        return redirect()->route('reporting.data-management')
                        ->with('error', 'Error deleting record: ' . $e->getMessage());
    }
}

/**
 * Bulk delete records
 */
public function bulkDelete(Request $request)
{
    $request->validate([
        'type' => 'required|in:all,month',
        'value' => 'nullable|string'
    ]);
    
    try {
        $deletedCount = 0;
        
        if ($request->type === 'all') {
            // Delete all records
            $deletedCount = ReportingData::count();
            ReportingData::truncate();
            
            return redirect()->route('reporting.data-management')
                            ->with('success', "All {$deletedCount} records have been deleted successfully.");
                            
        } elseif ($request->type === 'month' && $request->filled('value')) {
            // Delete records for specific month
            $month = $request->value;
            $deletedCount = ReportingData::whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$month])->count();
            ReportingData::whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$month])->delete();
            
            $monthName = Carbon::parse($month . '-01')->format('F Y');
            return redirect()->route('reporting.data-management')
                            ->with('success', "All {$deletedCount} records for {$monthName} have been deleted successfully.");
        }
        
        return redirect()->route('reporting.data-management')
                        ->with('error', 'Invalid bulk delete operation.');
                        
    } catch (\Exception $e) {
        return redirect()->route('reporting.data-management')
                        ->with('error', 'Error during bulk delete: ' . $e->getMessage());
    }
}

/**
 * Get upload statistics for dashboard
 */
public function getUploadStats()
{
    try {
        $stats = [
            'total_records' => ReportingData::count(),
            'unique_employees' => ReportingData::distinct('employee_id')->count(),
            'date_range' => [
                'earliest' => ReportingData::min('report_date'),
                'latest' => ReportingData::max('report_date')
            ],
            'recent_uploads' => ReportingData::select('report_date', 'employee_id', 'name')
                                           ->orderBy('created_at', 'desc')
                                           ->limit(10)
                                           ->get(),
            'monthly_breakdown' => ReportingData::selectRaw('
                                       DATE_FORMAT(report_date, "%Y-%m") as month,
                                       COUNT(*) as record_count,
                                       COUNT(DISTINCT employee_id) as employee_count
                                   ')
                                   ->groupBy('month')
                                   ->orderBy('month', 'desc')
                                   ->get()
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving upload statistics: ' . $e->getMessage()
        ], 500);
    }
}public function exportDataManagement(Request $request)
{
    $query = ReportingData::query();
    
    // Apply same filters as data management page
    if ($request->filled('month')) {
        $query->whereRaw('DATE_FORMAT(report_date, "%Y-%m") = ?', [$request->month]);
    }
    
    if ($request->filled('employee')) {
        $query->where('employee_id', $request->employee);
    }
    
    $records = $query->orderBy('report_date', 'desc')
                    ->orderBy('employee_id', 'asc')
                    ->get();
    
    $csvContent = $this->generateDataManagementCsv($records);
    
    $filename = 'data_management_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    return response($csvContent)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}

/**
 * Generate CSV content for data management export
 */
private function generateDataManagementCsv($records)
{
    $output = fopen('php://temp', 'w');
    
    // CSV Headers
    $headers = [
        'ID', 'Employee ID', 'Employee Name', 'Report Date', 'Working Days',
        'Talk Time', 'Avg Talk Time', 'Total Calls', 'Avatar Calls', 'JCs Calls',
        'Calls < 200s', 'Calls 200-400s', 'Calls > 400s',
        'Recording 1', 'Recording 2', 'Recording 3',
        'Created At', 'Updated At'
    ];
    
    fputcsv($output, $headers);
    
    // Data rows
    foreach ($records as $record) {
        $csvRow = [
            $record->id,
            $record->employee_id,
            $record->name ?: 'N/A',
            $record->report_date,
            $record->working_days ?: 0,
            $record->talktime ?: '0:00:00',
            $record->avg_talktime ?: '0:00:00',
            $record->total_avatar_jcs_xfers ?: 0,
            $record->avatar_xfer ?: 0,
            $record->jcs_xfers ?: 0,
            $record->calls_dur_less_than_200_secs ?: 0,
            $record->calls_dur_between_200_400_secs ?: 0,
            $record->calls_dur_greater_than_400_secs ?: 0,
            $record->rec_1_200_sec_duration ?: '',
            $record->rec_2_400_sec_duration ?: '',
            $record->rec_3_600_sec_duration ?: '',
            $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '',
            $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : ''
        ];
        fputcsv($output, $csvRow);
    }
    
    rewind($output);
    $csvContent = stream_get_contents($output);
    fclose($output);
    
    return $csvContent;
}


public function cleanOrphanedRecords()
{
    try {
        // Find records where employee_id doesn't exist in users table
        $orphanedRecords = ReportingData::whereNotIn('employee_id', function($query) {
            $query->select('dialer_id')
                  ->from('users')
                  ->whereNotNull('dialer_id')
                  ->where('type', 'closer');
        })->get();
        
        $orphanedCount = $orphanedRecords->count();
        
        if ($orphanedCount > 0) {
            // Delete orphaned records
            ReportingData::whereNotIn('employee_id', function($query) {
                $query->select('dialer_id')
                      ->from('users')
                      ->whereNotNull('dialer_id')
                      ->where('type', 'closer');
            })->delete();
            
            return redirect()->route('reporting.data-management')
                            ->with('success', "Cleaned {$orphanedCount} orphaned records successfully.");
        } else {
            return redirect()->route('reporting.data-management')
                            ->with('info', 'No orphaned records found to clean.');
        }
        
    } catch (\Exception $e) {
        return redirect()->route('reporting.data-management')
                        ->with('error', 'Error cleaning orphaned records: ' . $e->getMessage());
    }
}

/**
 * Duplicate data from one date to another
 */
public function duplicateData(Request $request)
{
    $request->validate([
        'source_date' => 'required|date',
        'target_date' => 'required|date|different:source_date'
    ]);
    
    try {
        $sourceDate = $request->source_date;
        $targetDate = $request->target_date;
        
        // Check if source date has data
        $sourceRecords = ReportingData::where('report_date', $sourceDate)->get();
        
        if ($sourceRecords->isEmpty()) {
            return redirect()->route('reporting.data-management')
                            ->with('error', 'No data found for source date: ' . $sourceDate);
        }
        
        // Check if target date already has data
        $existingTargetRecords = ReportingData::where('report_date', $targetDate)->count();
        
        if ($existingTargetRecords > 0) {
            return redirect()->route('reporting.data-management')
                            ->with('error', 'Target date already has data. Delete existing data first if you want to overwrite.');
        }
        
        $duplicatedCount = 0;
        
        // Duplicate each record
        foreach ($sourceRecords as $sourceRecord) {
            $newRecord = $sourceRecord->replicate();
            $newRecord->report_date = $targetDate;
            $newRecord->created_at = now();
            $newRecord->updated_at = now();
            $newRecord->save();
            $duplicatedCount++;
        }
        
        return redirect()->route('reporting.data-management')
                        ->with('success', "Successfully duplicated {$duplicatedCount} records from {$sourceDate} to {$targetDate}.");
        
    } catch (\Exception $e) {
        return redirect()->route('reporting.data-management')
                        ->with('error', 'Error duplicating data: ' . $e->getMessage());
    }
}
}


