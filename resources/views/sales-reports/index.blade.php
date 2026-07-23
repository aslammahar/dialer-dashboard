@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Sales Reports')

@push('css-page')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --rp-bg:#090d12; --rp-surface:#11161d; --rp-surface-alt:#171f29;
        --rp-border:rgba(255,255,255,.07); --rp-text:#f3f6f7; --rp-text-sec:#93a2ac;
        --rp-text-muted:#586872; --rp-accent:#34f5c5;
        --rp-font-display:'Space Grotesk',sans-serif; --rp-font-body:'Inter',sans-serif; --rp-font-mono:'JetBrains Mono',monospace;
    }
    .rp-wrap{background:var(--rp-bg);color:var(--rp-text);font-family:var(--rp-font-body);padding:28px;border-radius:18px;min-height:100vh}
    .rp-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
    .rp-title{font-family:var(--rp-font-display);font-weight:700;font-size:20px}
    .rp-month-form{display:flex;gap:8px;align-items:center}
    .rp-input{background:var(--rp-surface);border:1px solid var(--rp-border);color:var(--rp-text);border-radius:9px;padding:8px 12px;font-size:13px}
    .rp-apply{background:var(--rp-accent);color:#06231b;border:none;border-radius:9px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer}
    .rp-panel{background:var(--rp-surface);border:1px solid var(--rp-border);border-radius:14px;padding:20px;margin-bottom:20px}
    .rp-panel-title{font-family:var(--rp-font-display);font-size:15px;font-weight:700;margin-bottom:14px}
    .rp-table{width:100%;border-collapse:collapse}
    .rp-table th{font-size:10.5px;text-transform:uppercase;color:var(--rp-text-muted);text-align:right;padding:0 10px 10px;font-weight:600}
    .rp-table th:first-child,.rp-table th:nth-child(2){text-align:left}
    .rp-table td{padding:9px 10px;font-size:12.5px;border-bottom:1px solid var(--rp-border);text-align:right;font-family:var(--rp-font-mono)}
    .rp-table td:first-child,.rp-table td:nth-child(2){text-align:left;font-family:var(--rp-font-body)}
    .rp-scroll{overflow-x:auto}
    .rp-alert{background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--rp-accent);
        border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px}
    .rp-add-form{display:flex;gap:8px;align-items:end;margin-bottom:0}
    .rp-add-form label{font-size:11px;color:var(--rp-text-muted);text-transform:uppercase;display:block;margin-bottom:4px}
</style>
@endpush

@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--as-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Leaderboard
</a>
<div class="rp-wrap">
    <div class="rp-head">
        <div class="rp-title">📊 Monthly Sales Reports</div>
        <form class="rp-month-form" method="GET" action="{{ route('sales-reports.index') }}">
            <input type="month" name="month" class="rp-input" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
            <button type="submit" class="rp-apply">View</button>
        </form>
    </div>

    @if(session('status'))
        <div class="rp-alert">✓ {{ session('status') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
        <form method="POST" action="{{ route('sales-clients.store') }}" class="rp-panel rp-add-form">
            @csrf
            <div style="flex:1">
                <label>Add Client</label>
                <input type="text" name="name" class="rp-input" style="width:100%" placeholder="e.g. D6" required>
            </div>
            <button type="submit" class="rp-apply">Add</button>
        </form>

        <form method="POST" action="{{ route('sales-carriers.store') }}" class="rp-panel rp-add-form">
            @csrf
            <div style="flex:1">
                <label>Add Carrier</label>
                <input type="text" name="name" class="rp-input" style="width:100%" placeholder="e.g. AFLAC" required>
            </div>
            <button type="submit" class="rp-apply">Add</button>
        </form>
    </div>

    <div class="rp-panel">
        <div class="rp-panel-title">Closers — {{ \Carbon\Carbon::parse($month)->format('F Y') }}</div>
        <div class="rp-scroll">
            <table class="rp-table">
                <thead>
                    <tr><th>Closer</th><th>Team</th><th>Approved</th><th>Level</th><th>GI</th><th>Level %</th><th>Avg Pre</th><th>MTD SPD</th></tr>
                </thead>
                <tbody>
                    @forelse($closersReport as $row)
                        <tr>
                            <td>{{ $row['closer'] }}</td>
                            <td>{{ $row['team'] }}</td>
                            <td>{{ $row['approved'] }}</td>
                            <td>{{ $row['level'] }}</td>
                            <td>{{ $row['gi'] }}</td>
                            <td>{{ $row['level_pct'] }}%</td>
                            <td>{{ $row['avg_pre'] }}</td>
                            <td>{{ $row['mtd_spd'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;color:var(--rp-text-muted);padding:20px">No data for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rp-panel">
        <div class="rp-panel-title">Clients</div>
        <div class="rp-scroll">
            <table class="rp-table">
                <thead><tr><th>Client</th><th>Approved</th><th>Level</th><th>GI</th><th>Avg Pre</th></tr></thead>
                <tbody>
                    @forelse($clientsReport as $row)
                        <tr>
                            <td>{{ $row['client'] }}</td>
                            <td>{{ $row['approved'] }}</td>
                            <td>{{ $row['level'] }}</td>
                            <td>{{ $row['gi'] }}</td>
                            <td>{{ $row['avg_pre'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--rp-text-muted);padding:20px">No data for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rp-panel">
        <div class="rp-panel-title">Carriers</div>
        <div class="rp-scroll">
            <table class="rp-table">
                <thead><tr><th>Carrier</th><th>Approved</th><th>Level</th><th>GI</th><th>Avg Pre</th></tr></thead>
                <tbody>
                    @forelse($carriersReport as $row)
                        <tr>
                            <td>{{ $row['carrier'] }}</td>
                            <td>{{ $row['approved'] }}</td>
                            <td>{{ $row['level'] }}</td>
                            <td>{{ $row['gi'] }}</td>
                            <td>{{ $row['avg_pre'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--rp-text-muted);padding:20px">No data for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rp-panel">
        <div class="rp-panel-title">Teams</div>
        <div class="rp-scroll">
            <table class="rp-table">
                <thead><tr><th>Team</th><th>Closers</th><th>Approved</th><th>Level</th><th>GI</th><th>Level %</th></tr></thead>
                <tbody>
                    @forelse($teamsReport as $row)
                        <tr>
                            <td>{{ $row['team'] }}</td>
                            <td>{{ $row['closers'] }}</td>
                            <td>{{ $row['approved'] }}</td>
                            <td>{{ $row['level'] }}</td>
                            <td>{{ $row['gi'] }}</td>
                            <td>{{ $row['level_pct'] }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--rp-text-muted);padding:20px">No data for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection