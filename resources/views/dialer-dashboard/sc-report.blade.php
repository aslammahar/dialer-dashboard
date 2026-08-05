@extends('layouts.dashboard-fullscreen')

@section('page-title', "Senior Closer (SC) Report")

@push('css-page')
@include('dialer-dashboard._styles')
@endpush

@section('content')
<div class="dd-wrap">

    <div class="dd-topbar">
        <div class="dd-brand">
            <div class="dd-brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 5a2 2 0 0 1 2-2h2.2a1 1 0 0 1 1 .8l1 4.6a1 1 0 0 1-.5 1.1l-1.9 1.1a13 13 0 0 0 6.6 6.6l1.1-1.9a1 1 0 0 1 1.1-.5l4.6 1a1 1 0 0 1 .8 1V19a2 2 0 0 1-2 2h-1C10.6 21 3 13.4 3 6V5Z"/>
                </svg>
            </div>
            <div>
                <div class="dd-brand-title">Senior Closer (SC) Report</div>
                <div class="dd-brand-sub">Detailed breakdown of Avatar vs JC performance & ratios</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <div class="dd-nyclock" id="ddNyClock">
                <span class="dd-dot"></span>
                <span id="ddNyClockText">New York — --:--:-- </span>
            </div>
            <a href="{{ route('dialer-dashboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('dialer-leaderboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-list-numbers"></i> Leaderboard
            </a>
            <div class="dd-sync">
                <span class="dd-dot"></span>
                Synced hourly · last sync {{ $lastSyncedAt ?? 'pending first run' }}
            </div>
        </div>
    </div>

    <form class="dd-filters" method="GET" action="{{ route('dialer-sc-report') }}">
        <div class="dd-field">
            <label class="dd-label">From</label>
            <input type="text" id="ddFromDate" name="from" class="dd-input" autocomplete="off" value="{{ $filters['from'] ?? now('America/New_York')->startOfMonth()->toDateString() }}">
        </div>
        <div class="dd-field">
            <label class="dd-label">To</label>
            <input type="text" id="ddToDate" name="to" class="dd-input" autocomplete="off" value="{{ $filters['to'] ?? now('America/New_York')->toDateString() }}">
        </div>
        <button type="submit" class="dd-apply">Apply</button>
    </form>

    <div class="dd-panel">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Senior Closer Performance Report</div>
                <div class="dd-panel-sub">Granular Avatar vs JC performance metrics and conversion ratios</div>
            </div>
        </div>

        <div class="dd-lb-scroll">
            <table class="dd-lb-table">
                <thead>
                    <tr>
                        <th class="dd-text-left">USER NAME</th>
                        <th>Avatar Calls</th>
                        <th>JC Calls</th>
                        <th>Login Hours</th>
                        <th>Avatar Calls Average Talk Time</th>
                        <th>J.C's Calls Average Talk Time</th>
                        <th>Avatar Sales Submission</th>
                        <th>Avatar Sales Approved</th>
                        <th>Avatar Sales Approval Ratio</th>
                        <th>JC's Sales Submission</th>
                        <th>J.C's Sales Approved</th>
                        <th>J.C's Sales Approval Ratio</th>
                        <th>Avatar Approved Conversion</th>
                        <th>JC'S Approved Conversion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportRows as $i => $row)
                        @php
                            $initials = collect(explode(' ', $row['name']))
                                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <tr class="r{{ $i+1 <= 3 ? $i+1 : '' }}">
                            <td class="dd-text-left">
                                <div class="dd-lb-agent">
                                    <div class="dd-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="dd-lb-name">{{ $row['name'] }}</div>
                                        <div class="dd-lb-team">{{ $row['team'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($row['avatar_calls']) }}</td>
                            <td>{{ number_format($row['jc_calls']) }}</td>
                            <td>{{ $row['login_hours_formatted'] }}</td>
                            <td>{{ $row['avatar_avg_talktime'] }}</td>
                            <td>{{ $row['jc_avg_talktime'] }}</td>
                            <td>{{ number_format($row['avatar_submitted']) }}</td>
                            <td>{{ number_format($row['avatar_approved']) }}</td>
                            <td><span class="dd-pill {{ $row['avatar_approval_ratio'] >= 50 ? 'dd-pill-hi' : 'dd-pill-lo' }}">{{ $row['avatar_approval_ratio'] }}%</span></td>
                            <td>{{ number_format($row['jc_submitted']) }}</td>
                            <td>{{ number_format($row['jc_approved']) }}</td>
                            <td><span class="dd-pill {{ $row['jc_approval_ratio'] >= 50 ? 'dd-pill-hi' : 'dd-pill-lo' }}">{{ $row['jc_approval_ratio'] }}%</span></td>
                            <td>{{ $row['avatar_approved_conv'] > 0 ? $row['avatar_approved_conv'] : '-' }}</td>
                            <td>{{ $row['jc_approved_conv'] > 0 ? $row['jc_approved_conv'] : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" style="text-align:center;color:var(--dd-text-muted);padding:20px">
                                No SC report data available for this date selection.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="dd-text-left">Total</td>
                        <td>{{ number_format($totals['avatar_calls']) }}</td>
                        <td>{{ number_format($totals['jc_calls']) }}</td>
                        <td>{{ $totals['login_hours_formatted'] }}</td>
                        <td>{{ $totals['avatar_avg_talktime'] }}</td>
                        <td>{{ $totals['jc_avg_talktime'] }}</td>
                        <td>{{ number_format($totals['avatar_submitted']) }}</td>
                        <td>{{ number_format($totals['avatar_approved']) }}</td>
                        <td>{{ $totals['avatar_approval_ratio'] }}%</td>
                        <td>{{ number_format($totals['jc_submitted']) }}</td>
                        <td>{{ number_format($totals['jc_approved']) }}</td>
                        <td>{{ $totals['jc_approval_ratio'] }}%</td>
                        <td>{{ $totals['avatar_approved_conv'] }}</td>
                        <td>{{ $totals['jc_approved_conv'] }}</td>
                    </tr>
                    <tr>
                        <td class="dd-text-left">Average</td>
                        <td>{{ number_format($averages['avatar_calls'], 1) }}</td>
                        <td>{{ number_format($averages['jc_calls'], 1) }}</td>
                        <td>{{ $averages['login_hours_formatted'] }}</td>
                        <td>{{ $averages['avatar_avg_talktime'] }}</td>
                        <td>{{ $averages['jc_avg_talktime'] }}</td>
                        <td>{{ number_format($averages['avatar_submitted'], 1) }}</td>
                        <td>{{ number_format($averages['avatar_approved'], 1) }}</td>
                        <td>{{ $averages['avatar_approval_ratio'] }}</td>
                        <td>{{ number_format($averages['jc_submitted'], 1) }}</td>
                        <td>{{ number_format($averages['jc_approved'], 1) }}</td>
                        <td>{{ $averages['jc_approval_ratio'] }}</td>
                        <td>{{ $averages['avatar_approved_conv'] }}</td>
                        <td>{{ $averages['jc_approved_conv'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script>
(function(){
    if (typeof flatpickr === 'undefined') return;
    var fromEl = document.getElementById('ddFromDate');
    var toEl = document.getElementById('ddToDate');
    if (!fromEl || !toEl) return;

    var toPicker = flatpickr(toEl, { dateFormat: 'Y-m-d', maxDate: 'today' });
    flatpickr(fromEl, {
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        onChange: function (selectedDates) {
            if (selectedDates[0]) toPicker.set('minDate', selectedDates[0]);
        }
    });
})();
</script>
<script>
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
@endsection
