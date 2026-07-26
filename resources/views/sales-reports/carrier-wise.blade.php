@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Carrier Wise Report')

@push('css-page')

<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --cr-bg:#090d12; --cr-surface:#11161d; --cr-surface-alt:#171f29;
        --cr-border:rgba(255,255,255,.07); --cr-text:#f3f6f7; --cr-text-sec:#93a2ac;
        --cr-text-muted:#586872; --cr-accent:#34f5c5;
        --cr-font-display:'Space Grotesk',sans-serif; --cr-font-body:'Inter',sans-serif; --cr-font-mono:'JetBrains Mono',monospace;
    }
    .cr-wrap{background:var(--cr-bg);color:var(--cr-text);font-family:var(--cr-font-body);padding:28px;border-radius:18px;min-height:100vh}
    .cr-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
    .cr-title{font-family:var(--cr-font-display);font-weight:700;font-size:20px}
    .cr-input{background:var(--cr-surface);border:1px solid var(--cr-border);color:var(--cr-text);border-radius:9px;padding:8px 12px;font-size:13px}
    .cr-apply{background:var(--cr-accent);color:#06231b;border:none;border-radius:9px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer}
    .cr-box{background:var(--cr-surface);border:1px solid var(--cr-border);border-radius:14px;padding:18px}
    .cr-table{width:100%;border-collapse:collapse}
    .cr-table th{text-align:right;padding:0 10px 10px;font-size:10.5px;text-transform:uppercase;color:var(--cr-text-muted);white-space:nowrap}
    .cr-table th:first-child{text-align:left}
    .cr-table td{padding:8px 10px;font-size:12.5px;border-bottom:1px solid var(--cr-border);text-align:right;font-family:var(--cr-font-mono);white-space:nowrap}
    .cr-table td:first-child{text-align:left;font-family:var(--cr-font-body)}
    .cr-table tfoot td{font-weight:600;background:var(--cr-surface-alt);border-bottom:none}
    .cr-table tfoot td:first-child{text-align:left;font-family:var(--cr-font-body)}
</style>
@endpush

@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--cr-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Leaderboard
</a>
<div class="cr-wrap">
    <div class="cr-head">
        <div class="cr-title">🚚 Carrier Wise Report</div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a href="{{ route('sales-reports.carrier-wise', ['range' => 'daily']) }}" class="cr-apply" style="{{ $range == 'daily' ? '' : 'background:var(--cr-surface);color:var(--cr-text-sec);border:1px solid var(--cr-border)' }};text-decoration:none">Daily</a>
            <a href="{{ route('sales-reports.carrier-wise', ['range' => 'weekly']) }}" class="cr-apply" style="{{ $range == 'weekly' ? '' : 'background:var(--cr-surface);color:var(--cr-text-sec);border:1px solid var(--cr-border)' }};text-decoration:none">Weekly</a>
            <a href="{{ route('sales-reports.carrier-wise', ['range' => 'monthly']) }}" class="cr-apply" style="{{ $range == 'monthly' ? '' : 'background:var(--cr-surface);color:var(--cr-text-sec);border:1px solid var(--cr-border)' }};text-decoration:none">Monthly</a>

            @if($range == 'monthly')
            <form method="GET" action="{{ route('sales-reports.carrier-wise') }}" style="display:flex;gap:8px">
                <input type="hidden" name="range" value="monthly">
                <input type="month" name="month" class="cr-input" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
                <button type="submit" class="cr-apply">View</button>
            </form>
            @endif
        </div>
    </div>
@if(session('status'))
    <div style="background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--cr-accent);
        border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px">✓ {{ session('status') }}</div>
@endif

{{-- @if($canEdit) --}}
<form method="POST" action="{{ route('sales-carriers.store') }}" class="cr-box" style="display:flex;gap:8px;align-items:end;margin-bottom:16px">
    @csrf
    <div style="flex:1">
        <label style="font-size:11px;color:var(--cr-text-muted);text-transform:uppercase;display:block;margin-bottom:4px">Add Carrier</label>
        <input type="text" name="name" class="cr-input" style="width:100%" placeholder="e.g. AFLAC" required>
    </div>
    <button type="submit" class="cr-apply">Add</button>
</form>
{{-- @endif --}}
    <div class="cr-box" style="margin-bottom:16px">
        <div style="font-family:var(--cr-font-display);font-weight:700;font-size:14px;margin-bottom:12px">Carrier Summary</div>
        <div style="overflow-x:auto">
            <table class="cr-table">
                <thead>
                    <tr>
                        <th>Carrier</th>
                        <th>Approved</th>
                        <th>Level</th>
                        <th>GI</th>
                        <th>Level %</th>
                        <th>Avg Pre</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $s)
                        <tr>
                            <td>{{ $s['carrier'] }}</td>
                            <td>{{ $s['approved'] }}</td>
                            <td>{{ $s['level'] }}</td>
                            <td>{{ $s['gi'] }}</td>
                            <td>{{ $s['level_pct'] }}%</td>
                            <td>{{ $s['avg_pre'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--cr-text-muted);padding:16px">No data for this selection.</td></tr>
                    @endforelse
                </tbody>
                @if(count($summary))
                <tfoot>
                    @php
                        $totalApproved = array_sum(array_column($summary, 'approved'));
                        $totalLevel = array_sum(array_column($summary, 'level'));
                    @endphp
                    <tr>
                        <td>Total</td>
                        <td>{{ $totalApproved }}</td>
                        <td>{{ $totalLevel }}</td>
                        <td>{{ array_sum(array_column($summary, 'gi')) }}</td>
                        <td>{{ $totalApproved > 0 ? round(($totalLevel / $totalApproved) * 100) : 0 }}%</td>
                        <td>{{ round(array_sum(array_column($summary, 'avg_pre')) / count($summary), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="cr-box">
    <div style="font-family:var(--cr-font-display);font-weight:700;font-size:14px;margin-bottom:12px">Detailed Closer × Carrier Breakdown</div>
    <div style="overflow-x:auto">
        <table class="cr-table">
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
                    <th>Conversion</th>
                    <th>Avg Talk Time</th>
                    @foreach($carriers as $carrier)
                        <th>{{ $carrier }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row['closer'] }}</td>
                        <td>{{ $row['working_days'] }}</td>
                        <td>{{ $row['mtd'] }}</td>
                        <td>{{ $row['spd'] }}</td>
                        <td>{{ $row['level'] }}</td>
                        <td>{{ $row['gi'] }}</td>
                        <td>{{ $row['level_pct'] }}%</td>
                        <td>{{ $row['avg_pre'] }}</td>
                        <td>{{ $row['avatar_calls'] }}</td>
                        <td>{{ $row['conversion'] }}%</td>
                        <td>{{ $row['avg_talk_time'] }}</td>
                        @foreach($carriers as $carrier)
                            <td>{{ $row['carriers'][$carrier] ?? 0 }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ 11 + count($carriers) }}" style="text-align:center;color:var(--cr-text-muted);padding:20px">No data for this selection.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection