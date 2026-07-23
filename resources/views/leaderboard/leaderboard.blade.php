@extends('layouts.admin')

@section('page-title')
{{ __('Leaderboard') }}
@endsection

@php
    use Carbon\Carbon;

    $totalAgentCountAllTeams = 0;
    $totalLeadCountAllTeams = 0;
    $totalSaleMadeCountAllTeams = 0;

    $teamsInfo = [];
    $maxAttemptsPerAgent = 0;
    $teamOfTheDay = '';

    foreach ($teams as $team) {
        $totalAgentCount = count($team->agents);
        $totalLeadCount = 0;
        foreach ($team->agents as $agent) {
            $agentData = $mergedLeadsCount->where('id', $agent->id)->first();
            $totalLeadCount += $agentData->total_lead_count ?? 0;
        }
        $effectiveHC = $team->hc_override ?? $totalAgentCount;
        $attemptsPerAgent = $effectiveHC > 0 ? $totalLeadCount / $effectiveHC : 0;
        $formattedAttempts = number_format($attemptsPerAgent, 1);
        $teamsInfo[] = [
            'name'             => $team->name,
            'attemptsPerAgent' => $formattedAttempts,
            'totalLeadCount'   => $totalLeadCount,
            'totalAgentCount'  => $totalAgentCount,
            'effectiveHC'      => $effectiveHC,
        ];
        if ($attemptsPerAgent > $maxAttemptsPerAgent) {
            $maxAttemptsPerAgent = $attemptsPerAgent;
            $teamOfTheDay = $team->name;
        }
    }

    usort($teamsInfo, fn($a, $b) => (float)$b['attemptsPerAgent'] <=> (float)$a['attemptsPerAgent']);
    foreach ($teamsInfo as &$t) {
        $t['leadsNeeded'] = (int) ceil(max(0, ($maxAttemptsPerAgent - (float)$t['attemptsPerAgent']) * $t['effectiveHC']));
    }
    unset($t);

    function lbLeadColor($leadCount) {
        if ($leadCount == 0)  return '#dc2626';
        if ($leadCount <= 3)  return '#f87171';
        if ($leadCount <= 6)  return '#fbbf24';
        if ($leadCount <= 9)  return '#facc15';
        if ($leadCount <= 15) return '#a3e635';
        if ($leadCount <= 20) return '#4ade80';
        if ($leadCount <= 25) return '#22c55e';
        if ($leadCount <= 30) return '#2dd4bf';
        if ($leadCount <= 35) return '#22d3ee';
        return '#0ea5e9';
    }
    function lbSaleColor($saleMadeCount) {
        if ($saleMadeCount == 0)  return '#e11d48';
        if ($saleMadeCount == 1)  return '#86efac';
        if ($saleMadeCount == 2)  return '#22c55e';
        if ($saleMadeCount == 3)  return '#16a34a';
        if ($saleMadeCount == 4)  return '#14b8a6';
        if ($saleMadeCount == 5)  return '#0d9488';
        if ($saleMadeCount <= 8)  return '#3b82f6';
        if ($saleMadeCount <= 10) return '#8b5cf6';
        if ($saleMadeCount <= 15) return '#7c3aed';
        if ($saleMadeCount <= 20) return '#a855f7';
        if ($saleMadeCount <= 25) return '#d946ef';
        if ($saleMadeCount <= 30) return '#ec4899';
        if ($saleMadeCount <= 35) return '#f97316';
        return '#eab308';
    }
    function lbAttemptColor($attemptsPerAgent) {
        $v = (float) $attemptsPerAgent;
        if ($v == 0)   return '#dc2626';
        if ($v <= 3)   return '#86efac';
        if ($v <= 6)   return '#22c55e';
        if ($v <= 9)   return '#16a34a';
        if ($v <= 12)  return '#14b8a6';
        if ($v <= 15)  return '#0d9488';
        if ($v <= 20)  return '#3b82f6';
        if ($v <= 25)  return '#8b5cf6';
        return '#7c3aed';
    }
@endphp

