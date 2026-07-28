@extends('layouts.dashboard-fullscreen')
@section('page-title', 'Closer Attendance')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--as-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Leaderboard
</a>
@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --at-bg:#090d12; --at-surface:#11161d; --at-surface-alt:#171f29;
        --at-border:rgba(255,255,255,.07); --at-text:#f3f6f7; --at-text-sec:#93a2ac;
        --at-text-muted:#586872; --at-accent:#34f5c5;
        --at-font-display:'Space Grotesk',sans-serif; --at-font-body:'Inter',sans-serif;
    }
    .at-wrap{background:var(--at-bg);color:var(--at-text);font-family:var(--at-font-body);padding:28px;border-radius:18px;min-height:100vh}
    .at-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px}
    .at-title{font-family:var(--at-font-display);font-weight:700;font-size:20px}
    .at-input{background:var(--at-surface);border:1px solid var(--at-border);color:var(--at-text);border-radius:9px;padding:8px 12px;font-size:13px}
    .at-apply{background:var(--at-accent);color:#06231b;border:none;border-radius:9px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer}
    .at-alert{background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--at-accent);
        border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px}
    .at-table{width:100%;border-collapse:collapse;background:var(--at-surface);border:1px solid var(--at-border);border-radius:12px;overflow:hidden}
    .at-table th{font-size:11px;text-transform:uppercase;color:var(--at-text-muted);text-align:left;padding:12px 14px;border-bottom:1px solid var(--at-border)}
    .at-table td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--at-border)}
    
    /* 👇 WHITE TEXT for Closer & Team columns */
    .at-table td:first-child,
    .at-table td:nth-child(2) {
        color: #ffffff !important;
        font-weight: 500;
    }

    .at-toggle{display:flex;gap:6px}
    .at-toggle input{display:none}
    .at-toggle label{padding:6px 12px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;
        background:var(--at-surface-alt);border:1px solid var(--at-border);color:var(--at-text-sec)}
    .at-toggle input:checked + label{background:rgba(52,245,197,.14);border-color:var(--at-accent);color:var(--at-accent)}
    .at-toggle input[value="absent"]:checked + label{background:rgba(255,90,90,.14);border-color:#ff5a5a;color:#ff5a5a}
    .at-toggle input[value="half_day"]:checked + label{background:rgba(255,176,32,.2);border-color:#ffb020;color:#ffb020}
    .at-toggle input[value="leave"]:checked + label{background:rgba(255,176,32,.14);border-color:#ffb020;color:#ffb020}
    .at-submit{margin-top:16px;background:var(--at-accent);color:#06231b;border:none;border-radius:9px;
        padding:11px 22px;font-size:13.5px;font-weight:700;cursor:pointer}
        .at-summary-table td {
    color: #ffffff !important;
}
</style>
@endpush

@section('content')
<div class="at-wrap">
    <div class="at-head">
        <div class="at-title">🗓️ Closer Attendance</div>
        <form method="GET" action="{{ route('attendance.index') }}" style="display:flex;gap:8px">
            <input type="date" name="date" class="at-input" value="{{ $date }}" max="{{ now()->toDateString() }}">
            <button type="submit" class="at-apply">Load</button>
        </form>
    </div>

    @if(session('status'))
        <div class="at-alert">✓ {{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('attendance-closer.store') }}">
        @csrf
        <input type="date" name="date" class="at-input" value="{{ $date }}" max="{{ now('America/New_York')->toDateString() }}">
        <table class="at-table">
            <thead>
                <tr><th>Closer</th><th>Team</th><th>Attendance</th></tr>
            </thead>
            <tbody>
                @foreach($closers as $closer)
                    @php $current = $existing[$closer->id] ?? 'present'; @endphp
                    <tr>
                        <td>{{ $closer->name }}</td>
                        <td>{{ $closer->team->name ?? '-' }}</td>
                        <td>
                            <div class="at-toggle">
    <input type="radio" name="status[{{ $closer->id }}]" id="p{{ $closer->id }}" value="present" {{ $current == 'present' ? 'checked' : '' }}>
    <label for="p{{ $closer->id }}">Present</label>

    <input type="radio" name="status[{{ $closer->id }}]" id="h{{ $closer->id }}" value="half_day" {{ $current == 'half_day' ? 'checked' : '' }}>
    <label for="h{{ $closer->id }}">Half Day</label>

    <input type="radio" name="status[{{ $closer->id }}]" id="a{{ $closer->id }}" value="absent" {{ $current == 'absent' ? 'checked' : '' }}>
    <label for="a{{ $closer->id }}">Absent</label>

    <input type="radio" name="status[{{ $closer->id }}]" id="l{{ $closer->id }}" value="leave" {{ $current == 'leave' ? 'checked' : '' }}>
    <label for="l{{ $closer->id }}">Leave</label>
</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>

            
        </table>

        <button type="submit" class="at-submit">Save Attendance</button>
    </form>
</div>
<div style="margin-top:28px">
    <div style="font-family:var(--at-font-display);font-weight:700;font-size:15px;margin-bottom:12px">
        Monthly Attendance Summary — {{ now('America/New_York')->format('F Y') }}
    </div>
   <table class="at-table at-summary-table">
    <thead>
        <tr><th>Closer</th><th>Present</th><th>Half Day</th><th>Absent</th><th>Leave</th><th>Total Days</th></tr>
    </thead>
    <tbody>
        @forelse($monthlySummary as $m)
            <tr>
                <td>{{ $m['closer'] }}</td>
                <td>{{ $m['present'] }}</td>
                <td>{{ $m['half_day'] }}</td>
                <td>{{ $m['absent'] }}</td>
                <td>{{ $m['leave'] }}</td>
                <td>{{ $m['total'] }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:var(--at-text-muted);padding:16px">No attendance marked yet this month.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection