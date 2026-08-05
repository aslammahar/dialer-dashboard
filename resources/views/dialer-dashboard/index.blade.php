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

    <div id="ddAnnouncementCelebration">
    <div class="dd-lightbeams" id="ddAnnLights">
        <div class="dd-beam dd-beam-1"></div>
        <div class="dd-beam dd-beam-2"></div>
        <div class="dd-beam dd-beam-3"></div>
        <div class="dd-beam dd-beam-4"></div>
    </div>
    <div class="dd-fireworks" id="ddAnnFireworks"></div>
    <div class="dd-msg-banner" id="ddMsgBanner">
        <div class="dd-msg-pill">
            <div class="dd-msg-kicker">📢 Announcement</div>
            <div class="dd-msg-person" id="ddMsgPerson">
                <div class="dd-msg-avatar-wrap">
                    <span class="dd-msg-avatar-ring ring-1"></span>
                    <span class="dd-msg-avatar-ring ring-2"></span>
                    <img src="" alt="" class="dd-msg-avatar" id="ddMsgAuthorImage">
                </div>
                <div class="dd-msg-author" id="ddMsgAuthorName">—</div>
            </div>
            <div class="dd-msg-text" id="ddMsgText">—</div>
            <div class="dd-msg-wave" id="ddMsgWave">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
    </div>
</div>

    <div id="ddTeamOvertakeCelebration">
        <div class="dd-lightbeams" id="ddTeamOvertakeLights">
            <div class="dd-beam dd-beam-1"></div>
            <div class="dd-beam dd-beam-2"></div>
            <div class="dd-beam dd-beam-3"></div>
            <div class="dd-beam dd-beam-4"></div>
        </div>
        <div class="dd-fireworks" id="ddTeamOvertakeFireworks"></div>
        <div class="dd-overtake-streaks" id="ddTeamOvertakeStreaks"></div>
        <div class="dd-overtake-banner" id="ddTeamOvertakeBanner">
            <div class="dd-overtake-pill">
                <div class="dd-overtake-kicker">Team overtake</div>
                <div class="dd-overtake-match">
                    <span id="ddOvertakeWinner">—</span>
                    <span class="dd-overtake-vs">beat</span>
                    <span id="ddOvertakeLoser">—</span>
                </div>
                <div class="dd-overtake-sub"><span id="ddOvertakeSales">0</span> approved sales on board</div>
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
        <div class="dd-shift-timer" id="ddShiftTimer">
            <span class="dd-dot"></span>
            <span id="ddShiftTimerText">Office Remaining Time — --:--:--</span>
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
{{-- <a href="{{ route('dialer-sc-report') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-report-analytics"></i> SC's Report
</a> --}}
@if($canEdit)
<a href="{{ route('attendance-closer.index') }}" class="dd-readonly-badge" style="text-decoration:none">
    <i class="ti ti-calendar-check"></i> Mark Attendance
</a>
<div style="display:flex;align-items:center;gap:6px">
    <input type="text" id="ddAnnouncementInput" class="dd-input" placeholder="Type announcement..." style="min-width:180px">
    <button type="button" id="ddAnnouncementBtn" class="dd-apply" style="padding:9px 14px">📢 Announcement</button>
</div>
@endif
        <div class="dd-sync">
            <span class="dd-dot"></span>
            Synced hourly · last sync {{ $lastSyncedAt ?? 'pending first run' }}
        </div>
    </div>
</div>

    <div style="display:none">
    <form class="dd-filters" method="GET" action="{{ route('dialer-dashboard') }}">
    <input type="hidden" name="performance_month" value="{{ $monthlyPerformanceMonth }}">
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
    </div>

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

@php
    // Find today's top closer (highest level + approved)
    $topCloser = null;
    $topScore = 0;
    foreach ($dailyBoard as $row) {
        $score = ($row['level'] ?? 0) + ($row['approved'] ?? 0);
        if ($score > $topScore) {
            $topScore = $score;
            $topCloser = $row;
        }
    }
@endphp

<div class="dd-top-closer-card" id="ddTopCloserCard" style="{{ !$topCloser ? 'display:none' : '' }}">
    <div class="dd-top-closer-trophy">🥇</div>
    <div class="dd-top-closer-info" style="text-align:center">
        <div class="dd-top-closer-label" style="color:#38bdf8">Today's Top Team</div>
        <div class="dd-top-closer-name" id="ddTopTeamName" style="font-size:20px">{{ $teamsSummary[0]['team'] ?? '-' }}</div>
        <div class="dd-top-closer-team" id="ddTopTeamApproved">{{ $teamsSummary[0]['approved'] ?? 0 }} approved sales</div>
    </div>
    <div class="dd-top-closer-divider"></div>
    <div class="dd-top-closer-info" style="text-align:center">
        <div class="dd-top-closer-label">🏆 Today's Top Closer</div>
        <div class="dd-top-closer-name" id="ddTopCloserName">{{ $topCloser['closer'] ?? '-' }}</div>
        <div class="dd-top-closer-team" id="ddTopCloserTeam">{{ $topCloser['team'] ?? '' }}</div>
    </div>
    <div class="dd-top-closer-stats">
        <div class="dd-top-closer-stat">
            <span class="dd-top-closer-stat-val" id="ddTopCloserApproved">{{ $topCloser['approved'] ?? 0 }}</span>
            <span class="dd-top-closer-stat-lbl">Approved</span>
        </div>
        <div class="dd-top-closer-stat">
            <span class="dd-top-closer-stat-val" id="ddTopCloserLevel">{{ $topCloser['level'] ?? 0 }}</span>
            <span class="dd-top-closer-stat-lbl">Level</span>
        </div>
        <div class="dd-top-closer-stat">
            <span class="dd-top-closer-stat-val" id="ddTopCloserGI">{{ $topCloser['gi'] ?? 0 }}</span>
            <span class="dd-top-closer-stat-lbl">GI</span>
        </div>
        <div class="dd-top-closer-stat">
            <span class="dd-top-closer-stat-val" id="ddTopCloserLevelPct">{{ $topCloser['level_pct'] ?? 0 }}%</span>
            <span class="dd-top-closer-stat-lbl">Level %</span>
        </div>
    </div>