@section('content')
<style>
    .leaderboard-page {
        --lb-bg: #0f172a;
        --lb-surface: #1e293b;
        --lb-surface-hover: #334155;
        --lb-border: #334155;
        --lb-text: #f1f5f9;
        --lb-muted: #94a3b8;
        --lb-accent: #38bdf8;
        --lb-radius: 10px;
        --lb-radius-sm: 6px;
        --lb-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.2), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--lb-text);
        max-width: 1600px;
        margin: 0 auto;
        background: var(--lb-bg);
        padding: 1.25rem;
        border-radius: var(--lb-radius);
    }
    .leaderboard-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .leaderboard-title {
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: -0.02em;
        margin: 0;
        color: var(--lb-text);
    }
    .leaderboard-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .leaderboard-nav a {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.875rem;
        border-radius: var(--lb-radius-sm);
        font-size: 0.8125rem;
        font-weight: 500;
        text-decoration: none;
        background: var(--lb-surface);
        color: var(--lb-text);
        border: 1px solid var(--lb-border);
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }
    .leaderboard-nav a:hover {
        background: var(--lb-surface-hover);
        border-color: var(--lb-accent);
        color: var(--lb-accent);
    }
    .leaderboard-meta {
        font-size: 0.8125rem;
        color: var(--lb-muted);
        margin-top: 0.25rem;
    }
    .lb-card {
        background: var(--lb-surface);
        border-radius: var(--lb-radius);
        border: 1px solid var(--lb-border);
        box-shadow: var(--lb-shadow);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .lb-card-header {
        padding: 0.875rem 1.25rem;
        font-weight: 600;
        font-size: 0.9375rem;
        border-bottom: 1px solid var(--lb-border);
        background: rgba(0, 0, 0, 0.2);
        color: var(--lb-text);
    }
    .lb-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .lb-table th,
    .lb-table td {
        padding: 0.625rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--lb-border);
        color: var(--lb-text);
    }
    .lb-table th {
        font-weight: 600;
        color: var(--lb-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.6875rem;
    }
    .lb-table tbody tr:hover { background: var(--lb-surface-hover); }
    .lb-table .lb-num {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .lb-table .lb-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.8125rem;
    }
    /* Active HC read-only */
    .hc-cell {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 3px;
}
    .hc-readonly {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.8125rem;
        background: rgba(56,189,248,0.1);
        color: #38bdf8;
        border: 1px solid rgba(56,189,248,0.3);
        white-space: nowrap;
    }
    .hc-manual-tag {
        font-size: 0.6rem;
        color: #f59e0b;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
        background: rgba(245,158,11,0.1);
        border: 1px solid rgba(245,158,11,0.3);
        padding: 0.15rem 0.4rem;
        border-radius: 3px;
    }
    .agent-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-start;
    }
    .agent-team-card {
        flex: 1 1 320px;
        min-width: 280px;
        max-width: 420px;
        background: var(--lb-surface);
        border-radius: var(--lb-radius);
        border: 1px solid var(--lb-border);
        box-shadow: var(--lb-shadow);
        overflow: hidden;
    }
    .agent-team-card .card-title {
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 0.9375rem;
        border-bottom: 1px solid var(--lb-border);
        background: rgba(0, 0, 0, 0.2);
        color: var(--lb-text);
    }
    .agent-team-card .card-body { padding: 0.5rem 0; }
    .agent-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 0.5rem 1rem;
        padding: 0.5rem 1rem;
        align-items: center;
        font-size: 0.875rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        color: var(--lb-text);
    }
    .agent-row:last-child { border-bottom: none; }
    .agent-row .xfers,
    .agent-row .sold {
        min-width: 3rem;
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
    }
    .agent-row.tl-row {
        color: var(--lb-muted);
        font-size: 0.8125rem;
    }
    .totals-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 0.5rem 1rem;
        align-items: center;
        font-weight: 700;
        background: rgba(0,0,0,0.25);
        padding: 0.5rem 1rem;
        margin-top: 0.25rem;
        border-top: 1px solid var(--lb-border);
        font-size: 0.875rem;
        color: var(--lb-text);
    }
    .clock-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding: 0.625rem 1rem;
        background: var(--lb-surface);
        border-radius: var(--lb-radius-sm);
        border: 1px solid var(--lb-border);
        font-size: 0.8125rem;
        color: var(--lb-muted);
        margin-bottom: 1.25rem;
    }
    .clock-bar span { color: var(--lb-text); font-weight: 500; }
