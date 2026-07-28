@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Dialer Dashboard')

@push('css-page')
@include('dialer-dashboard._styles')
@endpush

@section('content')
<div class="dd-wrap">

    <div id="ddSaleCelebration">
        <div class="dd-lightbeams" id="ddSaleLights">
            <div class="dd-beam dd-beam-1"></div>
            <div class="dd-beam dd-beam-2"></div>
            <div class="dd-beam dd-beam-3"></div>
            <div class="dd-beam dd-beam-4"></div>
        </div>
        <div class="dd-fireworks" id="ddSaleFireworks"></div>
        <div class="dd-sale-bubbles" id="ddSaleBubbles"></div>
        <div class="dd-dancers" id="ddSaleDancers"></div>
        <div class="dd-sale-banner" id="ddSaleBanner">
            <div class="dd-sale-pill">
                <div class="dd-sale-kicker">New sale closed</div>
                <div class="dd-sale-name" id="ddSaleCloserName">—</div>
                <div class="dd-sale-sub">Keep it up 🔥</div>
            </div>
        </div>
    </div>


    <div class="dd-topbar">
    <div class="dd-brand">
        <div class="dd-brand-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 5a2 2 0 0 1 2-2h2.2a1 1 0 0 1 1 .8l1 4.6a1 1 0 0 1-.5 1.1l-1.9 1.1a13 13 0 0 0 6.6 6.6l1.1-1.9a1 1 0 0 1 1.1-.5l4.6 1a1 1 0 0 1 .8 1V19a2 2 0 0 1-2 2h-1C10.6 21 3 13.4 3 6V5Z"/>
            </svg>
        </div>
        <div>
            <div class="dd-brand-title">Dialer Dashboard</div>
            <div class="dd-brand-sub">Inbound V2 · Talk-time analytics</div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <div class="dd-nyclock" id="ddNyClock">
            <span class="dd-dot"></span>
            <span id="ddNyClockText">New York — --:--:-- </span>
        </div>
        @unless($isCloser)
    @if($canEdit)
        <a href="{{ route('daily-sales.create') }}" class="dd-update-btn" style="text-decoration:none">
            <i class="ti ti-edit"></i> Update Sales
        </a>
    @else
        <div class="dd-readonly-badge"><i class="ti ti-eye"></i> View only</div>
    @endif
@endunless
       @unless($isCloser)
<a href="{{ route('sales-target.edit') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-target"></i> Edit Target
</a>
@endunless
        <a href="{{ route('sales-reports.team-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-users"></i> Team Wise
</a>
<a href="{{ route('sales-reports.client-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-building"></i> Client Wise
</a>
<a href="{{ route('sales-reports.carrier-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-truck"></i> Carrier Wise
</a>
<a href="{{ route('dialer-leaderboard') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-list-numbers"></i> Leaderboard
</a>
@if($canEdit)
<a href="{{ route('attendance-closer.index') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-calendar-check"></i> Mark Attendance
</a>
@endif
        <div class="dd-sync">
            <span class="dd-dot"></span>
            Synced hourly · last sync {{ $lastSyncedAt ?? 'pending first run' }}
        </div>
    </div>
</div>

    <form class="dd-filters" method="GET" action="{{ route('dialer-dashboard') }}">
    @unless($isCloser)
    <div class="dd-field">
        <label class="dd-label">View</label>
        <select name="view" class="dd-select">
            <option value="active" {{ ($filters['view'] ?? 'active') == 'active' ? 'selected' : '' }}>Active agents</option>
            <option value="archive" {{ ($filters['view'] ?? '') == 'archive' ? 'selected' : '' }}>Archive</option>
        </select>
    </div>
    <div class="dd-field">
        <label class="dd-label">Group</label>
        <select name="group" class="dd-select">
            <option value="all" {{ ($filters['group'] ?? 'all') == 'all' ? 'selected' : '' }}>All groups</option>
            @foreach($groups as $group)
                <option value="{{ $group }}" {{ ($filters['group'] ?? '') == $group ? 'selected' : '' }}>{{ $group }}</option>
            @endforeach
        </select>
    </div>
    @endunless
        <div class="dd-field">
            <label class="dd-label">From</label>
            <input type="text" id="ddFromDate" name="from" class="dd-input" autocomplete="off" value="{{ $filters['from'] ?? now('America/New_York')->subDays(1)->toDateString() }}">
        </div>
        <div class="dd-field">
            <label class="dd-label">To</label>
            <input type="text" id="ddToDate" name="to" class="dd-input" autocomplete="off" value="{{ $filters['to'] ?? now('America/New_York')->toDateString() }}">
        </div>
        <div class="dd-presets">
            <span class="dd-chip">Today</span>
            <span class="dd-chip active">This week</span>
            <span class="dd-chip">This month</span>
        </div>
        <button type="submit" class="dd-apply">Apply</button>
    </form>