</div>

    <div class="dd-panel" style="margin-top:16px">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Today's Sales Board</div>
                <div class="dd-panel-sub">{{ now('America/New_York')->format('d M Y') }} (New York) - Approved vs Pending per closer - <span id="ddTodayClosersCount">{{ $dailyBoardTotals['closers'] ?? 0 }}</span> closers with sales today</div>
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
        <td>Total</td>
        <td>-</td>
        <td><span class="dd-total-closers">{{ $dailyBoardTotals['closers'] ?? 0 }} Closers</span></td>
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
        <table class="dd-lb-table" id="ddTeamsTable">
         <thead>
    <tr>
        <th>Last Sale</th><th>Team</th><th style="text-align:center">Total Closers</th><th>Approved</th><th>Level</th><th>GI</th>
        <th>Level %</th><th>SPD</th><th>Avg Pre</th><th>Avg Talk Time</th>
        <th>Target</th><th>Left</th>
    </tr>
</thead>
<tbody>
    @forelse($teamsSummary as $t)
        <tr>
            <td>{{ $t['last_sale'] }}</td>
            <td>{{ $t['team'] }}</td>
            <td style="text-align:center"><span class="dd-lb-team" style="background:rgba(52,245,197,.12);color:#34f5c5" title="Active / Total">{{ $t['active_closers'] ?? 0 }} / {{ $t['closers'] }}</span></td>
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
        <td style="text-align:center">{{ $teamsSummaryTotals['active_closers'] ?? 0 }} / {{ $teamsSummaryTotals['closers'] }}</td>
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
            <div class="dd-panel-sub">Today — Client Breakdown / Approved / Level / GI / Target</div>
        </div>
        <a href="{{ route('sales-reports.carrier-wise') }}" class="dd-apply" style="text-decoration:none">View Full</a>
    </div>
    <div class="dd-lb-scroll">
        <table class="dd-lb-table" id="ddCarriersTable">
            <thead>
                <tr>
                    <th>Carrier</th>
                    @foreach($carriersClients as $cn)
                        <th style="color:var(--dd-accent);white-space:nowrap">{{ $cn }}</th>
                    @endforeach
                    <th>Approved</th><th>Level</th><th>GI</th>
                    <th>Level %</th><th>Target</th><th>Left</th><th>Avg Pre</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carriersSummary as $c)
                    <tr>
                        <td>{{ $c['carrier'] }}</td>
                        @foreach($carriersClients as $cn)
                            <td>{{ $c['client_breakdown'][$cn] ?? 0 }}</td>
                        @endforeach
                        <td><strong>{{ $c['approved'] }}</strong></td>
                        <td>{{ $c['level'] }}</td>
                        <td>{{ $c['gi'] }}</td>
                        <td>{{ $c['level_pct'] }}%</td>
                        <td>{{ $c['target'] }}</td>
                        <td>{{ $c['left'] }}</td>
                        <td>{{ $c['avg_pre'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ 8 + count($carriersClients) }}" style="text-align:center;color:var(--dd-text-muted);padding:16px">No carrier data yet.</td></tr>
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
        @foreach($carriersClients as $cn)
            <td>{{ array_sum(array_map(fn($c) => $c['client_breakdown'][$cn] ?? 0, $carriersSummary)) }}</td>
        @endforeach
        <td><strong>{{ $ctApproved }}</strong></td>
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
            <div class="dd-panel-sub">Blended score: sales volume + conversion efficiency + level% — {{ $monthlyPerformanceLabel }}</div>
        </div>
        <form method="GET" action="{{ route('dialer-dashboard') }}" class="dd-month-filter">
            <input type="hidden" name="view" value="{{ $filters['view'] }}">
            <input type="hidden" name="group" value="{{ $filters['group'] }}">
            <input type="hidden" name="from" value="{{ $filters['from'] }}">
            <input type="hidden" name="to" value="{{ $filters['to'] }}">
            <label class="dd-label" for="ddPerformanceMonth">Month</label>
            <input
                type="month"
                id="ddPerformanceMonth"
                name="performance_month"
                class="dd-input dd-month-input"
                value="{{ $monthlyPerformanceMonth }}"
                max="{{ now('America/New_York')->format('Y-m') }}"
                onchange="this.form.submit()"
            >
        </form>
    </div>
    <div class="dd-lb-scroll">
        <table class="dd-lb-table" id="ddMonthlyTable">
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
                    <th>Clients</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyPerformance as $idx => $p)
                    @php $rank = $idx + 1; @endphp
                    <tr class="{{ $rank <= 6 ? 'r'.$rank : '' }}">
                        <td><span class="dd-rank-badge">{{ $rank }}</span></td>
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
                               <td>
                            @if(!empty($p['client_breakdown']))
                                <div style="display:flex;flex-wrap:wrap;gap:4px">
                                @foreach($p['client_breakdown'] as $cName => $cCnt)
                                    <span style="background:rgba(52,245,197,.12);color:#34f5c5;border:1px solid rgba(52,245,197,.25);border-radius:4px;padding:1px 6px;font-size:10.5px;white-space:nowrap">{{ $cName }}: <b>{{ $cCnt }}</b></span>
                                @endforeach
                                </div>
                            @else
                                <span style="color:var(--dd-text-muted)">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" style="text-align:center;color:var(--dd-text-muted);padding:20px">
                            No sales data yet for this month.
                        </td>
                    </tr>
                @endforelse
            </tbody>
@if(count($monthlyPerformance))
<tfoot>
    <tr>
        <td colspan="3">Total / Avg</td>
        <td>{{ $monthlyPerformanceTotals['working_days_avg'] }}</td>
        <td>{{ $monthlyPerformanceTotals['mtd_total'] }} <span style="color:var(--dd-text-muted);font-size:10.5px">(avg {{ $monthlyPerformanceTotals['mtd_avg'] }})</span></td>
        <td>{{ $monthlyPerformanceTotals['spd_avg'] }}</td>
        <td>{{ $monthlyPerformanceTotals['level_total'] }} <span style="color:var(--dd-text-muted);font-size:10.5px">(avg {{ $monthlyPerformanceTotals['level_avg'] }})</span></td>
        <td>{{ $monthlyPerformanceTotals['gi_total'] }} <span style="color:var(--dd-text-muted);font-size:10.5px">(avg {{ $monthlyPerformanceTotals['gi_avg'] }})</span></td>
        <td>{{ $monthlyPerformanceTotals['level_pct_avg'] }}%</td>
        <td>{{ $monthlyPerformanceTotals['avg_pre_avg'] }}</td>
        <td>{{ $monthlyPerformanceTotals['calls_total'] }} <span style="color:var(--dd-text-muted);font-size:10.5px">(avg {{ $monthlyPerformanceTotals['calls_avg'] }})</span></td>
        <td>{{ $monthlyPerformanceTotals['conversion_avg'] }}</td>
        <td>{{ $monthlyPerformanceTotals['avg_talk_time_avg'] }}</td>
         <td>-</td>
    </tr>
</tfoot>
@endif

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
    var shiftEl = document.getElementById('ddShiftTimerText');
    if (!shiftEl) return;

    function formatTimeLeft(ms){
        if (ms <= 0) return 'Remaining — 00:00:00';
        var totalSeconds = Math.floor(ms / 1000);
        var hours = Math.floor(totalSeconds / 3600);
        var minutes = Math.floor((totalSeconds % 3600) / 60);
        var seconds = totalSeconds % 60;
        return 'Remaining — ' + String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    }

    function getShiftEndTime(now){
        var startHour = 20;
        var startMinute = 30;
        var endHour = 7;
        var endMinute = 0;
        var start = new Date(now);
        start.setHours(startHour, startMinute, 0, 0);
        var end = new Date(now);
        end.setHours(endHour, endMinute, 0, 0);

        var nowMinutes = now.getHours() * 60 + now.getMinutes();
        var startMinutes = startHour * 60 + startMinute;
        var endMinutes = endHour * 60 + endMinute;

        if (nowMinutes >= startMinutes) {
            end.setDate(end.getDate() + 1);
        } else if (nowMinutes < endMinutes) {
            start.setDate(start.getDate() - 1);
        } else {
            end.setDate(end.getDate() + 1);
        }

        return end;
    }

    function tick(){
        var now = new Date();
        if (shiftEl) {
            var shiftEnd = getShiftEndTime(now);
            var remainingMs = shiftEnd.getTime() - now.getTime();
            shiftEl.textContent = 'Office Remaining Time — ' + formatTimeLeft(remainingMs).replace('Remaining — ', '');
        }
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
<script>
(function(){
    var lastLatestId = null;
    var lastTeamOvertakeId = null;
    var lastAnnouncement = null;
    var pollUrl = @json(route('dialer-dashboard.live-board', ['performance_month' => $monthlyPerformanceMonth]));
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

    function renderCarrierRow(c, clients) {
        var clientCols = (clients || []).map(function(cn) {
            return '<td>' + ((c.client_breakdown && c.client_breakdown[cn]) ? c.client_breakdown[cn] : 0) + '</td>';
        }).join('');
        return '<tr>' +
            '<td>' + c.carrier + '</td>' +
            clientCols +
            '<td><strong>' + c.approved + '</strong></td>' +
            '<td>' + c.level + '</td>' +
            '<td>' + c.gi + '</td>' +
            '<td>' + c.level_pct + '%</td>' +
            '<td>' + c.target + '</td>' +
            '<td>' + c.left + '</td>' +
            '<td>' + c.avg_pre + '</td>' +
        '</tr>';
    }

    function renderTeamRow(t, rank) {
        var rClass = rank <= 3 ? ' r' + rank : '';
        return '<tr class="' + rClass + '">' +
            '<td>' + t.last_sale + '</td>' +
            '<td>' + t.team + '</td>' +
            '<td style="text-align:center"><span class="dd-lb-team" style="background:rgba(52,245,197,.12);color:#34f5c5" title="Active / Total">' + (t.active_closers || 0) + ' / ' + t.closers + '</span></td>' +
            '<td>' + t.approved + '</td>' +
            '<td>' + t.level + '</td>' +
            '<td>' + t.gi + '</td>' +
            '<td>' + t.level_pct + '%</td>' +
            '<td>' + t.spd + '</td>' +
            '<td>' + t.avg_pre + '</td>' +
            '<td>' + t.avg_talk_time + '</td>' +
            '<td>' + t.target + '</td>' +
            '<td>' + t.left + '</td>' +
        '</tr>';
    }

    function renderMonthlyRow(p, rank) {
        var clientHtml = '<span style="color:var(--dd-text-muted)">—</span>';
        if (p.client_breakdown && Object.keys(p.client_breakdown).length > 0) {
            clientHtml = '<div style="display:flex;flex-wrap:wrap;gap:4px">';
            for (var cName in p.client_breakdown) {
                clientHtml += '<span style="background:rgba(52,245,197,.12);color:#34f5c5;border:1px solid rgba(52,245,197,.25);border-radius:4px;padding:1px 6px;font-size:10.5px;white-space:nowrap">' + cName + ': <b>' + p.client_breakdown[cName] + '</b></span>';
            }
            clientHtml += '</div>';
        }

        var rClass = rank <= 6 ? ' r' + rank : '';
        var avatarLetters = (p.closer || '??').substring(0, 2).toUpperCase();

        return '<tr class="' + rClass + '">' +
            '<td><span class="dd-rank-badge">' + rank + '</span></td>' +
            '<td><div class="dd-lb-agent"><div class="dd-avatar">' + avatarLetters + '</div><div class="dd-lb-name">' + p.closer + '</div></div></td>' +
            '<td><span class="dd-lb-team">' + p.team + '</span></td>' +
            '<td>' + p.working_days + '</td>' +
            '<td>' + p.mtd + '</td>' +
            '<td>' + p.spd + '</td>' +
            '<td>' + p.level + '</td>' +
            '<td>' + p.gi + '</td>' +
            '<td>' + p.level_pct + '%</td>' +
            '<td>' + p.avg_pre + '</td>' +
            '<td>' + p.calls + '</td>' +
            '<td style="color:#22c55e;font-weight:600">' + p.conversion + '</td>' +
            '<td>' + p.avg_talk_time + '</td>' +
            '<td>' + clientHtml + '</td>' +
        '</tr>';
    }

    function sum(arr, key) {
        return arr.reduce(function(s, r){ return s + (r[key] || 0); }, 0);
    }

    window.ddCelebrateTeamOvertake = function (winnerTeam, passedTeam, currentSales) {
        var overlay = document.getElementById('ddTeamOvertakeCelebration');
        var fireworkHolder = document.getElementById('ddTeamOvertakeFireworks');
        var streakHolder = document.getElementById('ddTeamOvertakeStreaks');
        var banner = document.getElementById('ddTeamOvertakeBanner');
        var winnerEl = document.getElementById('ddOvertakeWinner');
        var loserEl = document.getElementById('ddOvertakeLoser');
        var salesEl = document.getElementById('ddOvertakeSales');
        if (!overlay || !fireworkHolder || !streakHolder || !banner || !winnerEl || !loserEl || !salesEl) return;

        winnerEl.textContent = winnerTeam;
        loserEl.textContent = passedTeam;
        salesEl.textContent = currentSales;

        banner.style.animation = 'none';
        void banner.offsetWidth;
        banner.style.animation = '';

        fireworkHolder.innerHTML = '';
        streakHolder.innerHTML = '';
        overlay.classList.add('show');

        var lineColors = [
            'linear-gradient(90deg,rgba(255,255,255,0),#38bdf8,#34f5c5,rgba(255,255,255,0))',
            'linear-gradient(90deg,rgba(255,255,255,0),#ffb020,#38bdf8,rgba(255,255,255,0))',
            'linear-gradient(90deg,rgba(255,255,255,0),#a78bfa,#34f5c5,rgba(255,255,255,0))'
        ];
        for (var i = 0; i < 24; i++) {
            var line = document.createElement('span');
            line.className = 'dd-overtake-line';
            line.style.width = (18 + Math.random() * 34) + 'vw';
            line.style.left = (-34 + Math.random() * 26) + 'vw';
            line.style.top = (10 + Math.random() * 78) + '%';
            line.style.animationDelay = (Math.random() * 0.85) + 's';
            line.style.animationDuration = (0.75 + Math.random() * 0.65) + 's';
            line.style.background = lineColors[i % lineColors.length];
            streakHolder.appendChild(line);
        }

        var fwColors = ['#38bdf8', '#34f5c5', '#ffb020', '#ffffff', '#a78bfa'];
        function fireworkBurst() {
            var originX = 18 + Math.random() * 64;
            var originY = 12 + Math.random() * 45;
            var flash = document.createElement('span');
            flash.className = 'dd-fw-flash';
            flash.style.left = originX + '%';
            flash.style.top = originY + '%';
            fireworkHolder.appendChild(flash);
            setTimeout(function () { flash.remove(); }, 550);

            for (var p = 0; p < 20; p++) {
                var angle = (Math.PI * 2 * p) / 20 + Math.random() * 0.22;
                var dist = 70 + Math.random() * 80;
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
            setTimeout(fireworkBurst, 700),
            setTimeout(fireworkBurst, 1500),
            setTimeout(fireworkBurst, 2400),
            setTimeout(fireworkBurst, 3400),
            setTimeout(fireworkBurst, 4700),
            setTimeout(fireworkBurst, 6200),
            setTimeout(fireworkBurst, 8000)
        ];

        clearTimeout(window._ddTeamOvertakeTimeout);
        (window._ddTeamOvertakeFwTimers || []).forEach(clearTimeout);
        window._ddTeamOvertakeFwTimers = fwTimers;
        window._ddTeamOvertakeTimeout = setTimeout(function () {
            overlay.classList.remove('show');
            fireworkHolder.innerHTML = '';
            streakHolder.innerHTML = '';
        }, 12000);
    };

    var prevTeamState = {};
    function checkTeamOvertake(teamsSummary, suppressAnimation) {
        if (!teamsSummary || teamsSummary.length === 0) {
            prevTeamState = {};
            return;
        }

        if (!firstRun) {
            for (var i = 0; i < teamsSummary.length; i++) {
                var t = teamsSummary[i];
                var teamName = t.team;
                var currentRank = i + 1;
                var currentSales = Number(t.approved || 0);

                var oldState = prevTeamState[teamName];
                if (oldState && currentRank < oldState.rank && currentSales > oldState.approved) {
                    var beatenRow = teamsSummary[i + 1] || null;
                    var passedTeam = beatenRow && beatenRow.team !== teamName ? beatenRow.team : null;

                    if (!suppressAnimation && passedTeam && typeof window.ddCelebrateTeamOvertake === 'function') {
                        window.ddCelebrateTeamOvertake(teamName, passedTeam, currentSales);
                        break;
                    }
                }
            }
        }

        prevTeamState = {};
        for (var j = 0; j < teamsSummary.length; j++) {
            prevTeamState[teamsSummary[j].team] = {
                rank: j + 1,
                approved: Number(teamsSummary[j].approved || 0)
            };
        }
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
                            '<td>Total</td>' +
                            '<td>-</td>' +
                            '<td><span class="dd-total-closers">' + (data.totals.closers || 0) + ' Closers</span></td>' +
                            '<td>' + data.totals.approved + '</td>' +
                            '<td>' + data.totals.level + '</td>' +
                            '<td>' + data.totals.gi + '</td>' +
                            '<td>' + data.totals.level_pct + '%</td>' +
                            '<td>' + data.totals.avg_pre + '</td>' +
                            '<td>' + data.totals.calls + '</td>' +
                            '<td>' + data.totals.avg_talk_time + '</td>';
                    }

                    var todayClosersEl = document.getElementById('ddTodayClosersCount');
                    if (todayClosersEl) todayClosersEl.textContent = data.totals.closers || 0;
                }

                // Today's Top Closer
                var topCloserCard = document.getElementById('ddTopCloserCard');

                if (topCloserCard && data.board.length > 0) {
                    var topCloser = null;
                    var topScore = 0;
                    data.board.forEach(function(row) {
                        var score = (row.level || 0) + (row.approved || 0);
                        if (score > topScore) {
                            topScore = score;
                            topCloser = row;
                        }
                    });
                    if (topCloser) {
                        topCloserCard.style.display = '';
                        document.getElementById('ddTopCloserName').textContent = topCloser.closer;
                        document.getElementById('ddTopCloserTeam').textContent = topCloser.team;
                        document.getElementById('ddTopCloserApproved').textContent = topCloser.approved;
                        document.getElementById('ddTopCloserLevel').textContent = topCloser.level;
                        document.getElementById('ddTopCloserGI').textContent = topCloser.gi;
                        document.getElementById('ddTopCloserLevelPct').textContent = topCloser.level_pct + '%';
                    }
                    // Today's Top Team (alongside top closer)
                    var topTeamNameEl = document.getElementById('ddTopTeamName');
                    var topTeamApprovedEl = document.getElementById('ddTopTeamApproved');
                    if (topTeamNameEl && data.teams_summary && data.teams_summary.length > 0) {
                        topTeamNameEl.textContent = data.teams_summary[0].team;
                        topTeamApprovedEl.textContent = data.teams_summary[0].approved + ' approved sales';
                    }
                } else if (topCloserCard) {
                    topCloserCard.style.display = 'none';
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
                    var clients = data.carriers_clients || [];
                    var colSpan = 8 + clients.length;
                    var caBody = carriersTable.querySelector('tbody');
                    caBody.innerHTML = data.carriers_summary.length === 0
                        ? '<tr><td colspan="' + colSpan + '" style="text-align:center;color:var(--dd-text-muted);padding:16px">No carrier data yet.</td></tr>'
                        : data.carriers_summary.map(function(c) { return renderCarrierRow(c, clients); }).join('');

                    // Update thead client columns
                    var thead = carriersTable.querySelector('thead tr');
                    if (thead) {
                        var baseHeaders = '<th>Carrier</th>';
                        clients.forEach(function(cn) {
                            baseHeaders += '<th style="color:var(--dd-accent);white-space:nowrap">' + cn + '</th>';
                        });
                        baseHeaders += '<th>Approved</th><th>Level</th><th>GI</th><th>Level %</th><th>Target</th><th>Left</th><th>Avg Pre</th>';
                        thead.innerHTML = baseHeaders;
                    }

                    var caTf = carriersTable.querySelector('tfoot tr');
                    if (caTf && data.carriers_summary.length > 0) {
                        var ctApproved = sum(data.carriers_summary, 'approved');
                        var ctLevel = sum(data.carriers_summary, 'level');
                        var clientTotals = clients.map(function(cn) {
                            var t = data.carriers_summary.reduce(function(acc, c) {
                                return acc + ((c.client_breakdown && c.client_breakdown[cn]) ? c.client_breakdown[cn] : 0);
                            }, 0);
                            return '<td>' + t + '</td>';
                        }).join('');
                        caTf.innerHTML =
                            '<td>Total</td>' +
                            clientTotals +
                            '<td><strong>' + ctApproved + '</strong></td>' +
                            '<td>' + ctLevel + '</td>' +
                            '<td>' + sum(data.carriers_summary, 'gi') + '</td>' +
                            '<td>' + (ctApproved > 0 ? Math.round((ctLevel / ctApproved) * 100) : 0) + '%</td>' +
                            '<td>' + sum(data.carriers_summary, 'target') + '</td>' +
                            '<td>' + sum(data.carriers_summary, 'left') + '</td>' +
                            '<td>' + (Math.round((sum(data.carriers_summary, 'avg_pre') / data.carriers_summary.length) * 100) / 100) + '</td>';
                    }
                }
                // Teams Summary
                var teamsTable = document.getElementById('ddTeamsTable');
                if (teamsTable) {
                    var tBody = teamsTable.querySelector('tbody');
                    tBody.innerHTML = data.teams_summary.length === 0
                        ? '<tr><td colspan="12" style="text-align:center;color:var(--dd-text-muted);padding:16px">No team data yet.</td></tr>'
                        : data.teams_summary.map(function(t, idx){ return renderTeamRow(t, idx + 1); }).join('');

                    var tTf = teamsTable.querySelector('tfoot tr');
                    if (tTf && data.teams_summary.length > 0) {
                        var tt = data.teams_summary_totals;
                        tTf.innerHTML =
                            '<td>Total / Avg</td><td>-</td>' +
                            '<td style="text-align:center">' + (tt.active_closers || 0) + ' / ' + tt.closers + '</td>' +
                            '<td>' + tt.approved + '</td>' +
                            '<td>' + tt.level + '</td>' +
                            '<td>' + tt.gi + '</td>' +
                            '<td>' + tt.level_pct + '%</td>' +
                            '<td>' + tt.spd + '</td>' +
                            '<td>' + tt.avg_pre + '</td>' +
                            '<td>' + tt.avg_talk_time + '</td>' +
                            '<td>' + tt.target + '</td>' +
                            '<td>' + tt.left + '</td>';
                    }

                    var serverOvertake = data.team_overtake || null;
                    var serverOvertakeId = serverOvertake ? String(serverOvertake.id) : null;
                    var shouldPlayServerOvertake = serverOvertake && serverOvertakeId !== lastTeamOvertakeId;

                    checkTeamOvertake(data.teams_summary, shouldPlayServerOvertake);

                    if (shouldPlayServerOvertake && typeof window.ddCelebrateTeamOvertake === 'function') {
                        window.ddCelebrateTeamOvertake(serverOvertake.winner, serverOvertake.loser, serverOvertake.sales);
                        lastTeamOvertakeId = serverOvertakeId;
                    }
                }

                // Monthly Performance Ranking
                var monthlyTable = document.getElementById('ddMonthlyTable');
                if (monthlyTable && data.monthly_performance) {
                    var mBody = monthlyTable.querySelector('tbody');
                    mBody.innerHTML = data.monthly_performance.length === 0
                        ? '<tr><td colspan="14" style="text-align:center;color:var(--dd-text-muted);padding:20px">No sales data yet for this month.</td></tr>'
                        : data.monthly_performance.map(function(p, idx){ return renderMonthlyRow(p, idx + 1); }).join('');

                    var mTf = monthlyTable.querySelector('tfoot tr');
                    if (mTf && data.monthly_performance_totals) {
                        var mt = data.monthly_performance_totals;
                        mTf.innerHTML =
                            '<td colspan="3">Total / Avg</td>' +
                            '<td>' + mt.working_days_avg + '</td>' +
                            '<td>' + mt.mtd_total + ' <span style="color:var(--dd-text-muted);font-size:10.5px">(avg ' + mt.mtd_avg + ')</span></td>' +
                            '<td>' + mt.spd_avg + '</td>' +
                            '<td>' + mt.level_total + ' <span style="color:var(--dd-text-muted);font-size:10.5px">(avg ' + mt.level_avg + ')</span></td>' +
                            '<td>' + mt.gi_total + ' <span style="color:var(--dd-text-muted);font-size:10.5px">(avg ' + mt.gi_avg + ')</span></td>' +
                            '<td>' + mt.level_pct_avg + '%</td>' +
                            '<td>' + mt.avg_pre_avg + '</td>' +
                            '<td>' + mt.calls_total + ' <span style="color:var(--dd-text-muted);font-size:10.5px">(avg ' + mt.calls_avg + ')</span></td>' +
                            '<td>' + mt.conversion_avg + '</td>' +
                            '<td>' + mt.avg_talk_time_avg + '</td>' +
                            '<td>-</td>';
                    }
                }
                
                // Celebration on new approved sale
                if (!firstRun && data.latest_id && data.latest_id !== lastLatestId) {
                    window.ddCelebrateSale(data.latest_closer);
                }
                lastLatestId = data.latest_id;
                var announcementId = data.announcement && typeof data.announcement === 'object'
                    ? data.announcement.id
                    : data.announcement;
                if (!firstRun && data.announcement && announcementId !== lastAnnouncement) {
                    window.ddCelebrateAnnouncement(data.announcement);
                }
                lastAnnouncement = announcementId || null;
                firstRun = false;
            })
            .catch(function(err){ console.error('Live board poll failed', err); });
    }

    poll();
    setInterval(poll, 5000); // ab 5 second
})();
</script>
<script>
window.ddPlayAnnouncementIntro = function () {
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return;

    try {
        var ctx = window._ddAnnouncementAudioCtx || new AudioContext();
        window._ddAnnouncementAudioCtx = ctx;
        if (ctx.state === 'suspended') ctx.resume();

        var now = ctx.currentTime;
        var master = ctx.createGain();
        master.gain.setValueAtTime(0.0001, now);
        master.gain.exponentialRampToValueAtTime(0.42, now + 0.03);
        master.gain.exponentialRampToValueAtTime(0.0001, now + 0.95);
        master.connect(ctx.destination);

        var boom = ctx.createOscillator();
        boom.type = 'sine';
        boom.frequency.setValueAtTime(82, now);
        boom.frequency.exponentialRampToValueAtTime(54, now + 0.38);
        var boomGain = ctx.createGain();
        boomGain.gain.setValueAtTime(0.0001, now);
        boomGain.gain.exponentialRampToValueAtTime(0.34, now + 0.035);
        boomGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.48);
        boom.connect(boomGain).connect(master);
        boom.start(now);
        boom.stop(now + 0.54);

        [523.25, 659.25, 783.99].forEach(function (freq, index) {
            var start = now + 0.28 + (index * 0.12);
            var tone = ctx.createOscillator();
            tone.type = 'sine';
            tone.frequency.setValueAtTime(freq, start);
            var gain = ctx.createGain();
            gain.gain.setValueAtTime(0.0001, start);
            gain.gain.exponentialRampToValueAtTime(0.16, start + 0.025);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.20);
            tone.connect(gain).connect(master);
            tone.start(start);
            tone.stop(start + 0.24);
        });
    } catch (e) {
        console.warn('Announcement intro audio failed', e);
    }
};

