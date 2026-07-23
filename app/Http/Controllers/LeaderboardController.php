<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\AvatarLead;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    /** Cache TTL in seconds (2 minutes). */
    private const LEADERBOARD_CACHE_TTL = 120;

    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    // Only use for testing purpose
    // public function show()
    // {
    //     $mountainTime = Carbon::now('America/Denver');

    //     $data = (function () use ($mountainTime) {
    //         $teams = Team::withoutGlobalScope('center')
    //             ->with([
    //                 'leader' => fn($q) => $q->withoutGlobalScope('center'),
    //                 'agents' => fn($q) => $q->withoutGlobalScope('center'),
    //             ])
    //             ->get();

    //         // subDay() testing ke liye - live pe hatao
    //         $dayStartUtc = $mountainTime->copy()->subDay()->startOfDay()->utc();
    //         $dayEndUtc   = $mountainTime->copy()->endOfDay()->utc();

    //         $avatarSubquery = AvatarLead::query()
    //             ->select(
    //                 'dialer_id',
    //                 DB::raw('COUNT(*) as lead_count'),
    //                 DB::raw('SUM(CASE WHEN closer_status = "sale_made" THEN 1 ELSE 0 END) as sale_made_count'),
    //                 DB::raw('SUM(count) as total_count')
    //             )
    //             ->whereBetween('created_at', [$dayStartUtc, $dayEndUtc])
    //             ->groupBy('dialer_id');

    //         $mergedLeadsCount = User::query()
    //             ->withoutGlobalScope('center')
    //             // ->forCurrentCenter()
    //             ->leftJoinSub($avatarSubquery, 'avatar_leads_counts', function ($join) {
    //                 $join->on('users.dialer_id', '=', 'avatar_leads_counts.dialer_id');
    //             })
    //             ->select(
    //                 'users.id',
    //                 'users.name',
    //                 DB::raw('COALESCE(avatar_leads_counts.lead_count, 0) as total_lead_count'),
    //                 'avatar_leads_counts.sale_made_count',
    //                 'avatar_leads_counts.total_count'
    //             )
    //             ->get()
    //             ->sortByDesc('total_lead_count')
    //             ->values();

    //         return [
    //             'teams' => $teams,
    //             'mergedLeadsCount' => $mergedLeadsCount,
    //             'date' => $mountainTime->toDateString(),
    //         ];
    //     })();

    //     return view('leaderboard.leaderboard', $data);
    // }

public function show()
    {
        $mountainTime = Carbon::now('America/Denver');
        $cacheKey = $this->leaderboardCacheKey($mountainTime->toDateString());

        $data = Cache::remember($cacheKey, self::LEADERBOARD_CACHE_TTL, function () use ($mountainTime) {
            $teams = Team::with('leader', 'agents')->get();

            $avatarSubquery = AvatarLead::query()
                ->select(
                    'dialer_id',
                    DB::raw('COUNT(*) as lead_count'),
                    DB::raw('SUM(CASE WHEN closer_status = "sale_made" THEN 1 ELSE 0 END) as sale_made_count'),
                    DB::raw('SUM(count) as total_count')
                )
                ->whereDate('created_at', $mountainTime->toDateString())
                ->groupBy('dialer_id');

            $mergedLeadsCount = User::query()
                ->forCurrentCenter()
                ->leftJoinSub($avatarSubquery, 'avatar_leads_counts', function ($join) {
                    $join->on('users.dialer_id', '=', 'avatar_leads_counts.dialer_id');
                })
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('COALESCE(avatar_leads_counts.lead_count, 0) as total_lead_count'),
                    'avatar_leads_counts.sale_made_count',
                    'avatar_leads_counts.total_count'
                )
                ->get()
                ->sortByDesc('total_lead_count')
                ->values();

            return [
                'teams' => $teams,
                'mergedLeadsCount' => $mergedLeadsCount,
                'date' => $mountainTime->toDateString(),
            ];
        });

        return view('leaderboard.leaderboard', $data);
    }


    private function leaderboardCacheKey(string $date): string
    {
        $centerId = auth()->user()->center_id ?? 'guest';
        return 'leaderboard_daily_' . $centerId . '_' . $date;
    }

   // Team $team ki jagah $teamId lo, DB::table se update karo
public function updateHcOverride(Request $request, $teamId)
{
    $request->validate([
        'hc_override' => 'nullable|integer|min:1|max:500',
    ]);

    $team = Team::withoutGlobalScopes()->findOrFail($teamId);

    DB::table('teams')
        ->where('id', $team->id)
        ->update(['hc_override' => $request->filled('hc_override') ? (int)$request->hc_override : null, 'updated_at' => now()]);

    Cache::forget($this->leaderboardCacheKey(Carbon::now('America/Denver')->toDateString()));

    return back()->with('hc_success', 'Active HC updated for ' . $team->name . '!');
}

    public function showMonthly()
    {
        $data = $this->leaderboardService->getMonthlyLeaderboard();
        return view('leaderboard.monthly_stats_leaderboard', $data);
    }

    public function dailyLeads()
    {
        $leaderboard = DB::table('leads')
            ->select('user_id', DB::raw('count(*) as leads_count'))
            ->whereDate('created_at', today())
            ->groupBy('user_id')
            ->get();

        foreach ($leaderboard as $user) {
            $user->name = User::where('id', $user->user_id)->value('name');
        }

        return view('voice-section.daily_leads', [
            'sectionTitle' => 'Daily Leads',
            'leaderboard'  => $leaderboard,
        ]);
    }

    public function monthlyLeads()
    {
        $firstDayOfMonth = Carbon::today()->startOfMonth();
        $lastDayOfMonth  = Carbon::today()->endOfMonth();

        $leaderboard = DB::table('leads')
            ->select('user_id', DB::raw('count(*) as leads_count'))
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy('user_id')
            ->get();

        foreach ($leaderboard as $user) {
            $user->name = User::where('id', $user->user_id)->value('name');
        }

        return view('voice-section.monthly_leads', [
            'sectionTitle' => 'Monthly Leads',
            'leaderboard'  => $leaderboard,
        ]);
    }

    public function dailyAvatarLeads()
    {
        $currentDate = Carbon::now()->setTimezone('America/Denver')->toDateString();

        $leaderboard = DB::table('avatar_leads')
            ->select('dialer_id', DB::raw('count(*) as leads_count'))
            ->whereDate('created_at', $currentDate)
            ->groupBy('dialer_id')
            ->get();

        foreach ($leaderboard as $user) {
            $user->name = User::where('dialer_id', $user->dialer_id)->value('name');
        }

        return view('avatar.daily_leaderboard', [
            'sectionTitle' => 'Daily Avatar Leads',
            'leaderboard'  => $leaderboard,
        ]);
    }

    public function monthlyAvatarLeads()
    {
        $firstDayOfMonth = Carbon::today()->startOfMonth();
        $lastDayOfMonth  = Carbon::today()->endOfMonth();

        $leaderboard = DB::table('avatar_leads')
            ->select('dialer_id', DB::raw('count(*) as leads_count'))
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy('dialer_id')
            ->get();

        foreach ($leaderboard as $user) {
            $user->name = User::where('dialer_id', $user->dialer_id)->value('name');
        }

        return view('avatar.monthly_leaderboard', [
            'sectionTitle' => 'Monthly Avatar Leads',
            'leaderboard'  => $leaderboard,
        ]);
    }
}