</style>

<div class="leaderboard-page">

    <div class="leaderboard-header">
        <div>
            <h1 class="leaderboard-title">{{ __('Leaderboard') }}</h1>
            <p class="leaderboard-meta">Stats for {{ isset($date) ? \Carbon\Carbon::parse($date)->format('M j, Y') : 'today' }} (MT) · Cached 2 min</p>
        </div>
        <nav class="leaderboard-nav">
            <a href="{{ route('leads.create') }}">{{ __('Create Lead') }}</a>
            <a href="{{ route('monthly-stats-leaderboard') }}">{{ __('Leaderboard Monthly') }}</a>
            <a href="{{ route('avatar-leaderboard-daily') }}">{{ __('Leaderboard Daily') }}</a>
            <a href="{{ url('avatar-section') }}">{{ __('Avatar Section') }}</a>
        </nav>
    </div>

    <div class="clock-bar">
        <div>Current: <span id="currentDateTime">—</span></div>
        <div>Time until EOD: <span id="remainingTime">—</span></div>
    </div>

    {{-- Team ranking --}}
    <div class="lb-card">
        <div class="lb-card-header">Team ranking (attempts per active HC)</div>
        <div style="padding: 0 1rem 1rem;">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Team</th>
                        <th class="lb-num">Xfers</th>
                        <th class="lb-num">Active HC</th>
                        <th class="lb-num">Attempts / Active HC</th>
                        <th class="lb-num">Needed to be #1</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teamsInfo as $i => $t)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $t['name'] }}</td>
                        <td class="lb-num">{{ number_format($t['totalLeadCount']) }}</td>
                        <td class="lb-num">
                            <div class="hc-cell">
                                <span class="hc-readonly">{{ $t['effectiveHC'] }}</span>
                            </div>
                        </td>
                        <td class="lb-num">{{ $t['attemptsPerAgent'] }}</td>
                        <td class="lb-num">{{ $t['leadsNeeded'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Teams overview --}}
    @php
        $totalAgentCountAllTeams = 0;
        $totalLeadCountAllTeams = 0;
        $totalSaleMadeCountAllTeams = 0;
    @endphp

    <div class="lb-card">
        <div class="lb-card-header">Teams overview</div>
        <div style="padding: 0 1rem 1rem;">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>Leader</th>
                        <th class="lb-num">HC</th>
                        <th class="lb-num">Active HC</th>
                        <th class="lb-num">Xfers</th>
                        <th class="lb-num">Sales</th>
                        <th class="lb-num">Attempts / Active HC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teams as $team)
                    @php
                        $totalAgentCount    = count($team->agents);
                        $totalLeadCount     = 0;
                        $totalSaleMadeCount = 0;
                        foreach ($team->agents as $agent) {
                            $d = $mergedLeadsCount->where('id', $agent->id)->first();
                            $totalLeadCount     += $d->total_lead_count ?? 0;
                            $totalSaleMadeCount += $d->total_count ?? 0;
                        }
                        $totalAgentCountAllTeams    += $totalAgentCount;
                        $totalLeadCountAllTeams     += $totalLeadCount;
                        $totalSaleMadeCountAllTeams += $totalSaleMadeCount;
                        $effectiveHC       = $team->hc_override ?? $totalAgentCount;
                        $attemptsPerAgent  = $effectiveHC > 0 ? $totalLeadCount / $effectiveHC : 0;
                        $formattedAttempts = number_format($attemptsPerAgent, 1);
                    @endphp
                    <tr>
                        <td>{{ $team->name }}</td>
                        <td>{{ $team->leader->name ?? '—' }} <small style="color:var(--lb-muted)">(TL)</small></td>
                        <td class="lb-num">{{ $totalAgentCount }}</td>
                        <td class="lb-num">
                            <div class="hc-cell">
                                <span class="hc-readonly">{{ $effectiveHC }}</span>
                                @if($team->hc_override)
                                    <span class="hc-manual-tag">manual</span>
                                @endif
                            </div>
                        </td>
                        <td class="lb-num">{{ $totalLeadCount }}</td>
                        <td class="lb-num">
                            <span class="lb-badge" style="background: {{ lbSaleColor($totalSaleMadeCount) }}20; color: {{ lbSaleColor($totalSaleMadeCount) }};">
                                {{ $totalSaleMadeCount }}
                            </span>
                        </td>
                        <td class="lb-num">
                            <span class="lb-badge" style="background: {{ lbAttemptColor($formattedAttempts) }}20; color: {{ lbAttemptColor($formattedAttempts) }};">
                                {{ $formattedAttempts }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: rgba(0,0,0,0.25); font-weight: 700;">
                        <td colspan="2">Totals</td>
                        <td class="lb-num">{{ $totalAgentCountAllTeams }}</td>
                        <td class="lb-num">—</td>
                        <td class="lb-num">{{ $totalLeadCountAllTeams }}</td>
                        <td class="lb-num">{{ $totalSaleMadeCountAllTeams }}</td>
                        <td class="lb-num">
                            @php
                                $avgAttempts = $totalAgentCountAllTeams > 0
                                    ? $totalLeadCountAllTeams / $totalAgentCountAllTeams
                                    : 0;
                            @endphp
                            {{ number_format($avgAttempts, 1) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Agents by team --}}
    <div class="lb-card">
        <div class="lb-card-header">Agents by team</div>
        <div style="padding: 1rem;">
            <div class="agent-cards">
                @foreach ($teams->sortByDesc(fn($t) => $t->agents->count()) as $team)
                @php
                    $agentsSorted = $team->agents->sortByDesc(function ($agent) use ($mergedLeadsCount) {
                        $d = $mergedLeadsCount->where('id', $agent->id)->first();
                        return $d->total_lead_count ?? 0;
                    });
                    $teamTotalXfers = 0;
                    $teamTotalSold  = 0;
                @endphp
                <div class="agent-team-card">
                    <div class="card-title">{{ $team->name }}</div>
                    <div class="card-body">
                        <div class="agent-row tl-row">
                            <span>{{ $team->leader->name ?? '—' }} (TL)</span>
                            <span></span>
                            <span></span>
                        </div>
                        @foreach ($agentsSorted as $agent)
                        @php
                            $agentData  = $mergedLeadsCount->where('id', $agent->id)->first();
                            $leadCount  = $agentData->total_lead_count ?? 0;
                            $saleCount  = $agentData->total_count ?? 0;
                            $teamTotalXfers += $leadCount;
                            $teamTotalSold  += $saleCount;
                        @endphp
                        <div class="agent-row">
                            <span>{{ $agent->name }}</span>
                            <span class="xfers" style="background: {{ lbLeadColor($leadCount) }}20; color: {{ lbLeadColor($leadCount) }};">{{ $leadCount }}</span>
                            <span class="sold"  style="background: {{ lbSaleColor($saleCount) }}20; color: {{ lbSaleColor($saleCount) }};">{{ $saleCount }}</span>
                        </div>
                        @endforeach
                        <div class="totals-row">
                            <span>Total</span>
                            <span class="lb-num">{{ $teamTotalXfers }}</span>
                            <span class="lb-num">{{ $teamTotalSold }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    function pad(n) { return n < 10 ? '0' + n : n; }
    function updateClocks() {
        var now = new Date();
        var end = new Date(now);
        end.setHours(18, 0, 0, 0);
        if (now > end) end.setDate(end.getDate() + 1);
        var rem = new Date(end - now);
        document.getElementById('currentDateTime').textContent = now.toLocaleString();
        document.getElementById('remainingTime').textContent =
            pad(rem.getHours()) + ':' + pad(rem.getMinutes()) + ':' + pad(rem.getSeconds());
    }
    updateClocks();
    setInterval(updateClocks, 1000);
})();
</script>

@endsection