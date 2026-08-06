@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Retention Closers Report')

@push('css-page')
@include('dialer-dashboard._styles')
@endpush

@section('content')
<div class="dd-wrap">

    <div class="dd-topbar">
        <div class="dd-brand">
            <div class="dd-brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div>
                <div class="dd-brand-title">Retention Closers Report</div>
                <div class="dd-brand-sub">Monthly Performance & Breakdown</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px">
            <a href="{{ route('retention.dashboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-arrow-left"></i> Retention Dashboard
            </a>
            <a href="{{ route('retention.clients') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-building"></i> Retention Clients
            </a>
        </div>
    </div>

    <div class="dd-panel">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Retention Closers Performance ({{ $month }})</div>
                <div class="dd-panel-sub">Ranked & aggregated by closer performance</div>
            </div>
            <form method="GET" action="{{ route('retention.report') }}" style="display:flex;gap:8px;align-items:center">
                <input type="month" name="month" value="{{ $month }}" class="dd-input" style="padding:4px 8px">
                <button type="submit" class="dd-apply" style="padding:5px 12px">Load Month</button>
            </form>
        </div>

        <div class="dd-table-wrap">
            <table class="dd-table">
                <thead>
                    <tr>
                        <th>Retention Closers</th>
                        <th>Working Days</th>
                        <th>Total Count</th>
                        <th>Rewrite</th>
                        <th>Fixed</th>
                        <th>Correspondence</th>
                        <th>New Policy</th>
                        <th>SPD</th>
                        <th>LEVEL</th>
                        <th>GI</th>
                        <th>Level %</th>
                        <th>FCF</th>
                        <th>D6</th>
                        <th>PM7</th>
                        <th>AVG Pre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                        <tr>
                            <td style="font-weight:700;color:#fff">{{ $row['closer'] }}</td>
                            <td>{{ $row['working_days'] }}</td>
                            <td style="font-weight:800;color:#60a5fa">{{ $row['total_count'] }}</td>
                            <td>{{ $row['rewrite'] }}</td>
                            <td>{{ $row['fixed'] }}</td>
                            <td>{{ $row['correspondence'] }}</td>
                            <td>{{ $row['new_policy'] }}</td>
                            <td><span class="dd-badge" style="background:#065f46;color:#34d399">{{ $row['spd'] }}</span></td>
                            <td>{{ $row['level'] }}</td>
                            <td>{{ $row['gi'] }}</td>
                            <td>{{ $row['level_pct'] }}%</td>
                            <td>{{ $row['fcf'] }}</td>
                            <td>{{ $row['d6'] }}</td>
                            <td>{{ $row['pm7'] }}</td>
                            <td>${{ number_format($row['avg_pre'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="15" style="text-align:center;color:var(--dd-text-muted);padding:24px">No report data found for {{ $month }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