@if($goal['raw_target'] > 0)
    <div class="dd-track-card {{ $goal['pct'] >= 100 ? 'is-complete' : '' }}" id="ddTrackCard">
        <div class="dd-confetti" id="ddConfetti"></div>
        <div class="dd-track-head">
            <div>
                <div class="dd-track-eyebrow">This month's sales target</div>
                <div class="dd-track-title">Hit <b>{{ number_format($goal['raw_target']) }}</b> sales — {{ $goal['reward_headline'] }}</div>
            </div>
            <div class="dd-track-num">
                <div class="big" id="ddTrackCount" data-target="{{ $goal['approved_mtd'] }}">0</div>
                <div class="small">
                    of {{ number_format($goal['raw_target']) }} sales this month
                    &nbsp;·&nbsp; SPD: {{ $goal['current_spd'] }} / {{ $goal['monthly_spd_target'] }} target
                </div>
            </div>
        </div>

        <div class="dd-track">
            <div class="dd-track-line"></div>
            <div class="dd-track-fill" id="ddTrackFill" style="width: 0%" data-pct="{{ min($goal['pct'], 100) }}"></div>
            <div class="dd-track-shadow" id="ddTrackShadow" style="left: 0%"></div>
            <div class="dd-track-runner {{ $goal['pct'] < 100 ? 'flying' : '' }}" id="ddTrackRunner" style="left: 0%" data-pct="{{ min($goal['pct'], 100) }}">🚌</div>

            <div class="dd-milestone {{ $goal['pct'] >= 45 ? 'reached' : '' }}" style="left: 45%">
                <div class="dd-milestone-icon">🎬</div>
                <div class="dd-milestone-dot"></div>
                <div class="dd-milestone-label">{{ $goal['milestone_1_label'] }}</div>
            </div>
            <div class="dd-milestone {{ $goal['pct'] >= 75 ? 'reached' : '' }}" style="left: 75%">
                <div class="dd-milestone-icon">💰</div>
                <div class="dd-milestone-dot"></div>
                <div class="dd-milestone-label">{{ $goal['milestone_2_label'] }}{{ $goal['milestone_2_amount'] ? ' ('.$goal['milestone_2_amount'].')' : '' }}</div>
            </div>
            <div class="dd-milestone {{ $goal['pct'] >= 100 ? 'reached' : '' }}" style="left: 100%">
                <div class="dd-milestone-icon">🏆</div>
                <div class="dd-milestone-dot"></div>
                <div class="dd-milestone-label">{{ $goal['milestone_3_label'] }}</div>
            </div>
        </div>

        <div class="dd-track-foot">
            <div class="dd-track-msg">
                @if($goal['pct'] >= 100)
                    <b>Target hit</b> — trip is locked in for the whole team.
                @else
                    <b>{{ number_format($goal['raw_target'] - $goal['approved_mtd']) }}</b> sales to go — keep pushing.
                @endif
            </div>
            <span class="dd-badge-unlocked">🏆 {{ $goal['milestone_3_label'] }} unlocked</span>
        </div>
    </div>
@else
    <div class="dd-track-card" style="text-align:center;padding:24px">
        <div style="color:var(--dd-text-muted);font-size:13px;margin-bottom:12px">No sales target set for this month yet.</div>
        <a href="{{ route('sales-target.edit') }}" class="dd-apply" style="text-decoration:none;display:inline-block">Set Target</a>
    </div>
