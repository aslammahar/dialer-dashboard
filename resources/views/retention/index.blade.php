@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Retention Dashboard')

@push('css-page')
@include('dialer-dashboard._styles')
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
:root {
    --ret-bg: #080c11;
    --ret-surface: #0f1520;
    --ret-surface2: #141c28;
    --ret-border: rgba(255,255,255,.07);
    --ret-accent: #34f5c5;
    --ret-accent2: #60a5fa;
    --ret-purple: #c084fc;
    --ret-text: #e8f0f7;
    --ret-sec: #7a8fa0;
    --ret-muted: #3d5060;
    --ret-font: 'Inter', sans-serif;
    --ret-mono: 'JetBrains Mono', monospace;
    --ret-display: 'Space Grotesk', sans-serif;
}

/* ── Topbar ─────────────────────────────────────── */
.ret-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    background: var(--ret-surface);
    border: 1px solid var(--ret-border);
    border-radius: 16px;
    padding: 12px 20px;
    margin-bottom: 18px;
}
.ret-brand { display: flex; align-items: center; gap: 12px; }
.ret-brand-icon {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, #059669, var(--ret-accent));
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 16px rgba(52,245,197,.25);
}
.ret-brand-icon svg { width: 22px; height: 22px; }
.ret-brand-title { font-family: var(--ret-display); font-weight: 700; font-size: 17px; color: var(--ret-text); }
.ret-brand-sub { font-size: 11px; color: var(--ret-sec); }
.ret-nav { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.ret-nav-btn {
    display: inline-flex; align-items: center; gap: 5px;
    background: transparent; border: 1px solid var(--ret-border);
    color: var(--ret-sec); border-radius: 8px;
    padding: 5px 11px; font-size: 12px; font-family: var(--ret-font);
    cursor: pointer; text-decoration: none;
    transition: border-color .15s, color .15s, background .15s;
    white-space: nowrap;
}
.ret-nav-btn:hover { border-color: var(--ret-accent); color: var(--ret-accent); background: rgba(52,245,197,.06); }
.ret-nav-btn.active { border-color: var(--ret-accent); color: var(--ret-accent); background: rgba(52,245,197,.10); }
.ret-edit-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--ret-accent); color: #06231b;
    border: none; border-radius: 8px;
    padding: 6px 14px; font-size: 12.5px; font-weight: 700;
    font-family: var(--ret-font); cursor: pointer; text-decoration: none;
    transition: transform .15s, box-shadow .15s;
    white-space: nowrap;
}
.ret-edit-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(52,245,197,.35); }
.ret-time {
    display: flex; align-items: center; gap: 6px;
    font-size: 11.5px; color: var(--ret-sec);
    background: rgba(255,255,255,.04); border: 1px solid var(--ret-border);
    border-radius: 8px; padding: 4px 10px; white-space: nowrap;
}
.ret-time-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--ret-accent); box-shadow: 0 0 6px var(--ret-accent); animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

/* ── Stat Cards ─────────────────────────────────── */
.ret-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 18px;
}
.ret-stat {
    background: var(--ret-surface);
    border: 1px solid var(--ret-border);
    border-radius: 14px;
    padding: 16px 18px;
    position: relative; overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.ret-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.3); }
.ret-stat::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 2px; border-radius: 14px 14px 0 0;
    background: var(--stat-color, var(--ret-accent));
    opacity: .6;
}
.ret-stat-icon { font-size: 22px; margin-bottom: 8px; }
.ret-stat-label { font-size: 11px; color: var(--ret-sec); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
.ret-stat-val { font-family: var(--ret-display); font-weight: 700; font-size: 28px; color: var(--stat-color, var(--ret-accent)); line-height: 1; }
.ret-stat-sub { font-size: 11px; color: var(--ret-muted); margin-top: 4px; }

/* ── Target Bar ─────────────────────────────────── */
.ret-target { background: var(--ret-surface); border: 1px solid var(--ret-border); border-radius: 14px; padding: 18px 22px; margin-bottom: 18px; }
.ret-target-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
.ret-target-title { font-family: var(--ret-display); font-size: 15px; font-weight: 700; color: var(--ret-text); }
.ret-target-nums { font-size: 13px; color: var(--ret-sec); }
.ret-target-nums b { color: var(--ret-accent); }
.ret-bar-wrap { position: relative; height: 10px; background: rgba(255,255,255,.06); border-radius: 99px; overflow: visible; }
.ret-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #059669, var(--ret-accent)); box-shadow: 0 0 10px rgba(52,245,197,.4); transition: width .8s ease; }
.ret-bar-runner { position: absolute; top: 50%; transform: translate(-50%,-50%); font-size: 18px; pointer-events: none; filter: drop-shadow(0 0 6px rgba(52,245,197,.5)); }
.ret-target-foot { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 12px; color: var(--ret-sec); }
.ret-target-foot b { color: var(--ret-text); }

/* ── Daily Board Panel ──────────────────────────── */
.ret-panel {
    background: var(--ret-surface);
    border: 1px solid var(--ret-border);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 18px;
}
.ret-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid var(--ret-border);
    gap: 12px; flex-wrap: wrap;
}
.ret-panel-title { font-family: var(--ret-display); font-weight: 700; font-size: 15px; color: var(--ret-text); }
.ret-panel-sub { font-size: 11.5px; color: var(--ret-sec); margin-top: 2px; }
.ret-filter { display: flex; gap: 6px; align-items: center; }
.ret-filter input[type=date] {
    background: var(--ret-surface2); border: 1px solid var(--ret-border);
    color: var(--ret-text); border-radius: 8px; padding: 5px 9px;
    font-size: 12px; font-family: var(--ret-font);
}
.ret-filter button {
    background: var(--ret-accent); color: #06231b;
    border: none; border-radius: 8px; padding: 5px 14px;
    font-size: 12px; font-weight: 700; cursor: pointer;
}

/* ── Table ──────────────────────────────────────── */
.ret-table-wrap { overflow-x: auto; }
.ret-table { width: 100%; border-collapse: collapse; font-family: var(--ret-font); }
.ret-table th {
    font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em;
    color: var(--ret-sec); text-align: right;
    padding: 9px 12px; background: rgba(0,0,0,.2);
    border-bottom: 1px solid var(--ret-border);
    white-space: nowrap; font-weight: 600;
}
.ret-table th:first-child, .ret-table th:nth-child(2), .ret-table th:nth-child(3), .ret-table th:nth-child(4) { text-align: left; }
.ret-table td {
    padding: 8px 12px; font-size: 12.5px;
    border-bottom: 1px solid rgba(255,255,255,.04);
    color: var(--ret-text); text-align: right;
    font-family: var(--ret-mono); white-space: nowrap;
}
.ret-table td:first-child, .ret-table td:nth-child(2), .ret-table td:nth-child(3), .ret-table td:nth-child(4) { text-align: left; font-family: var(--ret-font); }
.ret-table tbody tr:hover { background: rgba(255,255,255,.03); }
.ret-table tfoot td { font-weight: 700; background: rgba(0,0,0,.3); border-top: 1px solid var(--ret-border); }
.ret-mtd-col { background: rgba(30,58,138,.2) !important; color: var(--ret-accent2) !important; }
.ret-mtd-th { background: rgba(30,58,138,.3) !important; color: var(--ret-accent2) !important; }

.ret-rank { width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,.08); color: var(--ret-sec); font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; font-family: var(--ret-mono); }
.ret-rank.r1 { background: rgba(245,197,52,.2); color: #f5c534; }
.ret-rank.r2 { background: rgba(148,163,184,.15); color: #94a3b8; }
.ret-rank.r3 { background: rgba(180,120,60,.15); color: #cd7f32; }
.ret-avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #059669, #10b981); color: #fff; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ret-closer-cell { display: flex; align-items: center; gap: 8px; }
.ret-closer-name { font-weight: 600; color: var(--ret-text); font-size: 13px; font-family: var(--ret-font); }
.ret-team-badge { display: inline-block; background: rgba(52,245,197,.1); color: var(--ret-accent); border: 1px solid rgba(52,245,197,.25); border-radius: 6px; font-size: 10.5px; padding: 2px 7px; font-family: var(--ret-font); }
.ret-timer-badge { display: inline-block; background: rgba(245,158,11,.1); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); border-radius: 6px; font-size: 11px; padding: 2px 8px; font-family: var(--ret-mono); }

/* ── Bottom Grid ────────────────────────────────── */
.ret-bottom { display: grid; grid-template-columns: 1fr 1.6fr; gap: 14px; margin-top: 18px; }
@media(max-width:900px){ .ret-bottom { grid-template-columns:1fr; } .ret-stats { grid-template-columns: repeat(3, 1fr); } }
</style>
@endpush

@section('content')
<div style="background:var(--ret-bg);min-height:100vh;padding:18px 20px;font-family:var(--ret-font)">

    {{-- ── Topbar ── --}}
    <div class="ret-topbar">
        <div class="ret-brand">
            <div class="ret-brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>
                </svg>
            </div>
            <div>
                <div class="ret-brand-title">Retention Dashboard</div>
                <div class="ret-brand-sub">Retention Department · Live Sales &amp; Performance</div>
            </div>
        </div>

        <div class="ret-nav">
            <div class="ret-time">
                <div class="ret-time-dot"></div>
                <span>{{ now('America/New_York')->format('h:i A') }} EST</span>
            </div>
            <a href="{{ route('retention.leaderboard') }}" class="ret-nav-btn"><i class="ti ti-trophy"></i> Leaderboard</a>
            <a href="{{ route('retention.reports.team-wise') }}" class="ret-nav-btn"><i class="ti ti-users"></i> Team Wise</a>
            <a href="{{ route('retention.reports.client-wise') }}" class="ret-nav-btn"><i class="ti ti-briefcase"></i> Client Wise</a>
            <a href="{{ route('retention.reports.carrier-wise') }}" class="ret-nav-btn"><i class="ti ti-truck"></i> Carrier Wise</a>
            <a href="{{ route('retention.report') }}" class="ret-nav-btn"><i class="ti ti-report"></i> Report</a>
            <a href="{{ route('retention.attendance.index') }}" class="ret-nav-btn"><i class="ti ti-calendar-check"></i> Attendance</a>
            @if($canEdit)
                <a href="{{ route('retention.sales.create') }}" class="ret-edit-btn"><i class="ti ti-edit"></i> Update Sales</a>
            @else
                <span class="ret-nav-btn"><i class="ti ti-eye"></i> View only</span>
            @endif
        </div>
    </div>

    {{-- ── Success Banner ── --}}
    @if(session('success'))
    <div style="background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--ret-accent);padding:10px 16px;border-radius:10px;margin-bottom:14px;display:flex;align-items:center;gap:8px;font-size:13px;">
        <i class="ti ti-circle-check" style="font-size:16px"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ── Stat Cards ── --}}
    @php
        $activeToday = collect($dailyBoard)->filter(fn($r) => $r['total_sales'] > 0)->count();
    @endphp
    <div class="ret-stats">
        <div class="ret-stat" style="--stat-color:var(--ret-accent)">
            <div class="ret-stat-icon">👥</div>
            <div class="ret-stat-label">Team Closers</div>
            <div class="ret-stat-val">{{ count($dailyBoard) }}</div>
            <div class="ret-stat-sub">{{ $activeToday }} active today</div>
        </div>
        <div class="ret-stat" style="--stat-color:var(--ret-accent)">
            <div class="ret-stat-icon">🏆</div>
            <div class="ret-stat-label">Today Sales</div>
            <div class="ret-stat-val">{{ $dailyBoardTotals['total_sales'] }}</div>
            <div class="ret-stat-sub">All types combined</div>
        </div>
        <div class="ret-stat" style="--stat-color:var(--ret-accent2)">
            <div class="ret-stat-icon">📊</div>
            <div class="ret-stat-label">MTD Total</div>
            <div class="ret-stat-val">{{ $dailyBoardTotals['m_total_sales'] }}</div>
            <div class="ret-stat-sub">Month to date</div>
        </div>
        <div class="ret-stat" style="--stat-color:#60a5fa">
            <div class="ret-stat-icon">⚡</div>
            <div class="ret-stat-label">MTD Level</div>
            <div class="ret-stat-val">{{ $dailyBoardTotals['m_level'] }}</div>
            <div class="ret-stat-sub">Level sales MTD</div>
        </div>
        <div class="ret-stat" style="--stat-color:var(--ret-purple)">
            <div class="ret-stat-icon">💎</div>
            <div class="ret-stat-label">MTD GI</div>
            <div class="ret-stat-val">{{ $dailyBoardTotals['m_gi'] }}</div>
            <div class="ret-stat-sub">Guaranteed issue MTD</div>
        </div>
    </div>

    {{-- ── Target Progress Bar ── --}}
    @php
        $targetGoal   = 100;
        $currentSales = $dailyBoardTotals['m_total_sales'] ?? 0;
        $pct          = min(100, round(($currentSales / max(1, $targetGoal)) * 100));
    @endphp
    <div class="ret-target">
        <div class="ret-target-head">
            <div>
                <div class="ret-target-title">🚌 Monthly Target Tracker — SPD 2.5 Goal</div>
                <div style="font-size:12px;color:var(--ret-sec);margin-top:3px;">Movie Night (SPD 2) &nbsp;·&nbsp; 100k Bonus (SPD 2.5) &nbsp;·&nbsp; Target (100)</div>
            </div>
            <div class="ret-target-nums">
                <b>{{ $currentSales }}</b> / {{ $targetGoal }} sales &nbsp;·&nbsp; <b>{{ $pct }}%</b>
            </div>
        </div>
        <div class="ret-bar-wrap">
            <div class="ret-bar-fill" style="width:{{ $pct }}%"></div>
            <div class="ret-bar-runner" style="left:{{ $pct }}%">🚌</div>
            <div style="position:absolute;left:45%;top:-20px;font-size:11px;color:#fbbf24;transform:translateX(-50%)">🎬</div>
            <div style="position:absolute;left:75%;top:-20px;font-size:11px;color:#f59e0b;transform:translateX(-50%)">💰</div>
            <div style="position:absolute;left:100%;top:-20px;font-size:11px;transform:translateX(-50%)">🏆</div>
        </div>
        <div class="ret-target-foot">
            <span>
                @if($pct >= 100) <b>🎉 Target Hit!</b> @else <b>{{ max(0, $targetGoal - $currentSales) }}</b> more to hit 100 MTD @endif
            </span>
            <span>🏅 SPD 2.5 Bonus Unlocked at 100</span>
        </div>
    </div>

    {{-- ── Daily Board ── --}}
    <div class="ret-panel">
        <div class="ret-panel-head">
            <div>
                <div class="ret-panel-title">Daily Board Retention</div>
                <div class="ret-panel-sub">Live retention leaderboard — ranked by total sales today</div>
            </div>
            <form method="GET" action="{{ route('retention.dashboard') }}" class="ret-filter">
                <input type="date" name="from" value="{{ $filters['from'] }}">
                <input type="date" name="to"   value="{{ $filters['to'] }}">
                <button type="submit">Filter</button>
            </form>
        </div>

        <div class="ret-table-wrap">
            <table class="ret-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Timer</th>
                        <th>Team</th>
                        <th>Closer</th>
                        <th>Rewrite</th>
                        <th>Fixed</th>
                        <th>Corr</th>
                        <th>New Policy</th>
                        <th style="color:var(--ret-accent)">Total</th>
                        <th>Level</th>
                        <th>GI</th>
                        <th>Level%</th>
                        <th>Avg Pre</th>
                        <th class="ret-mtd-th">MTD Total</th>
                        <th class="ret-mtd-th">MTD Lvl</th>
                        <th class="ret-mtd-th">MTD GI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyBoard as $i => $row)
                    <tr>
                        <td><span class="ret-rank {{ $i < 3 ? 'r'.($i+1) : '' }}">{{ $i+1 }}</span></td>
                        <td><span class="ret-timer-badge">{{ $row['time_since_last_sale'] }}</span></td>
                        <td><span class="ret-team-badge">{{ $row['team'] }}</span></td>
                        <td>
                            <div class="ret-closer-cell">
                                <div class="ret-avatar">{{ strtoupper(substr($row['closer'], 0, 2)) }}</div>
                                <span class="ret-closer-name">{{ $row['closer'] }}</span>
                            </div>
                        </td>
                        <td>{{ $row['rewrite'] }}</td>
                        <td>{{ $row['fixed'] }}</td>
                        <td>{{ $row['correspondence'] }}</td>
                        <td>{{ $row['new_policy'] }}</td>
                        <td style="font-weight:800;color:var(--ret-accent);font-size:15px">{{ $row['total_sales'] }}</td>
                        <td>{{ $row['level'] }}</td>
                        <td>{{ $row['gi'] }}</td>
                        <td>{{ $row['level_pct'] }}%</td>
                        <td style="color:var(--ret-accent)">${{ number_format($row['avg_pre'], 2) }}</td>
                        <td class="ret-mtd-col" style="font-weight:800">{{ $row['m_total_sales'] }}</td>
                        <td class="ret-mtd-col">{{ $row['m_level'] }}</td>
                        <td class="ret-mtd-col">{{ $row['m_gi'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="16" style="text-align:center;color:var(--ret-muted);padding:28px">No retention sales entries found.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($dailyBoard) > 0)
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;color:var(--ret-sec);font-family:var(--ret-font)">Total ↓</td>
                        <td>{{ $dailyBoardTotals['rewrite'] }}</td>
                        <td>{{ $dailyBoardTotals['fixed'] }}</td>
                        <td>{{ $dailyBoardTotals['correspondence'] }}</td>
                        <td>{{ $dailyBoardTotals['new_policy'] }}</td>
                        <td style="color:var(--ret-accent);font-size:15px">{{ $dailyBoardTotals['total_sales'] }}</td>
                        <td>{{ $dailyBoardTotals['level'] }}</td>
                        <td>{{ $dailyBoardTotals['gi'] }}</td>
                        <td>{{ $dailyBoardTotals['level_pct'] }}%</td>
                        <td>–</td>
                        <td class="ret-mtd-col" style="font-size:15px">{{ $dailyBoardTotals['m_total_sales'] }}</td>
                        <td class="ret-mtd-col">{{ $dailyBoardTotals['m_level'] }}</td>
                        <td class="ret-mtd-col">{{ $dailyBoardTotals['m_gi'] }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── Bottom Grid: Teams + Clients ── --}}
    <div class="ret-bottom">
        {{-- Teams Summary --}}
        <div class="ret-panel" style="margin-bottom:0">
            <div class="ret-panel-head">
                <div>
                    <div class="ret-panel-title">Retention Teams Summary</div>
                    <div class="ret-panel-sub">Performance per team</div>
                </div>
            </div>
            <div class="ret-table-wrap">
                <table class="ret-table">
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Closers</th>
                            <th>Rw &amp; Fixed</th>
                            <th>Level</th>
                            <th>GI</th>
                            <th>Lvl%</th>
                            <th>SPD</th>
                            <th>MTD SPD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamsSummary as $ts)
                        <tr>
                            <td style="font-weight:700;color:var(--ret-text);font-family:var(--ret-font)">{{ $ts['team'] }}</td>
                            <td>{{ $ts['closers'] }}</td>
                            <td>{{ $ts['rewrite_fixed'] }}</td>
                            <td>{{ $ts['level'] }}</td>
                            <td>{{ $ts['gi'] }}</td>
                            <td>{{ $ts['level_pct'] }}%</td>
                            <td><span style="background:#065f46;color:#34d399;border-radius:6px;padding:2px 8px;font-size:11px">{{ $ts['spd'] }}</span></td>
                            <td><span style="background:#1e3a8a;color:#93c5fd;border-radius:6px;padding:2px 8px;font-size:11px">{{ $ts['mtd_spd'] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;color:var(--ret-muted);padding:20px">No team data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Clients Summary --}}
        <div class="ret-panel" style="margin-bottom:0">
            <div class="ret-panel-head">
                <div>
                    <div class="ret-panel-title">Retention Clients Summary</div>
                    <div class="ret-panel-sub">Client target &amp; sales breakdown</div>
                </div>
            </div>
            <div class="ret-table-wrap">
                <table class="ret-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>UW</th>
                            <th>Rewrite</th>
                            <th>Fixed</th>
                            <th>Corr</th>
                            <th>New Pol</th>
                            <th>Level</th>
                            <th>GI</th>
                            <th>Target</th>
                            <th style="color:#ef4444">Left</th>
                            <th>Avg Pre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientsSummary as $cs)
                        <tr>
                            <td style="font-weight:700;color:var(--ret-accent);font-family:var(--ret-font)">{{ $cs['client'] }}</td>
                            <td>{{ $cs['underwriting'] }}</td>
                            <td>{{ $cs['rewrite'] }}</td>
                            <td>{{ $cs['fixed'] }}</td>
                            <td>{{ $cs['correspondence'] }}</td>
                            <td>{{ $cs['new_policy'] }}</td>
                            <td>{{ $cs['level'] }}</td>
                            <td>{{ $cs['gi'] }}</td>
                            <td>{{ $cs['target'] }}</td>
                            <td style="color:#ef4444;font-weight:700">{{ $cs['left'] }}</td>
                            <td style="color:var(--ret-accent)">${{ number_format($cs['avg_pre'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="11" style="text-align:center;color:var(--ret-muted);padding:20px">No client data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
