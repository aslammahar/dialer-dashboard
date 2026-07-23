<?php

namespace App\Services;

use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaderboardService
{
    public function getMonthlyLeaderboard()
    {
        // Define Mountain Time Zone
        $mountainTime = Carbon::now('America/Denver');

        // Define the start and end dates for the date range
        $startDate = $mountainTime->copy()->subMonth()->startOfMonth()->addDays(10);
        $endDate = $mountainTime->copy()->startOfMonth()->addDays(9);

        // Fetch all teams with leader and agents
        $teams = Team::with('leader', 'agents')->get();

        // Query to fetch the total number of leads for each user from both tables within the date range and the total count of sales
        $mergedLeadsCount = User::leftJoinSub(
            DB::table('leads')
                ->select('user_id', DB::raw('COUNT(*) as lead_count'))
                ->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('user_id'),
            'leads_counts',
            function ($join) {
                $join->on('users.id', '=', 'leads_counts.user_id');
            }
        )
            ->leftJoinSub(
                DB::table('avatar_leads')
                    ->select(
                        'dialer_id',
                        'QAstatus', // Include QAstatus column
                        DB::raw('COUNT(*) as lead_count'),
                        DB::raw('SUM(CASE WHEN closer_status = "sale_made" THEN 1 ELSE 0 END) as sale_made_count'),
                        DB::raw('SUM(count) as total_count'),
                        DB::raw('SUM(CASE WHEN QAstatus = "pending" THEN 1 ELSE 0 END) as pending_count'),
                        DB::raw('SUM(CASE WHEN QAstatus = "approved" THEN 1 ELSE 0 END) as approved_count'),
                        DB::raw('SUM(CASE WHEN QAstatus = "rejected" THEN 1 ELSE 0 END) as rejected_count')
                    )
                    ->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()])
                    ->groupBy('dialer_id', 'QAstatus'), // Group by QAstatus as well
                'avatar_leads_counts',
                function ($join) {
                    $join->on('users.dialer_id', '=', 'avatar_leads_counts.dialer_id');
                }
            )
            ->select(
                'users.id',
                'users.name',
                'avatar_leads_counts.QAstatus', // Select QAstatus column
                DB::raw('COALESCE(leads_counts.lead_count, 0) + COALESCE(avatar_leads_counts.lead_count, 0) as total_lead_count'),
                'avatar_leads_counts.sale_made_count',
                'avatar_leads_counts.total_count',
                'avatar_leads_counts.pending_count', // Select counts for each QAstatus
                'avatar_leads_counts.approved_count',
                'avatar_leads_counts.rejected_count'
            )
            ->get()
            ->sortByDesc('total_lead_count');

        return compact('teams', 'mergedLeadsCount');
    }
}
