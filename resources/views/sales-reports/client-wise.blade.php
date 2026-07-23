@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Client Wise Report')

@push('css-page')

<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --cw-bg:#090d12; --cw-surface:#11161d; --cw-surface-alt:#171f29;
        --cw-border:rgba(255,255,255,.07); --cw-text:#f3f6f7; --cw-text-sec:#93a2ac;
        --cw-text-muted:#586872; --cw-accent:#34f5c5;
        --cw-font-display:'Space Grotesk',sans-serif; --cw-font-body:'Inter',sans-serif; --cw-font-mono:'JetBrains Mono',monospace;
    }
    .cw-wrap{background:var(--cw-bg);color:var(--cw-text);font-family:var(--cw-font-body);padding:28px;border-radius:18px;min-height:100vh}
    .cw-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
    .cw-title{font-family:var(--cw-font-display);font-weight:700;font-size:20px}
    .cw-input{background:var(--cw-surface);border:1px solid var(--cw-border);color:var(--cw-text);border-radius:9px;padding:8px 12px;font-size:13px}
    .cw-apply{background:var(--cw-accent);color:#06231b;border:none;border-radius:9px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer}
    .cw-box{background:var(--cw-surface);border:1px solid var(--cw-border);border-radius:14px;padding:18px}
    .cw-table{width:100%;border-collapse:collapse}
    .cw-table th{text-align:right;padding:0 10px 10px;font-size:10.5px;text-transform:uppercase;color:var(--cw-text-muted);white-space:nowrap}
    .cw-table th:first-child{text-align:left}
    .cw-table td{padding:8px 10px;font-size:12.5px;border-bottom:1px solid var(--cw-border);text-align:right;font-family:var(--cw-font-mono);white-space:nowrap}
    .cw-table td:first-child{text-align:left;font-family:var(--cw-font-body)}
    .cw-table tfoot td{font-weight:600;background:var(--cw-surface-alt);border-top:1px solid var(--cw-border-strong,var(--cw-border));border-bottom:none}
    .cw-table tfoot td:first-child{text-align:left;font-family:var(--cw-font-body)}
</style>
@endpush

@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--cw-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Leaderboard
</a>
<div class="cw-wrap">
   <div class="cw-head">
    <div class="cw-title">🗂️ Client Wise Report</div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <a href="{{ route('sales-reports.client-wise', ['range' => 'daily']) }}" class="cw-apply" style="{{ $range == 'daily' ? '' : 'background:var(--cw-surface);color:var(--cw-text-sec);border:1px solid var(--cw-border)' }};text-decoration:none">Daily</a>
        <a href="{{ route('sales-reports.client-wise', ['range' => 'weekly']) }}" class="cw-apply" style="{{ $range == 'weekly' ? '' : 'background:var(--cw-surface);color:var(--cw-text-sec);border:1px solid var(--cw-border)' }};text-decoration:none">Weekly</a>
        <a href="{{ route('sales-reports.client-wise', ['range' => 'monthly']) }}" class="cw-apply" style="{{ $range == 'monthly' ? '' : 'background:var(--cw-surface);color:var(--cw-text-sec);border:1px solid var(--cw-border)' }};text-decoration:none">Monthly</a>

        @if($range == 'monthly')
        <form method="GET" action="{{ route('sales-reports.client-wise') }}" style="display:flex;gap:8px">
            <input type="hidden" name="range" value="monthly">
            <input type="month" name="month" class="cw-input" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
            <button type="submit" class="cw-apply">View</button>
        </form>
        @endif
    </div>
</div>
@if(session('status'))
    <div style="background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--cw-accent);
        border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px">✓ {{ session('status') }}</div>
@endif

{{-- @if($canEdit) --}}
<form method="POST" action="{{ route('sales-clients.store') }}" class="cw-box" style="display:flex;gap:8px;align-items:end;margin-bottom:16px">
    @csrf
    <div style="flex:1">
        <label style="font-size:11px;color:var(--cw-text-muted);text-transform:uppercase;display:block;margin-bottom:4px">Add Client</label>
        <input type="text" name="name" class="cw-input" style="width:100%" placeholder="e.g. D6" required>
    </div>
    <button type="submit" class="cw-apply">Add</button>
</form>
{{-- @endif --}}

    <div class="cw-box" style="margin-bottom:16px">
        <div style="font-family:var(--cw-font-display);font-weight:700;font-size:14px;margin-bottom:12px">Client Summary — {{ \Carbon\Carbon::parse($month)->format('F Y') }}</div>
        <div style="overflow-x:auto">
            <table class="cw-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Approved</th>
                        <th>Level</th>
                        <th>GI</th>
                        <th>Avg Pre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $s)
                        <tr>
                            <td>{{ $s['client'] }}</td>
                            <td>{{ $s['approved'] }}</td>
                            <td>{{ $s['level'] }}</td>
                            <td>{{ $s['gi'] }}</td>
                            <td>{{ $s['avg_pre'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--cw-text-muted);padding:16px">No data for this month.</td></tr>
                    @endforelse
                </tbody>
                @if(count($summary))
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>{{ array_sum(array_column($summary, 'approved')) }}</td>
                        <td>{{ array_sum(array_column($summary, 'level')) }}</td>
                        <td>{{ array_sum(array_column($summary, 'gi')) }}</td>
                        <td>{{ round(array_sum(array_column($summary, 'avg_pre')) / count($summary), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
<div class="cw-box">
    <div style="font-family:var(--cw-font-display);font-weight:700;font-size:14px;margin-bottom:12px">Detailed Closer × Client Breakdown</div>
    <div style="overflow-x:auto">
        @php
            $rowsCollection = collect($rows);
            $clients = $rowsCollection->pluck('client')->unique()->sort()->values();

            $pivot = [];
            foreach ($rowsCollection as $r) {
                $pivot[$r['closer']][$r['client']] = $r['mtd']; // <-- actual approved value
            }
        @endphp
        <table class="cw-table">
            <thead>
                <tr>
                    <th>Closer</th>
                    {{-- <th>Client</th> --}}
                    <th>Working Days</th>
                    <th>MTD</th>
                    <th>SPD</th>
                    <th>Level</th>
                    <th>GI</th>
                    <th>Level %</th>
                    <th>Avg Pre</th>
                    @foreach($clients as $client)
                        <th>{{ $client }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row['closer'] }}</td>
                        {{-- <td>{{ $row['client'] }}</td> --}}
                        <td>{{ $row['working_days'] }}</td>
                        <td>{{ $row['mtd'] }}</td>
                        <td>{{ $row['spd'] }}</td>
                        <td>{{ $row['level'] }}</td>
                        <td>{{ $row['gi'] }}</td>
                        <td>{{ $row['level_pct'] }}%</td>
                        <td>{{ $row['avg_pre'] }}</td>
                        @foreach($clients as $client)
                            <td>{{ $pivot[$row['closer']][$client] ?? '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ 9 + $clients->count() }}" style="text-align:center;color:var(--cw-text-muted);padding:20px">No data for this month.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection