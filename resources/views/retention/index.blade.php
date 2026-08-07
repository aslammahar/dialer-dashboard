@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Retention Department Dashboard')

@push('css-page')
@include('dialer-dashboard._styles')
<style>
    .dd-table th.monthly-col {
        background: rgba(30, 58, 138, 0.4) !important;
        color: #93c5fd !important;
    }
    .dd-table td.monthly-col {
        background: rgba(30, 58, 138, 0.15) !important;
    }
</style>
@endpush

@section('content')
<div class="dd-wrap">

    {{-- Top Bar --}}
    <div class="dd-topbar">
        <div class="dd-brand">
            <div class="dd-brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                    <path d="M21 3v5h-5"/>
                </svg>
            </div>
            <div>
                <div class="dd-brand-title">Retention Dashboard</div>
                <div class="dd-brand-sub">Retention Department · Live Sales & Performance</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <div class="dd-shift-timer" id="ddShiftTimer">
                <span class="dd-dot"></span>
                <span>Office Time — {{ now('America/New_York')->format('h:i:s A') }} EST</span>
            </div>

            <a href="{{ route('dialer-dashboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-phone-incoming"></i> Dialer Dashboard
            </a>

            <a href="{{ route('retention.leaderboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-trophy"></i> Inbound Leaderboard
            </a>

            <a href="{{ route('retention.reports.team-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-users"></i> Team Wise
            </a>

            <a href="{{ route('retention.reports.client-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-briefcase"></i> Client Wise
            </a>

            <a href="{{ route('retention.reports.carrier-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-truck"></i> Carrier Wise
            </a>

            <a href="{{ route('retention.report') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-report"></i> Retention Report
            </a>

            <a href="{{ route('retention.clients') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-building"></i> Retention Clients
            </a>

            <a href="{{ route('retention.attendance.index') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-calendar-check"></i> Attendance
            </a>

            @if($canEdit)
                <a href="{{ route('retention.sales.create') }}" class="dd-update-btn" style="text-decoration:none">
                    <i class="ti ti-edit"></i> Update Sales
                </a>
            @else
                <div class="dd-readonly-badge"><i class="ti ti-eye"></i> View only</div>
            @endif

            <div class="dd-sync">
                <span class="dd-dot"></span>
                Synced · {{ $lastSyncedAt }}
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.4);color:#34d399;padding:12px 16px;border-radius:10px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
            <i class="ti ti-circle-check" style="font-size:18px"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Target Progress Bus Runner Card (Dialer Dashboard Style) --}}
    @php
        $targetGoal = 100;
        $currentSales = $dailyBoardTotals['m_total_sales'] ?? 0;
        $pct = min(100, round(($currentSales / max(1, $targetGoal)) * 100));
    @endphp
    <div class="dd-track-card {{ $pct >= 100 ? 'is-complete' : '' }}" id="ddTrackCard">
        <div class="dd-confetti" id="ddConfetti"></div>
        <div class="dd-track-head">
            <div>
                <div class="dd-track-eyebrow">Retention Monthly Target</div>
                <div class="dd-track-title">SPD Target: <b>2.5 / Day</b> — Movie Night & 100k Bonus 🍿💰</div>
            </div>
            <div class="dd-track-num">
                <div class="big" id="ddTrackCount">{{ $currentSales }}</div>
                <div class="small">
                    of {{ $targetGoal }} sales this month
                    &nbsp;·&nbsp; SPD of 2.5 Target
                </div>
            </div>
        </div>

        <div class="dd-track">
            <div class="dd-track-line"></div>
            <div class="dd-track-fill" id="ddTrackFill" style="width: {{ $pct }}%"></div>
            <div class="dd-track-shadow" style="left: {{ $pct }}%"></div>
            <div class="dd-track-runner {{ $pct < 100 ? 'flying' : '' }}" style="left: {{ $pct }}%">🚌</div>

            <div class="dd-milestone {{ $pct >= 45 ? 'reached' : '' }}" style="left: 45%">
                <div class="dd-milestone-icon">🎬</div>
                <div class="dd-milestone-dot"></div>
                <div class="dd-milestone-label">Movie Night (SPD 2)</div>
            </div>
            <div class="dd-milestone {{ $pct >= 75 ? 'reached' : '' }}" style="left: 75%">
                <div class="dd-milestone-icon">💰</div>
                <div class="dd-milestone-dot"></div>
                <div class="dd-milestone-label">100k Bonus (SPD 2.5)</div>
            </div>
            <div class="dd-milestone {{ $pct >= 100 ? 'reached' : '' }}" style="left: 100%">
                <div class="dd-milestone-icon">🏆</div>
                <div class="dd-milestone-dot"></div>
                <div class="dd-milestone-label">Target Hit</div>
            </div>
        </div>

        <div class="dd-track-foot">
            <div class="dd-track-msg">
                @if($pct >= 100)
                    <b>Retention Target Hit</b> — Rewards unlocked!
                @else
                    <b>{{ max(0, $targetGoal - $currentSales) }}</b> sales to hit 100 monthly target — keep pushing!
                @endif
            </div>
            <span class="dd-badge-unlocked">🏆 SPD 2.5 Bonus Target</span>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="dd-stats" style="grid-template-columns:repeat(4,1fr);margin-bottom:16px">
        <div class="dd-stat-card">
            <div class="dd-stat-icon"><i class="ti ti-user-check"></i></div>
            <div class="dd-stat-label">Retention Closers</div>
            <div class="dd-stat-value">{{ count($dailyBoard) }}</div>
            <div class="dd-stat-trend dd-trend-up">Active team members</div>
        </div>
        <div class="dd-stat-card">
            <div class="dd-stat-icon"><i class="ti ti-trophy"></i></div>
            <div class="dd-stat-label">Today Total Sales</div>
            <div class="dd-stat-value" style="color:var(--dd-accent)">{{ $dailyBoardTotals['total_sales'] }}</div>
            <div class="dd-stat-trend dd-trend-up">Rewrite, Fixed, Corr & New Policy</div>
        </div>
        <div class="dd-stat-card">
            <div class="dd-stat-icon"><i class="ti ti-circle-check"></i></div>
            <div class="dd-stat-label">Today Level Sales</div>
            <div class="dd-stat-value" style="color:#60a5fa">{{ $dailyBoardTotals['level'] }}</div>
            <div class="dd-stat-trend dd-trend-up">Level {{ $dailyBoardTotals['level_pct'] }}%</div>
        </div>
        <div class="dd-stat-card">
            <div class="dd-stat-icon"><i class="ti ti-chart-pie"></i></div>
            <div class="dd-stat-label">Today GI Sales</div>
            <div class="dd-stat-value" style="color:#c084fc">{{ $dailyBoardTotals['gi'] }}</div>
            <div class="dd-stat-trend dd-trend-up">Guaranteed Issue</div>
        </div>
    </div>

    {{-- Top Closer Hero Card --}}
    @php
        $topCloser = $dailyBoard[0] ?? null;
    @endphp
    @if($topCloser && $topCloser['total_sales'] > 0)
    <div class="dd-top-closer-card" style="margin-bottom:16px">
        <div class="dd-top-closer-trophy">🥇</div>
        <div class="dd-top-closer-info" style="text-align:center">
            <div class="dd-top-closer-label" style="color:#38bdf8">Retention Team</div>
            <div class="dd-top-closer-name" style="font-size:20px">{{ $teamsSummary[0]['team'] ?? 'Retention' }}</div>
            <div class="dd-top-closer-team">{{ $dailyBoardTotals['total_sales'] }} total sales today</div>
        </div>
        <div class="dd-top-closer-divider"></div>
        <div class="dd-top-closer-info" style="text-align:center">
            <div class="dd-top-closer-label">🏆 Top Retention Closer</div>
            <div class="dd-top-closer-name">{{ $topCloser['closer'] }}</div>
            <div class="dd-top-closer-team">{{ $topCloser['team'] }}</div>
        </div>
        <div class="dd-top-closer-stats">
            <div class="dd-top-closer-stat">
                <span class="dd-top-closer-stat-val">{{ $topCloser['total_sales'] }}</span>
                <span class="dd-top-closer-stat-lbl">Sales</span>
            </div>
            <div class="dd-top-closer-stat">
                <span class="dd-top-closer-stat-val">{{ $topCloser['level'] }}</span>
                <span class="dd-top-closer-stat-lbl">Level</span>
            </div>
            <div class="dd-top-closer-stat">
                <span class="dd-top-closer-stat-val">{{ $topCloser['gi'] }}</span>
                <span class="dd-top-closer-stat-lbl">GI</span>
            </div>
            <div class="dd-top-closer-stat">
                <span class="dd-top-closer-stat-val">{{ $topCloser['level_pct'] }}%</span>
                <span class="dd-top-closer-stat-lbl">Level %</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Panel: Daily Board Retention --}}
    <div class="dd-panel">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Daily Board Retention</div>
                <div class="dd-panel-sub">Live retention leaderboard — ranked by total sales today</div>
            </div>
            <form method="GET" action="{{ route('retention.dashboard') }}" style="display:flex;gap:8px;align-items:center">
                <input type="date" name="from" value="{{ $filters['from'] }}" class="dd-input" style="padding:4px 8px">
                <input type="date" name="to" value="{{ $filters['to'] }}" class="dd-input" style="padding:4px 8px">
                <button type="submit" class="dd-apply" style="padding:5px 12px">Filter</button>
            </form>
        </div>

        <div class="dd-table-wrap" style="overflow-x:auto">
            <table class="dd-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Time Since Last Sale</th>
                        <th>Team</th>
                        <th>Closer</th>
                        <th>Rewrite</th>
                        <th>Fixed</th>
                        <th>Correspondence</th>
                        <th>New Policy</th>
                        <th style="color:var(--dd-accent)">Total Sales</th>
                        <th>Level</th>
                        <th>GI</th>
                        <th>Level %</th>
                        <th>Avg Pre</th>
                        <th class="monthly-col" style="color:#60a5fa">MTD Total</th>
                        <th class="monthly-col">MTD Level</th>
                        <th class="monthly-col">MTD GI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyBoard as $i => $row)
                        <tr class="{{ $i < 3 ? 'r'.($i+1) : '' }}">
                            <td><span class="dd-rank-badge">{{ $i+1 }}</span></td>
                            <td>
                                <span class="dd-badge" style="background:rgba(245,158,11,0.15);color:#fbbf24;border:1px solid rgba(245,158,11,0.3)">
                                    {{ $row['time_since_last_sale'] }}
                                </span>
                            </td>
                            <td><span class="dd-badge">{{ $row['team'] }}</span></td>
                            <td>
                                <div class="dd-lb-agent">
                                    <div class="dd-avatar" style="background:linear-gradient(135deg,#059669,#10b981)">
                                        {{ strtoupper(substr($row['closer'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="dd-lb-name" style="font-weight:700">{{ $row['closer'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $row['rewrite'] }}</td>
                            <td>{{ $row['fixed'] }}</td>
                            <td>{{ $row['correspondence'] }}</td>
                            <td>{{ $row['new_policy'] }}</td>
                            <td style="font-weight:800;color:var(--dd-accent);font-size:15px">{{ $row['total_sales'] }}</td>
                            <td>{{ $row['level'] }}</td>
                            <td>{{ $row['gi'] }}</td>
                            <td>{{ $row['level_pct'] }}%</td>
                            <td>${{ number_format($row['avg_pre'], 2) }}</td>

                            <td class="monthly-col" style="font-weight:800;color:#60a5fa">{{ $row['m_total_sales'] }}</td>
                            <td class="monthly-col">{{ $row['m_level'] }}</td>
                            <td class="monthly-col">{{ $row['m_gi'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" style="text-align:center;color:var(--dd-text-muted);padding:24px">
                                No retention sales entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($dailyBoard) > 0)
                <tfoot>
                    <tr style="background:rgba(0,0,0,0.4);font-weight:800">
                        <td colspan="4" style="text-align:right">Total:</td>
                        <td>{{ $dailyBoardTotals['rewrite'] }}</td>
                        <td>{{ $dailyBoardTotals['fixed'] }}</td>
                        <td>{{ $dailyBoardTotals['correspondence'] }}</td>
                        <td>{{ $dailyBoardTotals['new_policy'] }}</td>
                        <td style="color:var(--dd-accent);font-size:16px">{{ $dailyBoardTotals['total_sales'] }}</td>
                        <td>{{ $dailyBoardTotals['level'] }}</td>
                        <td>{{ $dailyBoardTotals['gi'] }}</td>
                        <td>{{ $dailyBoardTotals['level_pct'] }}%</td>
                        <td>-</td>
                        <td class="monthly-col" style="color:#60a5fa;font-size:16px">{{ $dailyBoardTotals['m_total_sales'] }}</td>
                        <td class="monthly-col">{{ $dailyBoardTotals['m_level'] }}</td>
                        <td class="monthly-col">{{ $dailyBoardTotals['m_gi'] }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Bottom Grid: Teams Summary & Clients Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
        {{-- Teams Summary Panel --}}
        <div class="dd-panel">
            <div class="dd-panel-head">
                <div>
                    <div class="dd-panel-title">Retention Teams Summary</div>
                    <div class="dd-panel-sub">Performance per team</div>
                </div>
            </div>
            <div class="dd-table-wrap">
                <table class="dd-table">
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Closers</th>
                            <th>Rewrite & Fixed</th>
                            <th>Level</th>
                            <th>GI</th>
                            <th>Level %</th>
                            <th>SPD</th>
                            <th>MTD SPD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamsSummary as $ts)
                            <tr>
                                <td style="font-weight:700;color:#fff">{{ $ts['team'] }}</td>
                                <td>{{ $ts['closers'] }}</td>
                                <td>{{ $ts['rewrite_fixed'] }}</td>
                                <td>{{ $ts['level'] }}</td>
                                <td>{{ $ts['gi'] }}</td>
                                <td>{{ $ts['level_pct'] }}%</td>
                                <td><span class="dd-badge" style="background:#065f46;color:#34d399">{{ $ts['spd'] }}</span></td>
                                <td><span class="dd-badge" style="background:#1e3a8a;color:#93c5fd">{{ $ts['mtd_spd'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" style="text-align:center;color:var(--dd-text-muted)">No team data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Clients Summary Panel --}}
        <div class="dd-panel">
            <div class="dd-panel-head">
                <div>
                    <div class="dd-panel-title">Retention Clients Summary</div>
                    <div class="dd-panel-sub">Client target & sales breakdown</div>
                </div>
            </div>
            <div class="dd-table-wrap">
                <table class="dd-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>UW</th>
                            <th>Rewrite</th>
                            <th>Fixed</th>
                            <th>Corr</th>
                            <th>New Policy</th>
                            <th>Level</th>
                            <th>GI</th>
                            <th>Target</th>
                            <th>Left</th>
                            <th>Avg Pre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientsSummary as $cs)
                            <tr>
                                <td style="font-weight:700;color:var(--dd-accent)">{{ $cs['client'] }}</td>
                                <td>{{ $cs['underwriting'] }}</td>
                                <td>{{ $cs['rewrite'] }}</td>
                                <td>{{ $cs['fixed'] }}</td>
                                <td>{{ $cs['correspondence'] }}</td>
                                <td>{{ $cs['new_policy'] }}</td>
                                <td>{{ $cs['level'] }}</td>
                                <td>{{ $cs['gi'] }}</td>
                                <td>{{ $cs['target'] }}</td>
                                <td style="color:#ef4444;font-weight:700">{{ $cs['left'] }}</td>
                                <td>${{ number_format($cs['avg_pre'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="11" style="text-align:center;color:var(--dd-text-muted)">No client data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


@endsection

