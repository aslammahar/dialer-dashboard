@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Team Wise Closers')

@push('css-page')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --tw-bg:#090d12; --tw-surface:#11161d; --tw-surface-alt:#171f29;
        --tw-border:rgba(255,255,255,.07); --tw-text:#f3f6f7; --tw-text-sec:#93a2ac;
        --tw-text-muted:#586872; --tw-accent:#34f5c5;
        --tw-font-display:'Space Grotesk',sans-serif; --tw-font-body:'Inter',sans-serif; --tw-font-mono:'JetBrains Mono',monospace;
    }
    .tw-wrap{background:var(--tw-bg);color:var(--tw-text);font-family:var(--tw-font-body);padding:28px;border-radius:18px;min-height:100vh}
    .tw-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
    .tw-title{font-family:var(--tw-font-display);font-weight:700;font-size:20px}
    .tw-month-form{display:flex;gap:8px;align-items:center}
    .tw-input{background:var(--tw-surface);border:1px solid var(--tw-border);color:var(--tw-text);border-radius:9px;padding:8px 12px;font-size:13px}
    .tw-apply{background:var(--tw-accent);color:#06231b;border:none;border-radius:9px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer}

    .tw-team-card{background:var(--tw-surface);border:1px solid var(--tw-border);border-radius:14px;padding:20px;margin-bottom:20px}
    .tw-team-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
    .tw-team-name{font-family:var(--tw-font-display);font-size:16px;font-weight:700}
    .tw-team-summary{font-size:12px;color:var(--tw-text-muted)}
    .tw-team-summary b{color:var(--tw-accent)}

    .tw-scroll{overflow-x:auto}
    .tw-table{width:100%;border-collapse:collapse;min-width:960px}
    .tw-table th{font-size:10.5px;text-transform:uppercase;color:var(--tw-text-muted);text-align:right;padding:0 10px 10px;font-weight:600;white-space:nowrap}
    .tw-table th:first-child{text-align:left}
    .tw-table td{padding:8px 10px;font-size:12.5px;border-bottom:1px solid var(--tw-border);text-align:right;font-family:var(--tw-font-mono);white-space:nowrap}
    .tw-table td:first-child{text-align:left;font-family:var(--tw-font-body);font-weight:500}
    .tw-table tfoot td{font-weight:600;background:var(--tw-surface-alt);border-top:1px solid var(--tw-border)}
    .tw-table tfoot td:first-child{text-align:left;font-family:var(--tw-font-body)}
</style>
@endpush

@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--tw-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Leaderboard
</a>
<div class="tw-wrap">
    <div class="tw-head">
        <div class="tw-title">👥 Team Wise Closers</div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a href="{{ route('sales-reports.team-wise', ['range' => 'daily']) }}" class="tw-apply" style="{{ $range == 'daily' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Daily</a>
            <a href="{{ route('sales-reports.team-wise', ['range' => 'weekly']) }}" class="tw-apply" style="{{ $range == 'weekly' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Weekly</a>
            <a href="{{ route('sales-reports.team-wise', ['range' => 'monthly']) }}" class="tw-apply" style="{{ $range == 'monthly' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Monthly</a>

            @if($range == 'monthly')
            <form class="tw-month-form" method="GET" action="{{ route('sales-reports.team-wise') }}">
                <input type="hidden" name="range" value="monthly">
                <input type="month" name="month" class="tw-input" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
                <button type="submit" class="tw-apply">View</button>
            </form>
            @endif
        </div>
    </div>

    @if(session('status'))
        <div style="background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--tw-accent);
            border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px">✓ {{ session('status') }}</div>
    @endif

    @if($canEdit)
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
        <form method="POST" action="{{ route('sales-teams.store') }}" class="tw-team-card" style="display:flex;gap:8px;align-items:end;margin-bottom:0">
            @csrf
            <div style="flex:1">
                <label style="font-size:11px;color:var(--tw-text-muted);text-transform:uppercase">Create Team</label>
                <input type="text" name="name" class="tw-input" style="width:100%" placeholder="e.g. Night Shift" required>
            </div>
            <button type="submit" class="tw-apply">Create</button>
        </form>

        <form method="POST" action="{{ route('sales-closers.store') }}" class="tw-team-card" style="display:flex;gap:8px;align-items:end;margin-bottom:0">
            @csrf
            @if($errors->any())
    <div style="background:rgba(255,90,90,.1);border:1px solid rgba(255,90,90,.3);color:#ff5a5a;
        border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px">
        @foreach($errors->all() as $e){{ $e }}@endforeach
    </div>
@endif
            <div style="flex:1">
                <label style="font-size:11px;color:var(--tw-text-muted);text-transform:uppercase">Add Closer</label>
                <input type="text" name="name" class="tw-input" style="width:100%" placeholder="Closer name" required>
            </div>
            <div style="flex:1">
                <label style="font-size:11px;color:var(--tw-text-muted);text-transform:uppercase">Team</label>
                <select name="sales_team_id" class="tw-input" style="width:100%">
                    <option value="">— None —</option>
                    @foreach(\App\Models\SalesTeam::orderBy('name')->get() as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="tw-apply">Add</button>
        </form>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-bottom:24px">
    @foreach($teamBoxes as $box)
        <div class="tw-team-card">
            <div class="tw-team-name" style="margin-bottom:2px">{{ $box['team'] }}</div>
            <div class="tw-team-summary" style="margin-bottom:12px">{{ $box['total_approved'] }} sales this month</div>
            @forelse($box['closers'] as $c)
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;font-size:12.5px;padding:7px 0;border-bottom:1px solid var(--tw-border)">
                    <span>{{ $c['name'] }}</span>
                    <span style="font-family:var(--tw-font-mono);color:var(--tw-text-sec)">{{ $c['approved'] }} · {{ $c['level_pct'] }}%</span>
                    @if($canEdit)
                    <form method="POST" action="{{ route('sales-closers.update', $c['id']) }}" style="margin:0">
                        @csrf @method('PUT')
                        <select name="sales_team_id" onchange="this.form.submit()" style="background:var(--tw-surface-alt);border:1px solid var(--tw-border);color:var(--tw-text-sec);border-radius:6px;padding:3px 6px;font-size:11px;max-width:110px">
                            <option value="">— None —</option>
                            @foreach($allTeams as $t)
                                <option value="{{ $t->id }}" {{ $box['team'] === $t->name ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                </div>
            @empty
                <div style="color:var(--tw-text-muted);font-size:12px">No closers in this team.</div>
            @endforelse
        </div>
    @endforeach
</div>
    @endif

    @forelse($teams as $team)
        <div class="tw-team-card">
            <div class="tw-team-head">
                <div class="tw-team-name">{{ $team['name'] }}</div>
                <div class="tw-team-summary">
    <b>{{ $team['totals']['mtd'] }}</b> MTD &nbsp;·&nbsp;
    Total Closers <b>{{ $team['total_closers'] ?? count($team['closers']) }}</b> &nbsp;·&nbsp;
    Team SPD <b>{{ $team['averages']['spd'] }}</b> &nbsp;·&nbsp;
    Level% <b>{{ $team['averages']['level_pct'] }}%</b> &nbsp;·&nbsp;
    Conv <b>{{ $team['totals']['conversion'] ?? 0 }}</b>
</div>
            </div>

            <div class="tw-scroll">
                <table class="tw-table">
                    <thead>
                        <tr>
                            <th>Closer</th>
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
                            @foreach($clients as $clientName)
                                <th>{{ $clientName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($team['closers'] as $closer)
                            <tr>
                                <td>{{ $closer['closer'] }}</td>
                                <td>{{ $closer['working_days'] }}</td>
                                <td>{{ $closer['mtd'] }}</td>
                                <td>{{ $closer['spd'] }}</td>
                                <td>{{ $closer['level'] }}</td>
                                <td>{{ $closer['gi'] }}</td>
                                <td>{{ $closer['level_pct'] }}%</td>
                                <td>{{ $closer['avg_pre'] }}</td>
                                <td>{{ $closer['avatar_calls'] ?? 0 }}</td>
                                <td>{{ $closer['conversion'] ?? 0 }}</td>
                                <td>{{ $closer['avg_talk_time'] ?? '-' }}</td>
                                @foreach($clients as $clientName)
                                    <td>{{ $closer['clients'][$clientName] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ 11 + count($clients) }}" style="text-align:center;color:var(--tw-text-muted);padding:16px">No closers in this team for this selection.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
    <tr>
        <td>Total / Avg</td>
        <td>{{ $team['averages']['working_days'] }}</td>
        <td>{{ $team['totals']['mtd'] }}</td>
        <td>{{ $team['averages']['spd'] }}</td>
        <td>{{ $team['totals']['level'] }}</td>
        <td>{{ $team['totals']['gi'] }}</td>
        <td>{{ $team['averages']['level_pct'] }}%</td>
        <td>{{ $team['averages']['avg_pre'] }}</td>
        <td>{{ $team['totals']['calls'] ?? 0 }}</td>
        <td>{{ $team['totals']['conversion'] ?? 0 }}</td>
        <td>{{ $team['totals']['avg_talk_time'] ?? '-' }}</td>
        @foreach($clients as $clientName)
            <td>
                {{ $team['totals']['clients'][$clientName]['total'] ?? 0 }}
                <span style="color:var(--tw-text-muted);font-size:10.5px">(avg {{ $team['totals']['clients'][$clientName]['avg'] ?? 0 }})</span>
            </td>
        @endforeach
    </tr>
</tfoot>
                </table>
            </div>
        </div>
    @empty
        <div class="tw-team-card" style="text-align:center;color:var(--tw-text-muted)">No data for this selection.</div>
    @endforelse
</div>
@endsection