@endif
   <div class="dd-stats" style="grid-template-columns:repeat(4,1fr)">
    <div class="dd-stat-card">
        <div class="dd-stat-icon"><i class="ti ti-user-check"></i></div>
        <div class="dd-stat-label">Active Closers</div>
        <div class="dd-stat-value" id="ddActiveClosersVal">{{ $closerCounts['active'] }} <span style="font-size:14px;color:var(--dd-text-muted)">/ {{ $closerCounts['total'] }}</span></div>
        <div class="dd-stat-trend dd-trend-up">Present today</div>
    </div>
    <div class="dd-stat-card">
        <div class="dd-stat-icon"><i class="ti ti-trophy"></i></div>
        <div class="dd-stat-label">Today Total Sales</div>
        <div class="dd-stat-value" id="ddTotalSalesVal">{{ $dailyBoardTotals['approved'] }}</div>
        <div class="dd-stat-trend dd-trend-up">Approved sales</div>
    </div>
    <div class="dd-stat-card">
        <div class="dd-stat-icon"><i class="ti ti-clock"></i></div>
        <div class="dd-stat-label">Avg Talk Time (Active Closers)</div>
        <div class="dd-stat-value" id="ddAvgTalkVal">{{ $activeStats['avg_talk_time'] }}</div>
        <div class="dd-stat-trend dd-trend-up">Today, present closers only</div>
    </div>
    <div class="dd-stat-card">
        <div class="dd-stat-icon"><i class="ti ti-phone-outgoing"></i></div>
        <div class="dd-stat-label">Avg Calls per Sale</div>
        <div class="dd-stat-value" id="ddAvgCallsVal">{{ $dailyBoardTotals['approved'] > 0 ? round($activeStats['calls'] / $dailyBoardTotals['approved'], 1) : 0 }}</div>
        <div class="dd-stat-trend dd-trend-up" id="ddCallsSubVal">{{ $activeStats['calls'] }} calls today (active closers)</div>
    </div>
</div>

    <div class="dd-panel" style="margin-top:16px">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Today's Sales Board</div>
                <div class="dd-panel-sub">{{ now('America/New_York')->format('d M Y') }} (New York) — Approved vs Pending per closer</div>
            </div>
            {{-- <a href="{{ route('daily-sales.create') }}" class="dd-apply" style="text-decoration:none"> Update Sale</a> --}}
        </div>
        <div class="dd-lb-scroll">
    <table class="dd-lb-table" id="ddDailyBoardTable">
        <thead>
            <tr>
                <th>Time Since Last Sale</th>
                <th>Team</th>
                <th>Closer</th>
                <th>Approved</th>
                <th>Level</th>
                <th>GI</th>
                <th>Level %</th>
                <th>Avg Pre</th>
                <th>Calls</th>
                <th>Avg Talk Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyBoard as $row)
                <tr>
                    <td>{{ $row['time_since_last_sale'] ?? '-' }}</td>
                    <td><span class="dd-lb-team">{{ $row['team'] }}</span></td>
                    <td>{{ $row['closer'] }}</td>
                    <td>{{ $row['approved'] }}</td>
                    <td>{{ $row['level'] }}</td>
                    <td>{{ $row['gi'] }}</td>
                    <td>{{ $row['level_pct'] }}%</td>
                    <td>{{ $row['avg_pre'] }}</td>
                    <td>{{ $row['calls'] }}</td>
                    <td>{{ $row['avg_talk_time'] }}</td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;color:var(--dd-text-muted);padding:20px">No sales logged today yet.</td></tr>
            @endforelse
        </tbody>
       <tfoot>
    <tr>
        <td colspan="3">Total</td>
        <td>{{ $dailyBoardTotals['approved'] }}</td>
        <td>{{ $dailyBoardTotals['level'] }}</td>
        <td>{{ $dailyBoardTotals['gi'] }}</td>
        <td>{{ $dailyBoardTotals['level_pct'] }}%</td>
        <td>{{ $dailyBoardTotals['avg_pre'] }}</td>
        <td>{{ $dailyBoardTotals['calls'] }}</td>
        <td>{{ $dailyBoardTotals['avg_talk_time'] }}</td>
    </tr>
</tfoot>
    </table>
</div>
    </div>

   <div class="dd-panel" style="margin-top:16px">
    <div class="dd-panel-head">
        <div>
            <div class="dd-panel-title">Teams Summary</div>
            <div class="dd-panel-sub">Today — Approved / Level / GI / SPD / Target</div>
        </div>
        <a href="{{ route('sales-reports.team-wise') }}" class="dd-apply" style="text-decoration:none">Manage Teams</a>
    </div>
    <div class="dd-lb-scroll">
        <table class="dd-lb-table">
         <thead>
    <tr>
        <th>Last Sale</th><th>Team</th><th>Approved</th><th>Level</th><th>GI</th>
        <th>Level %</th><th>SPD</th><th>Avg Pre</th><th>Avg Talk Time</th>
        <th>Target</th><th>Left</th>
    </tr>
</thead>
<tbody>
    @forelse($teamsSummary as $t)
        <tr>
            <td>{{ $t['last_sale'] }}</td>
            <td>{{ $t['team'] }}</td>
            <td>{{ $t['approved'] }}</td>
            <td>{{ $t['level'] }}</td>
            <td>{{ $t['gi'] }}</td>
            <td>{{ $t['level_pct'] }}%</td>
            <td>{{ $t['spd'] }}</td>
            <td>{{ $t['avg_pre'] }}</td>
            <td>{{ $t['avg_talk_time'] }}</td>
            <td>{{ $t['target'] }}</td>
            <td>{{ $t['left'] }}</td>
        </tr>
    @empty
        <tr><td colspan="12" style="text-align:center;color:var(--dd-text-muted);padding:16px">No team data yet.</td></tr>
    @endforelse
