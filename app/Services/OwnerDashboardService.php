<?php

namespace App\Services;

use App\Models\User;
use App\Models\Team;
use App\Models\CloserTeam;
use App\Models\ClosedCall;
use App\Models\OutsourceClosedCall;
use App\Models\AvatarLead;
use App\Models\HikVisionAttendance;
use App\Models\StatusChangeLog;
use App\Models\ReportingData;
use App\Models\Monitoring;
use App\Models\AvatarMonitoring;
use App\Models\QueueSale;
use App\Models\Employee;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OwnerDashboardService
{
    private const APPROVED_STATUSES = [
        'Funded',
        'charged_backed',
        'Approved',
        'Potential Lapsed',
    ];

    /**
     * Parse and resolve dates for a given period in America/New_York timezone.
     */
    public function getDatesForPeriod(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $tz = 'America/New_York';
        $now = Carbon::now($tz);

        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfDay();
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfDay();
                break;
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate, $tz)->startOfDay() : $now->copy()->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate, $tz)->endOfDay() : $now->copy()->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        return [$start, $end];
    }

    /**
     * Calculate start and end dates for the previous matching period.
     */
    public function getPreviousPeriodDates(Carbon $start, Carbon $end): array
    {
        $diffInDays = $start->diffInDays($end) + 1;
        $prevStart = $start->copy()->subDays($diffInDays);
        $prevEnd = $end->copy()->subDays($diffInDays);
        return [$prevStart, $prevEnd];
    }

    /**
     * Safely calculate percentage growth delta.
     */
    private function calculateDelta(float $previous, float $current): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * 1. Company-Wide Sales Summary
     */
    public function getSalesSummary(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);
        list($prevStart, $prevEnd) = $this->getPreviousPeriodDates($start, $end);

        // Fetch current period metrics
        $currentQuery = ClosedCall::whereBetween('created_at', [$start, $end]);
        $prevQuery = ClosedCall::whereBetween('created_at', [$prevStart, $prevEnd]);

        if ($centerId) {
            $currentQuery->where('closed_calls.center_id', $centerId);
            $prevQuery->where('closed_calls.center_id', $centerId);
        }

        $currentSales = $currentQuery->whereIn('status', self::APPROVED_STATUSES)->count();
        $currentPremium = $currentQuery->whereIn('status', self::APPROVED_STATUSES)
            ->whereNotNull('monthly_premium')
            ->sum(DB::raw('CAST(nullif(monthly_premium, "") AS DECIMAL(10,2))'));

        $prevSales = $prevQuery->whereIn('status', self::APPROVED_STATUSES)->count();
        $prevPremium = $prevQuery->whereIn('status', self::APPROVED_STATUSES)
            ->whereNotNull('monthly_premium')
            ->sum(DB::raw('CAST(nullif(monthly_premium, "") AS DECIMAL(10,2))'));

        $salesDelta = $this->calculateDelta($prevSales, $currentSales);
        $premiumDelta = $this->calculateDelta($prevPremium, $currentPremium);

        // Submissions count
        $currentSubmissions = ClosedCall::whereBetween('created_at', [$start, $end]);
        if ($centerId) {
            $currentSubmissions->where('closed_calls.center_id', $centerId);
        }
        $submissionsCount = $currentSubmissions->count();

        // Status breakdown
        $statusBreakdownQuery = ClosedCall::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start, $end]);
        if ($centerId) {
            $statusBreakdownQuery->where('closed_calls.center_id', $centerId);
        }
        $statusBreakdown = $statusBreakdownQuery->groupBy('status')->get()->pluck('count', 'status')->toArray();

        // Team contribution breakdown
        // We join closer_team_members and closer_teams to get the team
        $teamContributionQuery = ClosedCall::leftJoin('closer_team_members', 'closed_calls.closer_id', '=', 'closer_team_members.user_id')
            ->leftJoin('closer_teams', 'closer_teams.id', '=', 'closer_team_members.closer_team_id')
            ->select(
                DB::raw('COALESCE(closer_teams.name, closed_calls.teamname, "No Team") as team_name'),
                DB::raw('COUNT(*) as total_deals'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END) as approved_deals'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN CAST(nullif(closed_calls.monthly_premium, "") AS DECIMAL(10,2)) ELSE 0 END) as approved_premium')
            )
            ->whereBetween('closed_calls.created_at', [$start, $end]);

        if ($centerId) {
            $teamContributionQuery->where('closed_calls.center_id', $centerId);
        }

        $teamContribution = $teamContributionQuery->groupBy('team_name')
            ->orderBy('approved_premium', 'desc')
            ->get();

        // Sales trend (Daily breakdown)
        $trendQuery = ClosedCall::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as submissions'),
            DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END) as sales'),
            DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN CAST(nullif(monthly_premium, "") AS DECIMAL(10,2)) ELSE 0 END) as premium')
        )
            ->whereBetween('created_at', [$start, $end]);

        if ($centerId) {
            $trendQuery->where('closed_calls.center_id', $centerId);
        }

        $trend = $trendQuery->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Outsourced sales summary
        $outsourceQuery = OutsourceClosedCall::whereBetween('created_at', [$start, $end]);
        if ($centerId) {
            $outsourceQuery->where('outsource_closed_calls.center_id', $centerId);
        }
        $outsourceSales = $outsourceQuery->whereIn('status', self::APPROVED_STATUSES)->count();
        $outsourcePremium = $outsourceQuery->whereIn('status', self::APPROVED_STATUSES)
            ->whereNotNull('monthly_premium')
            ->sum(DB::raw('CAST(nullif(monthly_premium, "") AS DECIMAL(10,2))'));

        return [
            'period' => $period,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_sales' => $currentSales,
            'total_premium' => round($currentPremium, 2),
            'sales_delta' => $salesDelta,
            'premium_delta' => $premiumDelta,
            'total_submissions' => $submissionsCount,
            'conversion_rate' => $submissionsCount > 0 ? round(($currentSales / $submissionsCount) * 100, 2) : 0,
            'status_breakdown' => $statusBreakdown,
            'team_contribution' => $teamContribution,
            'trend' => $trend,
            'outsourced' => [
                'sales' => $outsourceSales,
                'premium' => round($outsourcePremium, 2)
            ]
        ];
    }

    /**
     * 2. Team Performance Leaderboard
     */
    public function getTeamLeaderboard(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);

        // Leaderboard for Agent Teams (leads generated)
        $agentTeamsQuery = Team::leftJoin('avatar_leads', 'teams.id', '=', 'avatar_leads.team_id')
            ->select(
                'teams.id',
                'teams.name',
                DB::raw('COUNT(avatar_leads.id) as total_leads'),
                DB::raw('SUM(CASE WHEN avatar_leads.closer_status = "sale_made" THEN 1 ELSE 0 END) as sales_made'),
                DB::raw('SUM(CASE WHEN avatar_leads.QAstatus = "approved" THEN 1 ELSE 0 END) as qa_approved_leads')
            )
            ->whereBetween('avatar_leads.created_at', [$start, $end]);

        if ($centerId) {
            $agentTeamsQuery->where('teams.center_id', $centerId);
        }

        $agentTeams = $agentTeamsQuery->groupBy('teams.id', 'teams.name')
            ->orderBy('total_leads', 'desc')
            ->get()
            ->map(function ($team) {
                $team->conversion_rate = $team->total_leads > 0 ? round(($team->sales_made / $team->total_leads) * 100, 2) : 0;
                return $team;
            });

        // Leaderboard for Closer Teams (closed calls)
        $closerTeamsQuery = CloserTeam::leftJoin('closer_team_members', 'closer_teams.id', '=', 'closer_team_members.closer_team_id')
            ->leftJoin('closed_calls', 'closed_calls.closer_id', '=', 'closer_team_members.user_id')
            ->select(
                'closer_teams.id',
                'closer_teams.name',
                DB::raw('COUNT(closed_calls.id) as total_submissions'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END) as approved_deals'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN CAST(nullif(closed_calls.monthly_premium, "") AS DECIMAL(10,2)) ELSE 0 END) as approved_premium')
            )
            ->whereBetween('closed_calls.created_at', [$start, $end]);

        if ($centerId) {
            // Closer teams might not have center_id directly on closer_teams table, but we can query it via closed_calls center
            $closerTeamsQuery->where('closed_calls.center_id', $centerId);
        }

        $closerTeams = $closerTeamsQuery->groupBy('closer_teams.id', 'closer_teams.name')
            ->orderBy('approved_deals', 'desc')
            ->get()
            ->map(function ($team) {
                $team->approval_rate = $team->total_submissions > 0 ? round(($team->approved_deals / $team->total_submissions) * 100, 2) : 0;
                $team->approved_premium = round($team->approved_premium ?? 0, 2);
                return $team;
            });

        return [
            'agent_teams' => $agentTeams,
            'closer_teams' => $closerTeams
        ];
    }

    /**
     * 3. Agent & Closer Performance
     */
    public function getAgentPerformance(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);

        // Top Agents (Leads)
        $agentsQuery = AvatarLead::leftJoin('users', 'users.dialer_id', '=', 'avatar_leads.dialer_id')
            ->select(
                DB::raw('COALESCE(users.name, avatar_leads.agent_name, "Unknown Agent") as agent_name'),
                'avatar_leads.dialer_id',
                DB::raw('COUNT(*) as lead_count'),
                DB::raw('SUM(CASE WHEN avatar_leads.closer_status = "sale_made" THEN 1 ELSE 0 END) as sales_made'),
                DB::raw('SUM(CASE WHEN avatar_leads.QAstatus = "approved" THEN 1 ELSE 0 END) as approved_leads')
            )
            ->whereBetween('avatar_leads.created_at', [$start, $end]);

        if ($centerId) {
            $agentsQuery->where('avatar_leads.center_id', $centerId);
        }

        $topAgents = $agentsQuery->groupBy('avatar_leads.dialer_id', 'agent_name')
            ->orderBy('lead_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($agent) {
                $agent->conversion_rate = $agent->lead_count > 0 ? round(($agent->sales_made / $agent->lead_count) * 100, 2) : 0;
                return $agent;
            });

        // Top Closers (Deals)
        $closersQuery = ClosedCall::leftJoin('users', 'users.id', '=', 'closed_calls.closer_id')
            ->select(
                DB::raw('COALESCE(users.name, closed_calls.closername, "Unknown Closer") as closer_name'),
                'closed_calls.closer_id',
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END) as approved_deals'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN CAST(nullif(closed_calls.monthly_premium, "") AS DECIMAL(10,2)) ELSE 0 END) as approved_premium')
            )
            ->whereBetween('closed_calls.created_at', [$start, $end]);

        if ($centerId) {
            $closersQuery->where('closed_calls.center_id', $centerId);
        }

        $topClosers = $closersQuery->groupBy('closed_calls.closer_id', 'closer_name')
            ->orderBy('approved_deals', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($closer) {
                $closer->approval_rate = $closer->total_submissions > 0 ? round(($closer->approved_deals / $closer->total_submissions) * 100, 2) : 0;
                $closer->approved_premium = round($closer->approved_premium ?? 0, 2);
                return $closer;
            });

        // Talk Time Leaders (from reporting_data)
        $talktimeQuery = ReportingData::join('employees', 'employees.employee_id', '=', 'reporting_data.employee_id')
            ->join('users', 'users.id', '=', 'employees.user_id')
            ->select(
                'users.name as employee_name',
                'reporting_data.employee_id',
                DB::raw('SUM(reporting_data.talktime_seconds) as total_talktime'),
                DB::raw('SUM(reporting_data.total_submitted_sales) as submitted_sales'),
                DB::raw('SUM(reporting_data.total_approved) as approved_sales')
            )
            ->whereBetween('reporting_data.report_date', [$start->toDateString(), $end->toDateString()]);

        if ($centerId) {
            $talktimeQuery->where('users.center_id', $centerId);
        }

        $talktimeLeaders = $talktimeQuery->groupBy('reporting_data.employee_id', 'employee_name')
            ->orderBy('total_talktime', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $hours = floor($row->total_talktime / 3600);
                $minutes = floor(($row->total_talktime % 3600) / 60);
                $row->talktime_formatted = sprintf('%02dh %02dm', $hours, $minutes);
                return $row;
            });

        return [
            'top_agents' => $topAgents,
            'top_closers' => $topClosers,
            'talktime_leaders' => $talktimeLeaders
        ];
    }

    /**
     * 4. Attendance Overview
     */
    public function getAttendanceOverview(?string $date = null, ?int $centerId = null): array
    {
        $tz = 'America/New_York';
        $targetDate = $date ? Carbon::parse($date, $tz)->toDateString() : Carbon::now($tz)->toDateString();

        $start = Carbon::parse($targetDate, $tz)->startOfDay();
        $end = Carbon::parse($targetDate, $tz)->endOfDay();

        // Get company settings for late marks
        $settings = Utility::settings();
        $companyStartTimeStr = $settings['company_start_time'] ?? '19:45';
        $companyStartTime = Carbon::createFromTimeString($companyStartTimeStr);

        // Query active users that we expect to be present (excluding external types client/client partner/super admin)
        $expectedUsersQuery = User::where('is_active', 1)
            ->whereNotIn('type', ['super admin', 'client', 'company']);

        if ($centerId) {
            $expectedUsersQuery->where('users.center_id', $centerId);
        }

        $totalExpected = $expectedUsersQuery->count();
        $expectedUserIds = $expectedUsersQuery->pluck('id')->toArray();

        // Get attendance records for these users
        // Since attendance employee_no maps to Employee employee_id, let's query it
        $attendanceRecords = HikVisionAttendance::join('employees', 'employees.employee_id', '=', 'hik_vision_attendance.employee_no')
            ->join('users', 'users.id', '=', 'employees.user_id')
            ->select(
                'users.id as user_id',
                'users.name as employee_name',
                'employees.employee_id as employee_no',
                'hik_vision_attendance.status',
                'hik_vision_attendance.event_time'
            )
            ->whereBetween('hik_vision_attendance.event_time', [$start, $end])
            ->whereIn('users.id', $expectedUserIds)
            ->orderBy('hik_vision_attendance.event_time', 'asc')
            ->get();

        // Group by user
        $grouped = $attendanceRecords->groupBy('user_id');

        $presentCount = $grouped->count();
        $absentCount = max(0, $totalExpected - $presentCount);

        $lateCount = 0;
        $presentList = [];

        foreach ($grouped as $userId => $records) {
            // Find check in and check out
            $checkIn = $records->first(fn($r) => strcasecmp(trim($r->status), 'CheckIn') === 0);
            $checkOut = $records->sortByDesc('event_time')->first(fn($r) => strcasecmp(trim($r->status), 'CheckOut') === 0);

            $isLate = false;
            $lateMinutes = 0;

            if ($checkIn) {
                $checkInTime = Carbon::parse($checkIn->event_time);
                $expectedStart = Carbon::parse($targetDate)->setTimeFromTimeString($companyStartTime->format('H:i'));

                if ($checkInTime->gt($expectedStart)) {
                    $lateMinutes = $expectedStart->diffInMinutes($checkInTime);
                    // Standard threshold from controller is 5 minutes
                    if ($lateMinutes > 5) {
                        $isLate = true;
                        $lateCount++;
                    }
                }
            }

            $presentList[] = [
                'user_id' => $userId,
                'name' => $records->first()->employee_name,
                'employee_no' => $records->first()->employee_no,
                'check_in' => $checkIn ? Carbon::parse($checkIn->event_time)->format('h:i A') : 'N/A',
                'check_out' => $checkOut ? Carbon::parse($checkOut->event_time)->format('h:i A') : 'N/A',
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'late_formatted' => $isLate ? sprintf('%d mins', $lateMinutes) : 'On Time'
            ];
        }

        // Get absent list
        $presentUserIds = $grouped->keys()->toArray();
        $absentUsers = User::whereIn('id', $expectedUserIds)
            ->whereNotIn('id', $presentUserIds)
            ->select('id', 'name', 'type')
            ->get();

        return [
            'date' => $targetDate,
            'total_expected' => $totalExpected,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'attendance_rate' => $totalExpected > 0 ? round(($presentCount / $totalExpected) * 100, 2) : 0,
            'present_list' => $presentList,
            'absent_list' => $absentUsers
        ];
    }

    /**
     * 5. Retention & Quality Metrics
     */
    public function getRetentionMetrics(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);

        // Churn count: status change logs to charged_backed during the period
        $churnQuery = StatusChangeLog::where('new_status', 'charged_backed')
            ->whereBetween('changed_at', [$start, $end]);

        if ($centerId) {
            $churnQuery->join('closed_calls', 'closed_calls.id', '=', 'status_change_logs.closed_call_id')
                ->where('closed_calls.center_id', $centerId);
        }

        $churnCount = $churnQuery->count();

        // Funded/Approved count in period (as base for churn rate)
        $approvedBaseQuery = ClosedCall::whereIn('status', self::APPROVED_STATUSES)
            ->whereBetween('created_at', [$start, $end]);

        if ($centerId) {
            $approvedBaseQuery->where('closed_calls.center_id', $centerId);
        }

        $approvedBaseCount = $approvedBaseQuery->count();

        // Churn Rate
        $churnRate = $approvedBaseCount > 0 ? round(($churnCount / $approvedBaseCount) * 100, 2) : 0;

        // Save count: transitions from lapse/cancelled to approved/funded
        $savesQuery = StatusChangeLog::whereIn('old_status', ['lapse', 'cancelled', 'charged_backed', 'Potential Lapsed', 'NSF', 'Cancelled'])
            ->whereIn('new_status', ['approved', 'funded', 'Approved', 'Funded'])
            ->whereBetween('changed_at', [$start, $end]);

        if ($centerId) {
            $savesQuery->join('closed_calls', 'closed_calls.id', '=', 'status_change_logs.closed_call_id')
                ->where('closed_calls.center_id', $centerId);
        }

        $savesCount = $savesQuery->count();

        // Average QA Monitoring Scores
        $agentQaQuery = AvatarMonitoring::whereBetween('monitor_date', [$start->toDateString(), $end->toDateString()]);
        $closerQaQuery = Monitoring::whereBetween('monitor_date', [$start->toDateString(), $end->toDateString()]);

        if ($centerId) {
            $agentQaQuery->join('users', 'users.id', '=', 'avatar_monitoring.employee_id')
                ->where('users.center_id', $centerId);

            $closerQaQuery->join('users', 'users.id', '=', 'monitoring.employee_id')
                ->where('users.center_id', $centerId);
        }

        $agentQaAvg = $agentQaQuery->avg('score');
        $closerQaAvg = $closerQaQuery->avg('score');

        // QA scores dropdown/anomalies check
        return [
            'churn_count' => $churnCount,
            'approved_base_count' => $approvedBaseCount,
            'churn_rate' => $churnRate,
            'saves_count' => $savesCount,
            'agent_qa_avg' => round($agentQaAvg ?? 0, 2),
            'closer_qa_avg' => round($closerQaAvg ?? 0, 2),
        ];
    }

    /**
     * 6. Alerts & Flags
     */
    public function getAlerts(?int $centerId = null): array
    {
        $tz = 'America/New_York';
        $today = Carbon::now($tz);
        $sevenDaysAgo = $today->copy()->subDays(7);

        $alerts = [];

        // Alert A: High Chargeback Alert (in last 7 days)
        $chargebacksQuery = StatusChangeLog::where('new_status', 'charged_backed')
            ->whereBetween('changed_at', [$sevenDaysAgo, $today]);

        if ($centerId) {
            $chargebacksQuery->join('closed_calls', 'closed_calls.id', '=', 'status_change_logs.closed_call_id')
                ->where('closed_calls.center_id', $centerId);
        }

        $chargebacks = $chargebacksQuery->count();
        if ($chargebacks >= 5) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'High Chargebacks Detected',
                'message' => "There have been {$chargebacks} chargebacks logged in the last 7 days.",
                'details' => "Threshold is 5."
            ];
        }

        // Alert B: Low Attendance Alert
        $attendance = $this->getAttendanceOverview(null, $centerId);
        if ($attendance['total_expected'] > 0 && $attendance['attendance_rate'] < 70) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Attendance Rate',
                'message' => "Today's attendance rate is {$attendance['attendance_rate']}%. Expected at least 70%.",
                'details' => "Present: {$attendance['present_count']} / Expected: {$attendance['total_expected']}"
            ];
        }

        // Alert C: Agent Idle Alert (agents with 0 leads today)
        $activeAgentsQuery = User::where('is_active', 1)
            ->where('type', 'Agent');

        if ($centerId) {
            $activeAgentsQuery->where('users.center_id', $centerId);
        }

        $activeAgentsCount = $activeAgentsQuery->count();
        if ($activeAgentsCount > 0) {
            $activeAgentDialerIds = $activeAgentsQuery->pluck('dialer_id')->filter()->toArray();

            $activeLeadsCount = AvatarLead::whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->whereIn('dialer_id', $activeAgentDialerIds)
                ->groupBy('dialer_id')
                ->pluck('dialer_id')
                ->toArray();

            $idleAgentsCount = count($activeAgentDialerIds) - count($activeLeadsCount);
            if ($idleAgentsCount > 0 && count($activeAgentDialerIds) > 0) {
                $idlePercentage = round(($idleAgentsCount / count($activeAgentDialerIds)) * 100);
                if ($idlePercentage > 30) {
                    $alerts[] = [
                        'type' => 'warning',
                        'title' => 'High Idle Agent count',
                        'message' => "{$idleAgentsCount} active agents ({$idlePercentage}%) have generated 0 leads today.",
                        'details' => "Ensure dialer connection is working."
                    ];
                }
            }
        }

        // Alert D: Pending Sales Queue Alert (pending sales > 24h)
        $oneDayAgo = $today->copy()->subHours(24);
        $pendingSalesQuery = ClosedCall::where('status', 'pending')
            ->where('created_at', '<', $oneDayAgo);

        if ($centerId) {
            $pendingSalesQuery->where('closed_calls.center_id', $centerId);
        }

        $pendingSales = $pendingSalesQuery->count();
        if ($pendingSales > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Stale Pending Sales',
                'message' => "There are {$pendingSales} sales in the pending queue older than 24 hours.",
                'details' => "Needs verification approval."
            ];
        }

        // Alert E: QA Average Score Drop (Average < 75%)
        $retention = $this->getRetentionMetrics('this_month', null, null, $centerId);
        if ($retention['agent_qa_avg'] > 0 && $retention['agent_qa_avg'] < 75) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Low Agent QA Score',
                'message' => "Average Agent QA score this month is {$retention['agent_qa_avg']}%.",
                'details' => "Goal is > 80%."
            ];
        }

        return $alerts;
    }

    /**
     * 7. Closer Stats — tabular data for every user with type = Closer
     *
     * Returns: closer name, total submissions, individual status counts, monthly premium sum.
     */
    public function getCloserStats(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);

        $query = ClosedCall::leftJoin('users', 'users.id', '=', 'closed_calls.closer_id')
            ->select(
                'closed_calls.closer_id',
                DB::raw('COALESCE(users.name, closed_calls.closername, "Unknown Closer") as closer_name'),
                DB::raw('COUNT(*) as total_calls'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "pending" THEN 1 ELSE 0 END) as status_pending'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("Approved", "approved") THEN 1 ELSE 0 END) as status_approved'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("Cancelled", "cancelled") THEN 1 ELSE 0 END) as status_cancelled'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "NSF" THEN 1 ELSE 0 END) as status_nsf'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "DNC" THEN 1 ELSE 0 END) as status_dnc'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "Underwriting" THEN 1 ELSE 0 END) as status_underwriting'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "Need to Reach" THEN 1 ELSE 0 END) as status_need_to_reach'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("rejected", "Rejected") THEN 1 ELSE 0 END) as status_rejected'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("funded", "Funded") THEN 1 ELSE 0 END) as status_funded'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "charged_backed" THEN 1 ELSE 0 END) as status_charged_backed'),
                DB::raw('SUM(CASE WHEN closed_calls.status = "Potential Lapsed" THEN 1 ELSE 0 END) as status_potential_lapsed'),
                DB::raw('SUM(CASE WHEN closed_calls.status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN CAST(NULLIF(closed_calls.monthly_premium, "") AS DECIMAL(10,2)) ELSE 0 END) as total_premium')
            )
            ->whereBetween('closed_calls.created_at', [$start, $end]);

        if ($centerId) {
            $query->where('closed_calls.center_id', $centerId);
        }

        $rows = $query->groupBy('closed_calls.closer_id', 'closer_name')
            ->orderBy('status_approved', 'desc')
            ->get()
            ->map(function ($row) {
                $row->total_calls            = (int) $row->total_calls;
                $row->status_pending         = (int) $row->status_pending;
                $row->status_approved        = (int) $row->status_approved;
                $row->status_cancelled       = (int) $row->status_cancelled;
                $row->status_nsf             = (int) $row->status_nsf;
                $row->status_dnc             = (int) $row->status_dnc;
                $row->status_underwriting    = (int) $row->status_underwriting;
                $row->status_need_to_reach   = (int) $row->status_need_to_reach;
                $row->status_rejected        = (int) $row->status_rejected;
                $row->status_funded          = (int) $row->status_funded;
                $row->status_charged_backed  = (int) $row->status_charged_backed;
                $row->status_potential_lapsed = (int) $row->status_potential_lapsed;
                $row->total_premium          = round((float) ($row->total_premium ?? 0), 2);
                return $row;
            });

        return $rows->toArray();
    }

    /**
     * 8. Leads Summary — get statistics from avatar_leads grouped by QAstatus and split by users.is_wfh.
     *
     * Returns: status breakdown (status, wfh count, onsite count, total count) and totals.
     */
    public function getLeadsSummary(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);

        $results = \Illuminate\Support\Facades\DB::table('avatar_leads')
            ->leftJoin('users', 'users.dialer_id', '=', 'avatar_leads.dialer_id')
            ->select(
                'avatar_leads.QAstatus',
                DB::raw('COUNT(CASE WHEN users.is_wfh = 1 THEN 1 END) as wfh_count'),
                DB::raw('COUNT(CASE WHEN users.is_wfh = 0 OR users.is_wfh IS NULL THEN 1 END) as onsite_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->whereBetween('avatar_leads.created_at', [$start, $end]);

        if ($centerId) {
            $results->where('avatar_leads.center_id', $centerId);
        }

        $results = $results->groupBy('avatar_leads.QAstatus')->get();

        $rows = $results->map(function ($row) {
            return [
                'status' => $row->QAstatus ?: 'unassigned',
                'wfh' => (int) $row->wfh_count,
                'onsite' => (int) $row->onsite_count,
                'total' => (int) $row->total_count,
            ];
        })->toArray();

        // Calculate totals
        $totalWfh = 0;
        $totalOnsite = 0;
        $totalLeads = 0;

        // Sum approved and rejected for card statistics
        $approvedWfh = 0;
        $approvedOnsite = 0;
        $approvedTotal = 0;

        $rejectedWfh = 0;
        $rejectedOnsite = 0;
        $rejectedTotal = 0;

        foreach ($rows as $row) {
            $totalWfh += $row['wfh'];
            $totalOnsite += $row['onsite'];
            $totalLeads += $row['total'];

            $status = strtolower($row['status']);
            if ($status === 'approved') {
                $approvedWfh += $row['wfh'];
                $approvedOnsite += $row['onsite'];
                $approvedTotal += $row['total'];
            } elseif ($status === 'rejected') {
                $rejectedWfh += $row['wfh'];
                $rejectedOnsite += $row['onsite'];
                $rejectedTotal += $row['total'];
            }
        }

        return [
            'breakdown' => $rows,
            'totals' => [
                'wfh' => $totalWfh,
                'onsite' => $totalOnsite,
                'total' => $totalLeads,
            ],
            'approved' => [
                'wfh' => $approvedWfh,
                'onsite' => $approvedOnsite,
                'total' => $approvedTotal,
            ],
            'rejected' => [
                'wfh' => $rejectedWfh,
                'onsite' => $rejectedOnsite,
                'total' => $rejectedTotal,
            ]
        ];
    }

    /**
     * 9. Weekly Agent Performance Report — aggregates statistics from avatar_leads table.
     *
     * Returns: agent-wise performance details.
     */
    public function getWeeklyAgentPerformanceReport(string $period, ?string $startDate = null, ?string $endDate = null, ?int $centerId = null): array
    {
        list($start, $end) = $this->getDatesForPeriod($period, $startDate, $endDate);

        $query = \Illuminate\Support\Facades\DB::table('avatar_leads')
            ->leftJoin('users', 'users.dialer_id', '=', 'avatar_leads.dialer_id')
            ->select(
                DB::raw('COALESCE(users.name, avatar_leads.agent_name, "Unknown Agent") as agent_name'),
                'avatar_leads.dialer_id',
                DB::raw('COUNT(DISTINCT DATE(avatar_leads.created_at)) as working_days'),
                DB::raw('COUNT(avatar_leads.id) as total_transfers'),
                DB::raw('SUM(CASE WHEN avatar_leads.QAstatus = "approved" THEN 1 ELSE 0 END) as approved_transfers'),
                DB::raw('SUM(CASE WHEN avatar_leads.QAstatus = "rejected" THEN 1 ELSE 0 END) as rejected_transfers'),
                DB::raw('SUM(CASE WHEN avatar_leads.closer_status = "sale_made" THEN 1 ELSE 0 END) as sales')
            )
            ->whereBetween('avatar_leads.created_at', [$start, $end]);

        if ($centerId) {
            $query->where('avatar_leads.center_id', $centerId);
        }

        $results = $query->groupBy('avatar_leads.dialer_id', 'agent_name')
            ->orderBy('approved_transfers', 'desc')
            ->get();

        return $results->map(function ($row) {
            return [
                'agent_name' => $row->agent_name,
                'dialer_id' => $row->dialer_id,
                'working_days' => (int) $row->working_days,
                'total_transfers' => (int) $row->total_transfers,
                'approved_transfers' => (int) $row->approved_transfers,
                'rejected_transfers' => (int) $row->rejected_transfers,
                'sales' => (int) $row->sales,
            ];
        })->toArray();
    }
}