window.ddSpeakAnnouncement = function (text, authorName) {
    if (!text || !('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;

    window.speechSynthesis.cancel();

    var speakText = (authorName ? 'Announcement from ' + authorName + '. ' : 'Attention. ') + text;
    var utterance = new SpeechSynthesisUtterance(speakText);
    utterance.rate = 0.9;
    utterance.pitch = 1;
    utterance.volume = 1;

    var voices = window.speechSynthesis.getVoices ? window.speechSynthesis.getVoices() : [];
    var preferredVoice = voices
        .filter(function (voice) { return /^en(-|_)?/i.test(voice.lang || ''); })
        .sort(function (a, b) {
            var score = function (voice) {
                var name = (voice.name || '').toLowerCase();
                var lang = (voice.lang || '').toLowerCase();
                var s = 0;
                if (/zira|aria|jenny|sonia|guy|david|mark|daniel|ravi|heera/.test(name)) s += 5;
                if (/natural|online|enhanced/.test(name)) s += 3;
                if (/microsoft|google/.test(name)) s += 2;
                if (lang === 'en-in') s += 3;
                if (lang === 'en-us' || lang === 'en-gb') s += 2;
                return s;
            };
            return score(b) - score(a);
        })[0];
    if (preferredVoice) {
        utterance.voice = preferredVoice;
        utterance.lang = preferredVoice.lang || 'en-US';
    } else {
        utterance.lang = 'en-US';
    }

    setTimeout(function () {
        window.speechSynthesis.speak(utterance);
    }, 820);
};

if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
}

window.ddCelebrateAnnouncement = function (announcement) {
    var overlay = document.getElementById('ddAnnouncementCelebration');
    var fireworkHolder = document.getElementById('ddAnnFireworks');
    var banner = document.getElementById('ddMsgBanner');
    var textEl = document.getElementById('ddMsgText');
    var personEl = document.getElementById('ddMsgPerson');
    var imageEl = document.getElementById('ddMsgAuthorImage');
    var nameEl = document.getElementById('ddMsgAuthorName');
    if (!overlay || !banner || !textEl) return;

    var payload = (announcement && typeof announcement === 'object')
        ? announcement
        : { message: announcement };

    textEl.textContent = payload.message || '';
    window.ddPlayAnnouncementIntro();
    window.ddSpeakAnnouncement(payload.message || '', payload.author_name || '');
    if (personEl && imageEl && nameEl) {
        nameEl.textContent = payload.author_name || 'Announcement';
        if (payload.author_image) {
            imageEl.src = payload.author_image;
            imageEl.alt = payload.author_name || 'Announcement author';
            personEl.style.display = 'inline-flex';
        } else {
            imageEl.removeAttribute('src');
            personEl.style.display = 'none';
        }
    }

    banner.style.animation = 'none';
    void banner.offsetWidth;
    banner.style.animation = '';

    overlay.classList.add('show');

    fireworkHolder.innerHTML = '';
    var fwColors = ['#ffb020', '#34f5c5', '#ffffff', '#f3f6f7'];

    function fireworkBurst() {
        var originX = 15 + Math.random() * 70;
        var originY = 15 + Math.random() * 45;
        var flash = document.createElement('span');
        flash.className = 'dd-fw-flash';
        flash.style.left = originX + '%';
        flash.style.top = originY + '%';
        fireworkHolder.appendChild(flash);
        setTimeout(function () { flash.remove(); }, 550);

        for (var p = 0; p < 18; p++) {
            var angle = (Math.PI * 2 * p) / 18 + Math.random() * 0.2;
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
    var fwTimers = [];
    for (var i = 1; i <= 12; i++) {
        fwTimers.push(setTimeout(fireworkBurst, i * 4500));
    }

    clearTimeout(window._ddAnnTimeout);
    (window._ddAnnFwTimers || []).forEach(clearTimeout);
    window._ddAnnFwTimers = fwTimers;
    window._ddAnnTimeout = setTimeout(function () {
        overlay.classList.remove('show');
        fireworkHolder.innerHTML = '';
    }, 60000); // 1 minute
};

(function(){
    var btn = document.getElementById('ddAnnouncementBtn');
    var input = document.getElementById('ddAnnouncementInput');
    if (!btn || !input) return;

    btn.addEventListener('click', function(){
        var msg = input.value.trim();
        if (!msg) return;

        fetch(@json(route('dialer-dashboard.broadcast')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(function(r){ return r.json(); })
        .then(function(){
            input.value = '';
        })
        .catch(function(err){ console.error('Broadcast failed', err); });
    });

    input.addEventListener('keypress', function(e){
        if (e.key === 'Enter') btn.click();
    });
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
window.ddPlaySaleFireworkSound = function (isBigBurst) {
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return;

    try {
        var ctx = window._ddSaleAudioCtx || new AudioContext();
        window._ddSaleAudioCtx = ctx;
        if (ctx.state === 'suspended') ctx.resume();

        var now = ctx.currentTime;
        var master = ctx.createGain();
        master.gain.setValueAtTime(isBigBurst ? 0.82 : 0.58, now);
        master.gain.exponentialRampToValueAtTime(0.0001, now + 0.78);
        master.connect(ctx.destination);

        var thump = ctx.createOscillator();
        thump.type = 'sine';
        thump.frequency.setValueAtTime(isBigBurst ? 118 : 96, now);
        thump.frequency.exponentialRampToValueAtTime(58, now + 0.22);
        var thumpGain = ctx.createGain();
        thumpGain.gain.setValueAtTime(0.0001, now);
        thumpGain.gain.exponentialRampToValueAtTime(isBigBurst ? 0.45 : 0.25, now + 0.02);
        thumpGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.32);
        thump.connect(thumpGain).connect(master);
        thump.start(now);
        thump.stop(now + 0.36);

        function crackle(delay, gainAmount, duration) {
            var noiseLength = Math.floor(ctx.sampleRate * duration);
            var noiseBuffer = ctx.createBuffer(1, noiseLength, ctx.sampleRate);
            var data = noiseBuffer.getChannelData(0);
            for (var i = 0; i < noiseLength; i++) {
                data[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / noiseLength, 1.7);
            }
            var noise = ctx.createBufferSource();
            noise.buffer = noiseBuffer;
            var filter = ctx.createBiquadFilter();
            filter.type = 'highpass';
            filter.frequency.setValueAtTime(720 + Math.random() * 900, now + delay);
            var noiseGain = ctx.createGain();
            noiseGain.gain.setValueAtTime(0.0001, now + delay);
            noiseGain.gain.exponentialRampToValueAtTime(gainAmount, now + delay + 0.012);
            noiseGain.gain.exponentialRampToValueAtTime(0.0001, now + delay + duration);
            noise.connect(filter).connect(noiseGain).connect(master);
            noise.start(now + delay);
        }

        var crackleCount = isBigBurst ? 7 : 4;
        for (var crack = 0; crack < crackleCount; crack++) {
            crackle(0.035 + Math.random() * 0.22, isBigBurst ? 0.48 : 0.34, 0.12 + Math.random() * 0.10);
        }

        [880, 1174, 1568].forEach(function (freq, index) {
            var start = now + 0.05 + index * 0.045;
            var spark = ctx.createOscillator();
            spark.type = 'triangle';
            spark.frequency.setValueAtTime(freq + Math.random() * 80, start);
            spark.frequency.exponentialRampToValueAtTime(freq * 0.68, start + 0.13);
            var sparkGain = ctx.createGain();
            sparkGain.gain.setValueAtTime(0.0001, start);
            sparkGain.gain.exponentialRampToValueAtTime(isBigBurst ? 0.16 : 0.12, start + 0.012);
            sparkGain.gain.exponentialRampToValueAtTime(0.0001, start + 0.16);
            spark.connect(sparkGain).connect(master);
            spark.start(start);
            spark.stop(start + 0.18);
        });
    } catch (e) {
        console.warn('Sale firework audio failed', e);
    }
};

window.ddSpeakSaleClosed = function (closerName) {
    if (!closerName || !('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;

    window.speechSynthesis.cancel();

    var utterance = new SpeechSynthesisUtterance('New sale closed by ' + closerName + '. Congratulations ' + closerName + '.');
    utterance.rate = 0.92;
    utterance.pitch = 1;
    utterance.volume = 1;

    var voices = window.speechSynthesis.getVoices ? window.speechSynthesis.getVoices() : [];
    var preferredVoice = voices
        .filter(function (voice) { return /^en(-|_)?/i.test(voice.lang || ''); })
        .sort(function (a, b) {
            var score = function (voice) {
                var name = (voice.name || '').toLowerCase();
                var lang = (voice.lang || '').toLowerCase();
                var s = 0;
                if (/zira|aria|jenny|sonia|guy|david|mark|daniel|ravi|heera/.test(name)) s += 5;
                if (/natural|online|enhanced/.test(name)) s += 3;
                if (/microsoft|google/.test(name)) s += 2;
                if (lang === 'en-in') s += 3;
                if (lang === 'en-us' || lang === 'en-gb') s += 2;
                return s;
            };
            return score(b) - score(a);
        })[0];
    if (preferredVoice) {
        utterance.voice = preferredVoice;
        utterance.lang = preferredVoice.lang || 'en-US';
    } else {
        utterance.lang = 'en-US';
    }

    setTimeout(function () {
        window.speechSynthesis.speak(utterance);
    }, 520);
};

window.ddPrimeDashboardAudio = function () {
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return;

    try {
        window._ddSaleAudioCtx = window._ddSaleAudioCtx || new AudioContext();
        window._ddAnnouncementAudioCtx = window._ddAnnouncementAudioCtx || new AudioContext();
        if (window._ddSaleAudioCtx.state === 'suspended') window._ddSaleAudioCtx.resume();
        if (window._ddAnnouncementAudioCtx.state === 'suspended') window._ddAnnouncementAudioCtx.resume();
    } catch (e) {}
};

['pointerdown', 'keydown', 'touchstart'].forEach(function (eventName) {
    window.addEventListener(eventName, window.ddPrimeDashboardAudio, { once: true, passive: true });
});

window.ddCelebrateSale = function (closerName) {
    var overlay = document.getElementById('ddSaleCelebration');
    var bubbleHolder = document.getElementById('ddSaleBubbles');
    var dancerHolder = document.getElementById('ddSaleDancers');
    var fireworkHolder = document.getElementById('ddSaleFireworks');
    var banner = document.getElementById('ddSaleBanner');
    var nameEl = document.getElementById('ddSaleCloserName');
    if (!overlay || !bubbleHolder || !dancerHolder || !fireworkHolder || !banner || !nameEl) return;

    closerName = closerName || 'Closer';
    nameEl.textContent = closerName;
    window.ddPlaySaleFireworkSound(true);
    window.ddSpeakSaleClosed(closerName);

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
        window.ddPlaySaleFireworkSound(false);
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
        setTimeout(fireworkBurst, 3900),
        setTimeout(fireworkBurst, 5500),
        setTimeout(fireworkBurst, 7500),
        setTimeout(fireworkBurst, 9500),
        setTimeout(fireworkBurst, 11500),
        setTimeout(fireworkBurst, 13500)
    ];

    clearTimeout(window._ddCelebrateTimeout);
    (window._ddFireworkTimers || []).forEach(clearTimeout);
    window._ddFireworkTimers = fwTimers;
    window._ddCelebrateTimeout = setTimeout(function () {
        overlay.classList.remove('show');
        bubbleHolder.innerHTML = '';
        dancerHolder.innerHTML = '';
        fireworkHolder.innerHTML = '';
    }, 15000);
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
