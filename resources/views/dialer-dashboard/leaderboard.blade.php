@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Dialer Leaderboard')

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
                <div class="dd-brand-title">Dialer Leaderboard</div>
                <div class="dd-brand-sub">Live from VICIdial CLOSERstats · ranked by calls handled</div>
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
            <div class="dd-sync">
                <span class="dd-dot"></span>
                Synced hourly · last sync {{ $lastSyncedAt ?? 'pending first run' }}
            </div>
        </div>
    </div>

    <form class="dd-filters" method="GET" action="{{ route('dialer-leaderboard') }}">
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
    <button type="submit" class="dd-apply">Apply</button>
</form>

    <div class="dd-panel">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Leaderboard</div>
                <div class="dd-panel-sub">Live from VICIdial CLOSERstats — ranked by calls handled</div>
            </div>
        </div>

        @if($dialerError ?? null)
            <div style="font-size:12.5px;color:var(--dd-danger);margin-bottom:12px">{{ $dialerError }}</div>
        @endif

        <div class="dd-lb-scroll">
            <table class="dd-lb-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Agent</th>
                        <th>Team</th>
                        <th>Group</th>
                        <th>Calls</th>
                        <th>Time (H:M:S)</th>
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $i => $agent)
                        <tr class="r{{ $i+1 <= 3 ? $i+1 : '' }}">
                            <td><span class="dd-rank-badge">{{ $i+1 }}</span></td>
                            <td>
                                <div class="dd-lb-agent">
                                    <div class="dd-avatar">{{ $agent['initials'] }}</div>
                                    <div>
                                        <div class="dd-lb-name">{{ $agent['name'] }}</div>
                                        <div class="dd-lb-team">{{ $agent['emp_id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="dd-lb-team">{{ $agent['team'] }}</span></td>
                            <td><span class="dd-lb-team">{{ $agent['team'] }}</span></td>
                            <td>{{ $agent['calls'] }}</td>
                            <td>{{ $agent['time'] }}</td>
                            <td>{{ $agent['avg_talk_time'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--dd-text-muted);padding:20px">
                                No leaderboard data available for this selection.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Total</td>
                        <td>{{ $leaderboardTotals['calls'] }}</td>
                        <td>{{ $leaderboardTotals['time'] }}</td>
                        <td>{{ $leaderboardTotals['avg_talk_time'] }}</td>
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