</tbody>
@if(count($teamsSummary))
<tfoot>
    <tr>
        <td>Total / Avg</td>
        <td>-</td>
        <td>{{ $teamsSummaryTotals['approved'] }}</td>
        <td>{{ $teamsSummaryTotals['level'] }}</td>
        <td>{{ $teamsSummaryTotals['gi'] }}</td>
        <td>{{ $teamsSummaryTotals['level_pct'] }}%</td>
        <td>{{ $teamsSummaryTotals['spd'] }}</td>
        <td>{{ $teamsSummaryTotals['avg_pre'] }}</td>
        <td>{{ $teamsSummaryTotals['avg_talk_time'] }}</td>
        <td>{{ $teamsSummaryTotals['target'] }}</td>
        <td>{{ $teamsSummaryTotals['left'] }}</td>
    </tr>
</tfoot>
@endif
        </table>
    </div>
</div>

<div class="dd-panel" style="margin-top:16px">
    <div class="dd-panel-head">
        <div>
            <div class="dd-panel-title">Clients Summary</div>
            <div class="dd-panel-sub">Today — Approved / Level / GI / Target</div>
        </div>
        <a href="{{ route('sales-reports.client-wise') }}" class="dd-apply" style="text-decoration:none">View Full</a>
    </div>
    <div class="dd-lb-scroll">
        <table class="dd-lb-table" id="ddClientsTable">
            <thead>
                <tr>
                    <th>Last Sale</th><th>Client</th><th>Approved</th><th>Level</th><th>GI</th>
                    <th>Level %</th><th>Target</th><th>Left</th><th>Avg Pre</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientsSummary as $c)
                    <tr>
                        <td>{{ $c['last_sale'] }}</td>
                        <td>{{ $c['client'] }}</td>
                        <td>{{ $c['approved'] }}</td>
                        <td>{{ $c['level'] }}</td>
                        <td>{{ $c['gi'] }}</td>
                        <td>{{ $c['level_pct'] }}%</td>
                        <td>{{ $c['target'] }}</td>
                        <td>{{ $c['left'] }}</td>
                        <td>{{ $c['avg_pre'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;color:var(--dd-text-muted);padding:16px">No client data yet.</td></tr>
                @endforelse
            </tbody>
@if(count($clientsSummary))
<tfoot>
    @php
        $ctApproved = array_sum(array_column($clientsSummary, 'approved'));
        $ctLevel = array_sum(array_column($clientsSummary, 'level'));
    @endphp
    <tr>
        <td>Total</td>
        <td>-</td>
        <td>{{ $ctApproved }}</td>
        <td>{{ $ctLevel }}</td>
        <td>{{ array_sum(array_column($clientsSummary, 'gi')) }}</td>
        <td>{{ $ctApproved > 0 ? round(($ctLevel / $ctApproved) * 100) : 0 }}%</td>
        <td>{{ array_sum(array_column($clientsSummary, 'target')) }}</td>
        <td>{{ array_sum(array_column($clientsSummary, 'left')) }}</td>
        <td>{{ round(array_sum(array_column($clientsSummary, 'avg_pre')) / count($clientsSummary), 2) }}</td>
    </tr>
</tfoot>
@endif
        </table>
    </div>
</div>

<div class="dd-panel" style="margin-top:16px">
    <div class="dd-panel-head">
        <div>
            <div class="dd-panel-title">Carriers Summary</div>
            <div class="dd-panel-sub">Today — Approved / Level / GI / Target</div>
        </div>
        <a href="{{ route('sales-reports.carrier-wise') }}" class="dd-apply" style="text-decoration:none">View Full</a>
    </div>
    <div class="dd-lb-scroll">
        <table class="dd-lb-table" id="ddCarriersTable">
            <thead>
                <tr>
                    <th>Carrier</th><th>Approved</th><th>Level</th><th>GI</th>
                    <th>Level %</th><th>Target</th><th>Left</th><th>Avg Pre</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carriersSummary as $c)
                    <tr>
                        <td>{{ $c['carrier'] }}</td>
                        <td>{{ $c['approved'] }}</td>
                        <td>{{ $c['level'] }}</td>
                        <td>{{ $c['gi'] }}</td>
                        <td>{{ $c['level_pct'] }}%</td>
                        <td>{{ $c['target'] }}</td>
                        <td>{{ $c['left'] }}</td>
                        <td>{{ $c['avg_pre'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;color:var(--dd-text-muted);padding:16px">No carrier data yet.</td></tr>
                @endforelse
            </tbody>
@if(count($carriersSummary))
<tfoot>
    @php
        $ctApproved = array_sum(array_column($carriersSummary, 'approved'));
        $ctLevel = array_sum(array_column($carriersSummary, 'level'));
    @endphp
    <tr>
        <td>Total</td>
        <td>{{ $ctApproved }}</td>
        <td>{{ $ctLevel }}</td>
        <td>{{ array_sum(array_column($carriersSummary, 'gi')) }}</td>
        <td>{{ $ctApproved > 0 ? round(($ctLevel / $ctApproved) * 100) : 0 }}%</td>
        <td>{{ array_sum(array_column($carriersSummary, 'target')) }}</td>
        <td>{{ array_sum(array_column($carriersSummary, 'left')) }}</td>
        <td>{{ round(array_sum(array_column($carriersSummary, 'avg_pre')) / count($carriersSummary), 2) }}</td>
    </tr>
</tfoot>
@endif
        </table>
    </div>
</div>
    <div class="dd-grid">
        <div class="dd-panel">
    <div class="dd-panel-head">
        <div>
            <div class="dd-panel-title">Team Performance</div>
            <div class="dd-panel-sub">
                @if($teamPie['top_team'])
                    🏆 Top team this month: <b style="color:var(--dd-accent)">{{ $teamPie['top_team'] }}</b>
                @else
                    No sales recorded this month yet
                @endif
            </div>
        </div>
    </div>
    <div style="position:relative;height:280px">
        <canvas id="ddTeamPieChart" role="img" aria-label="Pie chart of team-wise sales performance"></canvas>
    </div>
</div>


<div class="dd-panel" style="margin-top:16px">
    <div class="dd-panel-head">
        <div>
            <div class="dd-panel-title">🏆 Monthly Performance Ranking</div>
            <div class="dd-panel-sub">Blended score: sales volume + conversion efficiency + level% — {{ now('America/New_York')->format('F Y') }}</div>
        </div>
    </div>
    <div class="dd-lb-scroll">
        <table class="dd-lb-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Closer</th>
                    <th>Team</th>
                    <th>Working Days</th>
                    <th>MTD</th>
                    <th>SPD</th>
                    <th>Level</th>
                    <th>GI</th>
                    <th>Level %</th>
                    <th>Avg Pre</th>
                    <th>Avatar/Jcs Calls</th>
                    <th>Conversion </th>
                    <th>Avg Talk Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyPerformance as $p)
                    <tr class="r{{ $p['rank'] <= 3 ? $p['rank'] : '' }}">
                        <td><span class="dd-rank-badge">{{ $p['rank'] }}</span></td>
                        <td>
                            <div class="dd-lb-agent">
                                <div class="dd-avatar">{{ strtoupper(substr($p['closer'], 0, 2)) }}</div>
                                <div class="dd-lb-name">{{ $p['closer'] }}</div>
                            </div>
                        </td>
                        <td><span class="dd-lb-team">{{ $p['team'] }}</span></td>
                        <td>{{ $p['working_days'] }}</td>
                        <td>{{ $p['mtd'] }}</td>
                        <td>{{ $p['spd'] }}</td>
                        <td>{{ $p['level'] }}</td>
                        <td>{{ $p['gi'] }}</td>
                        <td>{{ $p['level_pct'] }}%</td>
                        <td>{{ $p['avg_pre'] }}</td>
                        <td>{{ $p['calls'] }}</td>
                        <td style="color:#22c55e;font-weight:600">{{ $p['conversion'] }}</td>
                        <td>{{ $p['avg_talk_time'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" style="text-align:center;color:var(--dd-text-muted);padding:20px">
                            No sales data yet this month.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
    </div>

    <div class="dd-footnote">
        <span class="dd-dot-static"></span>
        Team, client &amp; carrier data updates live from our CRM. Full live call leaderboard is on its own
        <a href="{{ route('dialer-leaderboard') }}" style="color:var(--dd-accent)">Leaderboard</a> page.
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script>
(function(){
    if (typeof flatpickr === 'undefined') return;
    var fromEl = document.getElementById('ddFromDate');
    var toEl = document.getElementById('ddToDate');
    if (!fromEl || !toEl) return;

    var toPicker = flatpickr(toEl, { dateFormat: 'Y-m-d', maxDate: 'today' });
    var fromPicker = flatpickr(fromEl, {
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        onChange: function (selectedDates) {
            if (selectedDates[0]) toPicker.set('minDate', selectedDates[0]);
        }
    });
})();
</script>
<script>
// Live New York clock — dialer's real-time reference, shown top-right of the dashboard
(function(){
    var el = document.getElementById('ddNyClockText');
    if (!el) return;
    function tick(){
        var formatted = new Intl.DateTimeFormat('en-US', {
            timeZone: 'America/New_York',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            hour12: true, month: 'short', day: 'numeric'
        }).format(new Date());
        el.textContent = 'New York — ' + formatted;
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
<script>
(function(){
    var lastLatestId = null;
    var pollUrl = @json(route('dialer-dashboard.live-board'));
    var firstRun = true;

    function renderBoardRow(row) {
        return '<tr>' +
            '<td>' + (row.time_since_last_sale ?? '-') + '</td>' +
            '<td><span class="dd-lb-team">' + row.team + '</span></td>' +
            '<td>' + row.closer + '</td>' +
            '<td>' + row.approved + '</td>' +
            '<td>' + row.level + '</td>' +
            '<td>' + row.gi + '</td>' +
            '<td>' + row.level_pct + '%</td>' +
            '<td>' + row.avg_pre + '</td>' +
            '<td>' + row.calls + '</td>' +
            '<td>' + (row.avg_talk_time ?? '-') + '</td>' +
        '</tr>';
    }

    function renderClientRow(c) {
        return '<tr>' +
            '<td>' + c.last_sale + '</td>' +
            '<td>' + c.client + '</td>' +
            '<td>' + c.approved + '</td>' +
            '<td>' + c.level + '</td>' +
            '<td>' + c.gi + '</td>' +
            '<td>' + c.level_pct + '%</td>' +
            '<td>' + c.target + '</td>' +
            '<td>' + c.left + '</td>' +
            '<td>' + c.avg_pre + '</td>' +
        '</tr>';
    }

    function renderCarrierRow(c) {
        return '<tr>' +
            '<td>' + c.carrier + '</td>' +
            '<td>' + c.approved + '</td>' +
            '<td>' + c.level + '</td>' +
            '<td>' + c.gi + '</td>' +
            '<td>' + c.level_pct + '%</td>' +
            '<td>' + c.target + '</td>' +
            '<td>' + c.left + '</td>' +
            '<td>' + c.avg_pre + '</td>' +
        '</tr>';
    }

    function sum(arr, key) {
        return arr.reduce(function(s, r){ return s + (r[key] || 0); }, 0);
    }

    function poll() {
        fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
            .then(function(r){ return r.json(); })
            .then(function(data){
                // Today's Sales Board
                var table = document.getElementById('ddDailyBoardTable');
                if (table) {
                    var tbody = table.querySelector('tbody');
                    tbody.innerHTML = data.board.length === 0
                        ? '<tr><td colspan="10" style="text-align:center;color:var(--dd-text-muted);padding:20px">No sales logged today yet.</td></tr>'
                        : data.board.map(renderBoardRow).join('');

                    var tf = table.querySelector('tfoot tr');
                    if (tf) {
                        tf.innerHTML =
                            '<td colspan="3">Total</td>' +
                            '<td>' + data.totals.approved + '</td>' +
                            '<td>' + data.totals.level + '</td>' +
                            '<td>' + data.totals.gi + '</td>' +
                            '<td>' + data.totals.level_pct + '%</td>' +
                            '<td>' + data.totals.avg_pre + '</td>' +
                            '<td>' + data.totals.calls + '</td>' +
                            '<td>' + data.totals.avg_talk_time + '</td>';
                    }
                }

                // Stat cards
                var totalSalesEl = document.getElementById('ddTotalSalesVal');
                if (totalSalesEl) totalSalesEl.textContent = data.totals.approved;

                var avgTalkEl = document.getElementById('ddAvgTalkVal');
                if (avgTalkEl) avgTalkEl.textContent = data.active_stats.avg_talk_time;

                var avgCallsEl = document.getElementById('ddAvgCallsVal');
                if (avgCallsEl) {
                    var avgCalls = data.totals.approved > 0 ? Math.round((data.active_stats.calls / data.totals.approved) * 10) / 10 : 0;
                    avgCallsEl.textContent = avgCalls;
                }
                var callsSubEl = document.getElementById('ddCallsSubVal');
                if (callsSubEl) callsSubEl.textContent = data.active_stats.calls + ' calls today (active closers)';

                var activeClosersEl = document.getElementById('ddActiveClosersVal');
                if (activeClosersEl) {
                    activeClosersEl.innerHTML = data.closer_counts.active + ' <span style="font-size:14px;color:var(--dd-text-muted)">/ ' + data.closer_counts.total + '</span>';
                }

                // Clients Summary
                var clientsTable = document.getElementById('ddClientsTable');
                if (clientsTable) {
                    var cBody = clientsTable.querySelector('tbody');
                    cBody.innerHTML = data.clients_summary.length === 0
                        ? '<tr><td colspan="9" style="text-align:center;color:var(--dd-text-muted);padding:16px">No client data yet.</td></tr>'
                        : data.clients_summary.map(renderClientRow).join('');

                    var cTf = clientsTable.querySelector('tfoot tr');
                    if (cTf && data.clients_summary.length > 0) {
                        var ctApproved = sum(data.clients_summary, 'approved');
                        var ctLevel = sum(data.clients_summary, 'level');
                        cTf.innerHTML =
                            '<td>Total</td><td>-</td>' +
                            '<td>' + ctApproved + '</td>' +
                            '<td>' + ctLevel + '</td>' +
                            '<td>' + sum(data.clients_summary, 'gi') + '</td>' +
                            '<td>' + (ctApproved > 0 ? Math.round((ctLevel / ctApproved) * 100) : 0) + '%</td>' +
                            '<td>' + sum(data.clients_summary, 'target') + '</td>' +
                            '<td>' + sum(data.clients_summary, 'left') + '</td>' +
                            '<td>' + (Math.round((sum(data.clients_summary, 'avg_pre') / data.clients_summary.length) * 100) / 100) + '</td>';
                    }
                }

                // Carriers Summary
                var carriersTable = document.getElementById('ddCarriersTable');
                if (carriersTable) {
                    var caBody = carriersTable.querySelector('tbody');
                    caBody.innerHTML = data.carriers_summary.length === 0
                        ? '<tr><td colspan="8" style="text-align:center;color:var(--dd-text-muted);padding:16px">No carrier data yet.</td></tr>'
                        : data.carriers_summary.map(renderCarrierRow).join('');

                    var caTf = carriersTable.querySelector('tfoot tr');
                    if (caTf && data.carriers_summary.length > 0) {
                        var ctApproved = sum(data.carriers_summary, 'approved');
                        var ctLevel = sum(data.carriers_summary, 'level');
                        caTf.innerHTML =
                            '<td>Total</td>' +
                            '<td>' + ctApproved + '</td>' +
                            '<td>' + ctLevel + '</td>' +
                            '<td>' + sum(data.carriers_summary, 'gi') + '</td>' +
                            '<td>' + (ctApproved > 0 ? Math.round((ctLevel / ctApproved) * 100) : 0) + '%</td>' +
                            '<td>' + sum(data.carriers_summary, 'target') + '</td>' +
                            '<td>' + sum(data.carriers_summary, 'left') + '</td>' +
                            '<td>' + (Math.round((sum(data.carriers_summary, 'avg_pre') / data.carriers_summary.length) * 100) / 100) + '</td>';
                    }
                }

                // Celebration on new approved sale
                if (!firstRun && data.latest_id && data.latest_id !== lastLatestId) {
                    window.ddCelebrateSale(data.latest_closer);
                }
                lastLatestId = data.latest_id;
                firstRun = false;
            })
            .catch(function(err){ console.error('Live board poll failed', err); });
    }

    poll();
    setInterval(poll, 5000); // ab 5 second
})();
</script>
<script>
(function(){
    var ctx = document.getElementById('ddTeamPieChart');
    var labels = @json($teamPie['labels']);
    var data = @json($teamPie['data']);
    var colors = ['#34f5c5', '#ffb020', '#93a2ac', '#ff5a5a', '#7de8cf', '#c9d2da'];

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#090d12',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { color: '#93a2ac', font: { size: 11 } } }
            }
        }
    });
})();

(function(){
    var card = document.getElementById('ddTrackCard');
    if (!card) return;

    var fill = document.getElementById('ddTrackFill');
    var runner = document.getElementById('ddTrackRunner');
    var shadow = document.getElementById('ddTrackShadow');
    var countEl = document.getElementById('ddTrackCount');
    var pct = parseFloat(fill.dataset.pct || '0');

    setTimeout(function(){
        fill.style.width = pct + '%';
        runner.style.left = pct + '%';
        shadow.style.left = pct + '%';
    }, 350);

    var target = parseInt(countEl.dataset.target || '0', 10);
    var start = null;
    var duration = 1400;
    function step(ts){
        if (!start) start = ts;
        var progress = Math.min((ts - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        countEl.textContent = Math.round(eased * target).toLocaleString();
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);

    if (!card.classList.contains('is-complete')) return;
    var holder = document.getElementById('ddConfetti');
    var colors = ['#34f5c5', '#ffb020', '#f3f6f7', '#0f6e56'];
    function burst(){
        holder.innerHTML = '';
        for (var i = 0; i < 30; i++) {
            var piece = document.createElement('i');
            piece.style.left = Math.random() * 100 + '%';
            piece.style.background = colors[i % colors.length];
            piece.style.animationDelay = (Math.random() * 0.5) + 's';
            holder.appendChild(piece);
        }
    }
    setTimeout(burst, 500);
    setInterval(burst, 3400);
})();
</script>

<script>
window.ddCelebrateSale = function (closerName) {
    var overlay = document.getElementById('ddSaleCelebration');
    var bubbleHolder = document.getElementById('ddSaleBubbles');
    var dancerHolder = document.getElementById('ddSaleDancers');
    var fireworkHolder = document.getElementById('ddSaleFireworks');
    var banner = document.getElementById('ddSaleBanner');
    var nameEl = document.getElementById('ddSaleCloserName');
    if (!overlay || !bubbleHolder || !dancerHolder || !fireworkHolder || !banner || !nameEl) return;

    nameEl.textContent = closerName;

    banner.style.animation = 'none';
    void banner.offsetWidth;
    banner.style.animation = '';

    bubbleHolder.innerHTML = '';
    var count = 26;
    for (var i = 0; i < count; i++) {
        var b = document.createElement('span');
        b.className = 'dd-sale-bubble';
        var size = 10 + Math.random() * 34;
        var drift = (Math.random() * 60 - 30) + 'px';
        b.style.width = size + 'px';
        b.style.height = size + 'px';
        b.style.left = Math.random() * 100 + '%';
        b.style.setProperty('--dd-drift', drift);
        b.style.animationDelay = (Math.random() * 1.8) + 's';
        b.style.animationDuration = (3.6 + Math.random() * 1.6) + 's';
        bubbleHolder.appendChild(b);
    }

    overlay.classList.add('show');

    dancerHolder.innerHTML = '';
    var spot = document.createElement('div');
    spot.className = 'dd-dance-spot';
    dancerHolder.appendChild(spot);

    var d = document.createElement('span');
    d.className = 'dd-dancer';
    d.textContent = '🕺';
    dancerHolder.appendChild(d);

    fireworkHolder.innerHTML = '';
    var fwColors = ['#34f5c5', '#ffb020', '#ffffff', '#7de8cf'];

    function fireworkBurst() {
        var originX = 15 + Math.random() * 70;
        var originY = 15 + Math.random() * 45;

        var flash = document.createElement('span');
        flash.className = 'dd-fw-flash';
        flash.style.left = originX + '%';
        flash.style.top = originY + '%';
        fireworkHolder.appendChild(flash);
        setTimeout(function () { flash.remove(); }, 550);

        var particleCount = 18;
        for (var p = 0; p < particleCount; p++) {
            var angle = (Math.PI * 2 * p) / particleCount + Math.random() * 0.2;
            var dist = 60 + Math.random() * 70;
            var particle = document.createElement('span');
            particle.className = 'dd-fw-particle';
            particle.style.left = originX + '%';
            particle.style.top = originY + '%';
            particle.style.background = fwColors[p % fwColors.length];
            particle.style.setProperty('--dd-fw-x', (Math.cos(angle) * dist) + 'px');
            particle.style.setProperty('--dd-fw-y', (Math.sin(angle) * dist) + 'px');
            fireworkHolder.appendChild(particle);
            (function (el) { setTimeout(function () { el.remove(); }, 1150); })(particle);
        }
    }

    fireworkBurst();
    var fwTimers = [
        setTimeout(fireworkBurst, 900),
        setTimeout(fireworkBurst, 1900),
        setTimeout(fireworkBurst, 2900),
        setTimeout(fireworkBurst, 3900)
    ];

    clearTimeout(window._ddCelebrateTimeout);
    (window._ddFireworkTimers || []).forEach(clearTimeout);
    window._ddFireworkTimers = fwTimers;
    window._ddCelebrateTimeout = setTimeout(function () {
        overlay.classList.remove('show');
        bubbleHolder.innerHTML = '';
        dancerHolder.innerHTML = '';
        fireworkHolder.innerHTML = '';
    }, 5000);
};
</script>

@if(session('celebrate_closer'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        window.ddCelebrateSale(@json(session('celebrate_closer')));
    }, 800);
});
</script>
@endif
@endsection