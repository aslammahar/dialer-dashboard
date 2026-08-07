@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Retention Team Wise')

@push('css-page')
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
<a href="{{ route('retention.dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--tw-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Retention Dashboard
</a>
<div class="tw-wrap">
    <!-- ── Add Closer Strip ─────────────────────────────────── -->
    @if(isset($canEdit) && $canEdit)
    <!-- ── Add Closer Strip ─────────────────────────────────── -->
    <div style="background:var(--tw-surface-alt);border:1px solid var(--tw-border);border-radius:12px;padding:14px 18px;margin-bottom:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span style="font-size:13px;color:var(--tw-text-sec);font-weight:600;white-space:nowrap;">➕ Add Closer:</span>
        <form method="POST" action="{{ route('retention.teamWise.add') }}" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <select name="sales_closer_id" class="tw-input" required style="min-width:220px;">
                <option value="" disabled selected>Select a closer…</option>
                @foreach($availableClosers ?? [] as $ac)
                    <option value="{{ $ac->id }}">{{ $ac->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="tw-apply" style="transition:transform .15s;white-space:nowrap;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">＋ Add</button>
        </form>
    </div>

    <!-- ── Remove Closer Strip ───────────────────────────────── -->
    <div style="background:var(--tw-surface-alt);border:1px solid var(--tw-border);border-radius:12px;padding:14px 18px;margin-bottom:18px;">
        <div style="font-size:13px;color:var(--tw-text-sec);font-weight:600;margin-bottom:10px;">➖ Remove Closer from Retention Team:</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @forelse($retentionClosers ?? [] as $rc)
                <form method="POST" action="{{ route('retention.teamWise.remove') }}" style="display:inline-flex;align-items:center;">
                    @csrf
                    <input type="hidden" name="sales_closer_id" value="{{ $rc->id }}">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;background:var(--tw-surface);border:1px solid var(--tw-border);color:var(--tw-text);border-radius:8px;padding:5px 11px;font-size:12.5px;font-family:var(--tw-font-body);cursor:pointer;transition:border-color .15s,color .15s;"
                        onmouseover="this.style.borderColor='#e74c3c';this.style.color='#e74c3c'"
                        onmouseout="this.style.borderColor='var(--tw-border)';this.style.color='var(--tw-text)'"
                        onclick="return confirm('Remove {{ $rc->name }} from Retention team?')">
                        <span>{{ $rc->name }}</span>
                        <span style="font-size:11px;opacity:.7;">✕</span>
                    </button>
                </form>
            @empty
                <span style="color:var(--tw-text-muted);font-size:13px;">No closers currently in Retention team.</span>
            @endforelse
        </div>
    </div>

    @if(session('success'))
        <div style="background:rgba(52,245,197,.12);border:1px solid #34f5c5;border-radius:10px;padding:10px 16px;margin-bottom:14px;color:#34f5c5;font-size:13px;font-weight:600;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @endif


    <!-- ── Title + Date Range ───────────────────────────────── -->
    <div class="tw-head">
        <div class="tw-title">👥 Retention Team Wise</div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a href="{{ route('retention.reports.team-wise', ['range' => 'daily']) }}" class="tw-apply" style="{{ $range == 'daily' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Daily</a>
            <a href="{{ route('retention.reports.team-wise', ['range' => 'weekly']) }}" class="tw-apply" style="{{ $range == 'weekly' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Weekly</a>
            <a href="{{ route('retention.reports.team-wise', ['range' => 'monthly']) }}" class="tw-apply" style="{{ $range == 'monthly' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Monthly</a>
            <a href="{{ route('retention.reports.team-wise', ['range' => 'custom']) }}" class="tw-apply" style="{{ $range == 'custom' ? '' : 'background:var(--tw-surface);color:var(--tw-text-sec);border:1px solid var(--tw-border)' }};text-decoration:none">Custom</a>

            @if($range == 'monthly')
            <form class="tw-month-form" method="GET" action="{{ route('retention.reports.team-wise') }}">
                <input type="hidden" name="range" value="monthly">
                <input type="month" name="month" class="tw-input" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
                <button type="submit" class="tw-apply">View</button>
            </form>
            @endif

            @if($range == 'custom')
            <form class="tw-month-form" method="GET" action="{{ route('retention.reports.team-wise') }}">
                <input type="hidden" name="range" value="custom">
                <input type="date" name="start_date" class="tw-input" value="{{ $start_date ?? '' }}">
                <span style="color:var(--tw-text-sec);font-size:12px">to</span>
                <input type="date" name="end_date" class="tw-input" value="{{ $end_date ?? '' }}">
                <button type="submit" class="tw-apply">View</button>
            </form>
            @endif
        </div>
    </div>


    @forelse($teams as $team)
        <div class="tw-team-card">
            <div class="tw-team-head">
                <div class="tw-team-name">{{ $team['name'] }}</div>
                <div class="tw-team-summary">
                    <b>{{ $team['totals']['mtd'] }}</b> Sales &nbsp;·&nbsp;
                    Total Closers <b>{{ $team['total_closers'] }}</b> &nbsp;·&nbsp;
                    Level% <b>{{ $team['averages']['level_pct'] }}%</b> &nbsp;·&nbsp;
                    Avg Pre <b>{{ $team['averages']['avg_pre'] }}</b>
                </div>
            </div>

            <div class="tw-scroll">
                <table class="tw-table">
                    <thead>
                        <tr>
                            <th>Closer</th>
                            <th>Total Sales</th>
                            <th>Level</th>
                            <th>GI</th>
                            <th>Level %</th>
                            <th>Avg Pre</th>
                            @foreach($clients as $clientName)
                                <th>{{ $clientName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($team['closers'] as $closer)
                            <tr>
                                <td>{{ $closer['closer'] }}</td>
                                <td>{{ $closer['mtd'] }}</td>
                                <td>{{ $closer['level'] }}</td>
                                <td>{{ $closer['gi'] }}</td>
                                <td>{{ $closer['level_pct'] }}%</td>
                                <td>{{ $closer['avg_pre'] }}</td>
                                @foreach($clients as $clientName)
                                    <td>{{ $closer['clients'][$clientName] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ 6 + count($clients) }}" style="text-align:center;color:var(--tw-text-muted);padding:16px">No closers in this team for this selection.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total / Avg</td>
                            <td>{{ $team['totals']['mtd'] }}</td>
                            <td>{{ $team['totals']['level'] }}</td>
                            <td>{{ $team['totals']['gi'] }}</td>
                            <td>{{ $team['averages']['level_pct'] }}%</td>
                            <td>{{ $team['averages']['avg_pre'] }}</td>
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
