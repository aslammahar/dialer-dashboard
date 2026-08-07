@extends('layouts.dashboard-fullscreen')
@section('page-title', 'Retention Closers Attendance')

@push('css-page')
@include('dialer-dashboard._styles')
<style>
    .at-toggle{display:flex;gap:6px}
    .at-toggle input{display:none}
    .at-toggle label{padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;
        background:rgba(255,255,255,0.06);border:1px solid var(--dd-border);color:var(--dd-text-muted);transition:all 0.2s}
    .at-toggle input:checked + label{background:rgba(52,245,197,0.15);border-color:var(--dd-accent);color:var(--dd-accent)}
    .at-toggle input[value="absent"]:checked + label{background:rgba(239,68,68,0.18);border-color:#ef4444;color:#fca5a5}
    .at-toggle input[value="half_day"]:checked + label{background:rgba(245,158,11,0.18);border-color:#f59e0b;color:#fcd34d}
    .at-toggle input[value="leave"]:checked + label{background:rgba(59,130,246,0.18);border-color:#3b82f6;color:#93c5fd}
</style>
@endpush

@section('content')
<div class="dd-wrap">
    {{-- Top Bar --}}
    <div class="dd-topbar">
        <div class="dd-brand">
            <div class="dd-brand-logo">
                <i class="ti ti-calendar-check" style="font-size:22px"></i>
            </div>
            <div>
                <div class="dd-brand-title">Retention Closers Attendance</div>
                <div class="dd-brand-sub">Daily Attendance & Monthly Summary — Retention Department</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <a href="{{ route('retention.dashboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-layout-dashboard"></i> Retention Dashboard
            </a>

            <a href="{{ route('retention.leaderboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-trophy"></i> Inbound Leaderboard
            </a>

            <a href="{{ route('retention.reports.team-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-users"></i> Team Wise
            </a>

            <a href="{{ route('retention.reports.client-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-briefcase"></i> Client Wise
            </a>

            <a href="{{ route('retention.reports.carrier-wise') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-truck"></i> Carrier Wise
            </a>

            <a href="{{ route('retention.report') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-report"></i> Retention Report
            </a>

            <a href="{{ route('retention.attendance.index') }}" class="dd-readonly-badge" style="text-decoration:none;border-color:var(--dd-accent);color:var(--dd-accent)">
                <i class="ti ti-calendar-check"></i> Attendance
            </a>

            @if($canEdit)
                <a href="{{ route('retention.sales.create') }}" class="dd-update-btn" style="text-decoration:none">
                    <i class="ti ti-edit"></i> Update Sales
                </a>
            @endif
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div id="attendanceSuccess" class="attendance-success" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.4);color:#34d399;padding:12px 16px;border-radius:10px;margin-bottom:16px;display:flex;align-items:center;gap:10px;opacity:0;transform:translateY(-20px);animation:fadeSlide 1.5s forwards;">
            <i class="ti ti-circle-check" style="font-size:18px"></i> {{ session('success') }}
        </div>
        <script>
            // Simple fade‑in‑slide animation is handled by CSS keyframes.
            // Remove the element after a few seconds.
            setTimeout(() => {
                const el = document.getElementById('attendanceSuccess');
                if (el) el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 600);
            }, 3000);
        </script>
    @endif
    <style>
        @keyframes fadeSlide {
            0% { opacity: 0; transform: translateY(-20px); }
            30% { opacity: 1; transform: translateY(0); }
            80% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }
    </style>


    {{-- Main Panel: Mark Attendance --}}
    <div class="dd-panel">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">🗓️ Mark Retention Attendance — {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
                <div class="dd-panel-sub">Select date and mark attendance for Retention closers</div>
            </div>
            <form method="GET" action="{{ route('retention.attendance.index') }}" style="display:flex;gap:8px;align-items:center">
                <input type="date" name="date" class="dd-input" value="{{ $date }}" max="{{ now('America/New_York')->toDateString() }}" style="padding:5px 10px">
                <button type="submit" class="dd-apply" style="padding:6px 14px">Load Date</button>
            </form>
        </div>

        <form method="POST" action="{{ route('retention.attendance.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <div class="dd-table-wrap">
                <table class="dd-table">
                    <thead>
                        <tr>
                            <th>Closer</th>
                            <th>Team</th>
                            <th>Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($closers as $closer)
                            @php $current = $existing[$closer->id] ?? ''; @endphp
                            <tr>
                                <td style="font-weight:700;color:#fff;font-size:14px">
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="dd-avatar" style="background:linear-gradient(135deg,#059669,#10b981)">
                                            {{ strtoupper(substr($closer->name, 0, 2)) }}
                                        </div>
                                        {{ $closer->name }}
                                    </div>
                                </td>
                                <td><span class="dd-badge">Retention</span></td>
                                <td>
                                    <div class="at-toggle">
                                        <input type="radio" name="status[{{ $closer->id }}]" id="p{{ $closer->id }}" value="present" {{ $current == 'present' ? 'checked' : '' }}>
                                        <label for="p{{ $closer->id }}">Present</label>

                                        <input type="radio" name="status[{{ $closer->id }}]" id="h{{ $closer->id }}" value="half_day" {{ $current == 'half_day' ? 'checked' : '' }}>
                                        <label for="h{{ $closer->id }}">Half Day (0.5)</label>

                                        <input type="radio" name="status[{{ $closer->id }}]" id="a{{ $closer->id }}" value="absent" {{ $current == 'absent' ? 'checked' : '' }}>
                                        <label for="a{{ $closer->id }}">Absent</label>

                                        <input type="radio" name="status[{{ $closer->id }}]" id="l{{ $closer->id }}" value="leave" {{ $current == 'leave' ? 'checked' : '' }}>
                                        <label for="l{{ $closer->id }}">Leave</label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;color:var(--dd-text-muted);padding:24px">
                                    No active Retention closers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($canEdit && count($closers) > 0)
                <div style="padding:16px;text-align:right;border-top:1px solid var(--dd-border)">
                    <button type="submit" class="dd-apply" style="padding:10px 24px;font-size:14px;font-weight:700">
                        <i class="ti ti-device-floppy"></i> Save Attendance
                    </button>
                </div>
            @endif
        </form>
    </div>

    {{-- Monthly Attendance Summary --}}
    <div class="dd-panel" style="margin-top:20px">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">📊 Monthly Attendance Summary — {{ now('America/New_York')->format('F Y') }}</div>
                <div class="dd-panel-sub">Total present, half days, leaves, and weighted working days for Retention closers</div>
            </div>
        </div>
        <div class="dd-table-wrap">
            <table class="dd-table">
                <thead>
                    <tr>
                        <th>Closer</th>
                        <th>Present</th>
                        <th>Half Day (0.5)</th>
                        <th>Absent</th>
                        <th>Leave</th>
                        <th style="color:var(--dd-accent)">Weighted Days</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlySummary as $m)
                        <tr>
                            <td style="font-weight:700;color:#fff">{{ $m['closer_name'] }}</td>
                            <td><span style="color:#34d399;font-weight:700">{{ $m['present'] }}</span></td>
                            <td><span style="color:#fbbf24;font-weight:700">{{ $m['half_day'] }}</span></td>
                            <td><span style="color:#f87171;font-weight:700">{{ $m['absent'] }}</span></td>
                            <td><span style="color:#60a5fa;font-weight:700">{{ $m['leave'] }}</span></td>
                            <td style="font-weight:800;color:var(--dd-accent);font-size:15px">{{ $m['weighted_days'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--dd-text-muted);padding:24px">
                                No attendance record found for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
