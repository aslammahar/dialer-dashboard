@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Retention Client Wise Report')

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
<a href="{{ route('retention.dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--cw-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Retention Dashboard
</a>
<div class="cw-wrap">
   <div class="cw-head">
    <div class="cw-title">🗂️ Retention Client Wise</div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <a href="{{ route('retention.reports.client-wise', ['range' => 'daily']) }}" class="cw-apply" style="{{ $range == 'daily' ? '' : 'background:var(--cw-surface);color:var(--cw-text-sec);border:1px solid var(--cw-border)' }};text-decoration:none">Daily</a>
        <a href="{{ route('retention.reports.client-wise', ['range' => 'weekly']) }}" class="cw-apply" style="{{ $range == 'weekly' ? '' : 'background:var(--cw-surface);color:var(--cw-text-sec);border:1px solid var(--cw-border)' }};text-decoration:none">Weekly</a>
        <a href="{{ route('retention.reports.client-wise', ['range' => 'monthly']) }}" class="cw-apply" style="{{ $range == 'monthly' ? '' : 'background:var(--cw-surface);color:var(--cw-text-sec);border:1px solid var(--cw-border)' }};text-decoration:none">Monthly</a>

        @if($range == 'monthly')
        <form method="GET" action="{{ route('retention.reports.client-wise') }}" style="display:flex;gap:8px">
            <input type="hidden" name="range" value="monthly">
            <input type="month" name="month" class="cw-input" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
            <button type="submit" class="cw-apply">View</button>
        </form>
        @endif
    </div>
</div>

<div class="cw-box">
    <div style="font-family:var(--cw-font-display);font-weight:700;font-size:14px;margin-bottom:12px">Detailed Closer × Client Breakdown</div>
    <div style="overflow-x:auto">
        <table class="cw-table">
            <thead>
                <tr>
                    <th>Closer</th>
                    <th>MTD</th>
                    <th>Level</th>
                    <th>GI</th>
                    <th>Level %</th>
                    @foreach($clients as $client)
                        <th>{{ $client }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($board as $row)
                    <tr>
                        <td>{{ $row['closer'] }}</td>
                        <td>{{ $row['approved'] }}</td>
                        <td>{{ $row['level'] }}</td>
                        <td>{{ $row['gi'] }}</td>
                        <td>{{ $row['level_pct'] }}%</td>
                        @foreach($clients as $client)
                            <td>{{ $row['clients'][$client] ?? 0 }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ 5 + count($clients) }}" style="text-align:center;color:var(--cw-text-muted);padding:20px">No data for this selection.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td>{{ $totals['approved'] }}</td>
                    <td>{{ $totals['level'] }}</td>
                    <td>{{ $totals['gi'] }}</td>
                    <td>{{ $totals['level_pct'] }}%</td>
                    @foreach($clients as $client)
                        <td>{{ $totals['clients'][$client] ?? 0 }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</div>
@endsection
