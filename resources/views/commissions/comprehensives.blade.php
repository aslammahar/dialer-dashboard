@extends('layouts.admin')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap');

:root {
    --bg-page:        #f4f6fa;
    --bg-card:        #ffffff;
    --bg-header:      #ffffff;
    --bg-row:         #ffffff;
    --bg-row-alt:     #f9fafb;
    --bg-row-hover:   #eef4ff;
    --border:         #e5e9f0;
    --border-med:     #d0d7e3;
    --accent:         #2563eb;
    --accent-dim:     rgba(37,99,235,.08);
    --accent-mid:     rgba(37,99,235,.15);
    --accent-glow:    rgba(37,99,235,.25);
    --green:          #16a34a;
    --green-dim:      rgba(22,163,74,.1);
    --red:            #dc2626;
    --red-dim:        rgba(220,38,38,.1);
    --yellow:         #d97706;
    --yellow-dim:     rgba(217,119,6,.1);
    --purple:         #7c3aed;
    --purple-dim:     rgba(124,58,237,.1);
    --teal:           #0891b2;
    --teal-dim:       rgba(8,145,178,.1);
    --text-primary:   #111827;
    --text-secondary: #4b5563;
    --text-muted:     #9ca3af;
    --font-sans:      'Inter', sans-serif;
    --font-mono:      'JetBrains Mono', monospace;
    --radius:         10px;
    --radius-sm:      6px;
    --shadow-sm:      0 1px 4px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow:         0 4px 16px rgba(0,0,0,.08), 0 1px 4px rgba(0,0,0,.05);
    --shadow-lg:      0 12px 40px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.06);
}

.cr-page { font-family: var(--font-sans); color: var(--text-primary); }

/* ── Top bar ── */
.cr-topbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
}
.cr-topbar-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 3px;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -.3px;
}
.cr-topbar-title .title-bar {
    width: 4px; height: 22px;
    background: var(--accent);
    border-radius: 2px;
    flex-shrink: 0;
}
.cr-topbar-sub { font-size: 13px; color: var(--text-muted); margin: 0; }
.cr-back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid var(--border-med);
    color: var(--text-secondary); padding: 7px 14px;
    border-radius: var(--radius-sm); font-family: var(--font-sans);
    font-size: 13px; font-weight: 500; text-decoration: none;
    transition: all .15s; box-shadow: var(--shadow-sm);
}
.cr-back-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); text-decoration: none; }

/* ── Filter card ── */
.cr-filter-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius); margin-bottom: 20px;
    overflow: hidden; box-shadow: var(--shadow-sm);
}
.cr-filter-header {
    background: linear-gradient(90deg, #eff6ff 0%, #f8fafc 100%);
    border-bottom: 1px solid var(--border); padding: 11px 18px;
    display: flex; align-items: center; gap: 7px;
    font-size: 12px; font-weight: 600; color: var(--accent);
    letter-spacing: .4px; text-transform: uppercase;
}
.cr-filter-body { padding: 16px 18px; }
.cr-filter-body .form-label {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: var(--text-secondary); margin-bottom: 5px;
}
.cr-filter-body .form-select {
    background-color: #fff; border: 1px solid var(--border-med);
    color: var(--text-primary); font-family: var(--font-sans);
    font-size: 13px; border-radius: var(--radius-sm); padding: 7px 10px;
    box-shadow: var(--shadow-sm);
}
.cr-filter-body .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-mid); outline: none; }
.btn-apply {
    background: var(--accent); color: #fff; border: none;
    border-radius: var(--radius-sm); padding: 8px 20px;
    font-family: var(--font-sans); font-size: 13px; font-weight: 600;
    width: 100%; cursor: pointer; transition: all .15s;
    box-shadow: 0 2px 8px var(--accent-glow);
}
.btn-apply:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 14px var(--accent-glow); }

/* ── Stat grid ── */
.cr-stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
.cr-stat-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 18px 20px;
    position: relative; overflow: hidden;
    box-shadow: var(--shadow-sm); transition: box-shadow .2s, transform .2s;
}
.cr-stat-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); }
.cr-stat-card::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
}
.cr-stat-card.c-blue::after   { background: var(--accent); }
.cr-stat-card.c-green::after  { background: var(--green); }
.cr-stat-card.c-teal::after   { background: var(--teal); }
.cr-stat-card.c-yellow::after { background: var(--yellow); }
.cr-stat-card.c-purple::after { background: var(--purple); }

.cr-stat-icon {
    width: 38px; height: 38px; border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; margin-bottom: 12px;
}
.c-blue   .cr-stat-icon { background: var(--accent-dim);  color: var(--accent); }
.c-green  .cr-stat-icon { background: var(--green-dim);   color: var(--green); }
.c-teal   .cr-stat-icon { background: var(--teal-dim);    color: var(--teal); }
.c-yellow .cr-stat-icon { background: var(--yellow-dim);  color: var(--yellow); }
.c-purple .cr-stat-icon { background: var(--purple-dim);  color: var(--purple); }

.cr-stat-val { font-family: var(--font-mono); font-size: 22px; font-weight: 600; line-height: 1; margin-bottom: 4px; color: var(--text-primary); }
.cr-stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

/* ── Table card ── */
.cr-table-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow);
}

/* Toolbar */
.cr-table-topbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 13px 18px; border-bottom: 1px solid var(--border);
    background: #fafbfc; gap: 12px; flex-wrap: wrap;
}
.cr-table-title {
    font-size: 14px; font-weight: 600; color: var(--text-primary);
    display: flex; align-items: center; gap: 8px;
}
.cr-table-title i { color: var(--accent); font-size: 13px; }
.cr-badge {
    font-family: var(--font-mono); font-size: 11px; font-weight: 600;
    padding: 2px 8px; border-radius: 20px;
    background: var(--accent-dim); color: var(--accent);
    border: 1px solid var(--accent-mid);
}
.cr-toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Per-page */
.cr-perpage-wrap { display: flex; align-items: center; gap: 6px; }
.cr-perpage-label { font-size: 12px; color: var(--text-secondary); white-space: nowrap; }
.cr-perpage-select {
    background: #fff; border: 1px solid var(--border-med);
    color: var(--text-primary); font-family: var(--font-mono);
    font-size: 12px; border-radius: var(--radius-sm);
    padding: 5px 8px; cursor: pointer; box-shadow: var(--shadow-sm);
}
.cr-perpage-select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px var(--accent-mid); }

.cr-info-text { font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); white-space: nowrap; }

/* Fullscreen button */
.cr-fullscreen-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid var(--border-med);
    color: var(--text-secondary); padding: 6px 12px;
    border-radius: var(--radius-sm); font-family: var(--font-sans);
    font-size: 12px; font-weight: 500; cursor: pointer;
    transition: all .15s; white-space: nowrap; box-shadow: var(--shadow-sm);
}
.cr-fullscreen-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }

/* ── SCROLL WRAPPER — both axes, header frozen ── */
.cr-scroll-wrap {
    overflow: auto;
    max-height: 60vh;
}
.cr-scroll-wrap::-webkit-scrollbar { width: 6px; height: 6px; }
.cr-scroll-wrap::-webkit-scrollbar-track { background: #f1f5f9; }
.cr-scroll-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.cr-scroll-wrap::-webkit-scrollbar-thumb:hover { background: var(--accent); }
.cr-scroll-wrap::-webkit-scrollbar-corner { background: #f1f5f9; }

/* ── TABLE ── */
.cr-table {
    border-collapse: separate; border-spacing: 0;
    width: max-content; min-width: 100%;
    font-family: var(--font-sans); font-size: 13px;
}

/* FROZEN HEADER */
.cr-table thead th {
    position: sticky; top: 0; z-index: 10;
    background: #f1f5f9;
    color: var(--text-secondary);
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .5px;
    padding: 11px 14px;
    border-bottom: 2px solid var(--accent);
    border-right: 1px solid var(--border);
    white-space: nowrap; user-select: none;
}
.cr-table thead th:last-child { border-right: none; }

/* Body */
.cr-table tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
    background: var(--bg-row);
    color: var(--text-primary);
    white-space: nowrap; vertical-align: middle;
    transition: background .1s;
}
.cr-table tbody td:last-child { border-right: none; }
.cr-table tbody tr:nth-child(odd)  td { background: var(--bg-row); }
.cr-table tbody tr:nth-child(even) td { background: var(--bg-row-alt); }
.cr-table tbody tr:hover td { background: var(--bg-row-hover) !important; }

/* Detail expand row */
.cr-detail-row td { background: #f0f6ff !important; padding: 0; }
.cr-detail-inner { padding: 18px 22px; }
.cr-detail-title {
    font-size: 13px; font-weight: 600; color: var(--accent);
    margin-bottom: 12px; display: flex; align-items: center; gap: 7px;
}
.cr-detail-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.cr-detail-table thead th {
    position: static;
    background: #e8eef8; color: var(--text-secondary);
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .4px;
    padding: 8px 12px;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
}
.cr-detail-table tbody td { padding: 7px 12px; border-bottom: 1px solid var(--border); color: var(--text-primary); background: #fff; }
.cr-detail-table tfoot td { padding: 8px 12px; background: #eff6ff; font-weight: 600; border-top: 2px solid var(--accent); color: var(--accent); }

/* Chips */
.chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; white-space: nowrap; }
.chip-green  { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(22,163,74,.25); }
.chip-gray   { background: #f3f4f6;           color: #6b7280;       border: 1px solid #e5e7eb; }
.chip-yellow { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(217,119,6,.25); }
.chip-blue   { background: var(--accent-dim); color: var(--accent); border: 1px solid var(--accent-mid); }
.chip-teal   { background: var(--teal-dim);   color: var(--teal);   border: 1px solid rgba(8,145,178,.25); }

/* Values */
.policy-no  { font-family: var(--font-mono); font-size: 12px; font-weight: 600; color: var(--accent); }
.amt-pos     { font-family: var(--font-mono); font-size: 12px; font-weight: 600; color: var(--green); }
.amt-neg     { font-family: var(--font-mono); font-size: 12px; font-weight: 600; color: var(--red); }
.amt-neutral { font-family: var(--font-mono); font-size: 12px; font-weight: 500; color: var(--teal); }
.amt-pending { font-family: var(--font-mono); font-size: 12px; font-weight: 600; color: var(--yellow); }
.row-num     { font-family: var(--font-mono); font-size: 11px; color: var(--text-muted); }

/* View button */
.btn-view {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--accent-dim); border: 1px solid var(--accent-mid);
    color: var(--accent); padding: 4px 10px;
    border-radius: var(--radius-sm); font-size: 11px; font-weight: 600;
    cursor: pointer; transition: all .15s; white-space: nowrap;
}
.btn-view:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.btn-view.active { background: var(--green-dim); border-color: rgba(22,163,74,.3); color: var(--green); }
.btn-view.active:hover { background: var(--green); color: #fff; border-color: var(--green); }

/* ── PAGINATION ── */
.cr-pagination-wrap {
    display: flex; justify-content: space-between; align-items: center;
    padding: 13px 18px; border-top: 1px solid var(--border);
    background: #fafbfc; flex-wrap: wrap; gap: 10px;
}
.cr-pagination-info { font-size: 12px; color: var(--text-muted); font-family: var(--font-mono); }
.cr-pagination-wrap .pagination { margin: 0; gap: 3px; }
.cr-pagination-wrap .page-item .page-link {
    background: #fff; border: 1px solid var(--border-med);
    color: var(--text-secondary); font-family: var(--font-mono);
    font-size: 12px; padding: 5px 10px;
    border-radius: var(--radius-sm) !important; transition: all .12s;
}
.cr-pagination-wrap .page-item .page-link:hover { background: var(--accent-dim); border-color: var(--accent); color: var(--accent); }
.cr-pagination-wrap .page-item.active .page-link { background: var(--accent); border-color: var(--accent); color: #fff; font-weight: 700; }
.cr-pagination-wrap .page-item.disabled .page-link { opacity: .45; cursor: not-allowed; }

/* ── FULLSCREEN MODE ── */
.cr-table-card.is-fullscreen {
    position: fixed; inset: 0; z-index: 9999;
    border-radius: 0; border: none;
    display: flex; flex-direction: column; overflow: hidden;
    box-shadow: none;
}
.cr-table-card.is-fullscreen .cr-table-topbar  { flex-shrink: 0; }
.cr-table-card.is-fullscreen .cr-pagination-wrap { flex-shrink: 0; }

/* Kill horizontal scroll — fill width instead */
.cr-table-card.is-fullscreen .cr-scroll-wrap {
    max-height: none; flex: 1;
    overflow-x: hidden;
    overflow-y: auto;
}

/* Stretch table to full width with fixed layout */
.cr-table-card.is-fullscreen .cr-table {
    width: 100%;
    table-layout: fixed;
}

/* Compact header */
.cr-table-card.is-fullscreen .cr-table thead th {
    font-size: 9px; padding: 7px 4px;
    letter-spacing: 0; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}

/* Compact body cells */
.cr-table-card.is-fullscreen .cr-table tbody td {
    font-size: 11px; padding: 6px 4px;
    white-space: nowrap; overflow: hidden;
    text-overflow: ellipsis; max-width: 0;
}

/* Shrink chips */
.cr-table-card.is-fullscreen .chip { font-size: 9px; padding: 1px 5px; }

/* Shrink mono values */
.cr-table-card.is-fullscreen .amt-pos,
.cr-table-card.is-fullscreen .amt-neg,
.cr-table-card.is-fullscreen .amt-neutral,
.cr-table-card.is-fullscreen .amt-pending,
.cr-table-card.is-fullscreen .policy-no,
.cr-table-card.is-fullscreen .row-num { font-size: 10px; }

/* Shrink view button */
.cr-table-card.is-fullscreen .btn-view { font-size: 9px; padding: 3px 6px; gap: 3px; }

/* Column widths — all 18 columns, totals 100% */
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(1)  { width: 3%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(2)  { width: 6%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(3)  { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(4)  { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(5)  { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(6)  { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(7)  { width: 7%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(8)  { width: 7%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(9)  { width: 6%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(10) { width: 6%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(11) { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(12) { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(13) { width: 6%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(14) { width: 6%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(15) { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(16) { width: 6%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(17) { width: 5%; }
.cr-table-card.is-fullscreen .cr-table thead th:nth-child(18) { width: 4%; }

/* Empty state */
.cr-empty { text-align: center; padding: 72px 20px; }
.cr-empty i { font-size: 52px; color: var(--text-muted); margin-bottom: 14px; display: block; }
.cr-empty h5 { color: var(--text-secondary); font-weight: 600; margin-bottom: 6px; }
.cr-empty p  { color: var(--text-muted); font-size: 13px; }

@media (max-width: 768px) {
    .cr-stat-grid { grid-template-columns: repeat(2,1fr); }
    .cr-topbar { flex-direction: column; gap: 12px; }
}
</style>

<div class="cr-page">

    {{-- TOP BAR --}}
    <div class="cr-topbar">
        <div>
            <h2 class="cr-topbar-title">
                <span class="title-bar"></span>
                Comprehensive Commission Report
            </h2>
            <p class="cr-topbar-sub">Complete policy history across all uploaded months</p>
        </div>
        <a href="{{ route('commission.index') }}" class="cr-back-btn">
            <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to Dashboard
        </a>
    </div>

    {{-- FILTERS --}}
    <div class="cr-filter-card">
        <div class="cr-filter-header">
            <i class="fas fa-sliders-h"></i> Filter Options
        </div>
        <div class="cr-filter-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2 col-6">
                        <label class="form-label">From Month</label>
                        <select class="form-select" name="from_month">
                            <option value="">All</option>
                            @for($i=1;$i<=12;$i++)
                                <option value="{{ $i }}" {{ $fromMonth==$i?'selected':'' }}>{{ date('F',mktime(0,0,0,$i,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">From Year</label>
                        <select class="form-select" name="from_year">
                            <option value="">All</option>
                            @for($y=date('Y');$y>=2020;$y--)
                                <option value="{{ $y }}" {{ $fromYear==$y?'selected':'' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">To Month</label>
                        <select class="form-select" name="to_month">
                            @for($i=1;$i<=12;$i++)
                                <option value="{{ $i }}" {{ $toMonth==$i?'selected':'' }}>{{ date('F',mktime(0,0,0,$i,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">To Year</label>
                        <select class="form-select" name="to_year">
                            @for($y=date('Y');$y>=2020;$y--)
                                <option value="{{ $y }}" {{ $toYear==$y?'selected':'' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="all"     {{ $type=='all'    ?'selected':'' }}>All</option>
                            <option value="agents"  {{ $type=='agents' ?'selected':'' }}>Agents</option>
                            <option value="closers" {{ $type=='closers'?'selected':'' }}>Closers</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <button type="submit" class="btn-apply">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- STATS --}}
    <div class="cr-stat-grid">
        <div class="cr-stat-card c-blue">
            <div class="cr-stat-icon"><i class="fas fa-file-contract"></i></div>
            <div class="cr-stat-val">{{ $data->total() }}</div>
            <div class="cr-stat-label">Total Policies</div>
        </div>
        <div class="cr-stat-card c-green">
            <div class="cr-stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="cr-stat-val">${{ number_format($totals['total_revenue'],0) }}</div>
            <div class="cr-stat-label">Total Revenue</div>
        </div>
        <div class="cr-stat-card c-teal">
            <div class="cr-stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="cr-stat-val">${{ number_format($totals['monthly_premium'],0) }}</div>
            <div class="cr-stat-label">Total Monthly Premium</div>
        </div>
        <div class="cr-stat-card c-yellow">
            <div class="cr-stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="cr-stat-val">${{ number_format($totals['pending_amount'],0) }}</div>
            <div class="cr-stat-label">Total Pending</div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="cr-table-card" id="tableCard">

        <div class="cr-table-topbar">
            <div class="cr-table-title">
                <i class="fas fa-table"></i>
                All Policies — Complete History
                <span class="cr-badge">{{ $data->total() }} records</span>
            </div>
            <div class="cr-toolbar-right">

                {{-- Per-page --}}
                <form method="GET" id="perPageForm" class="cr-perpage-wrap">
                    <input type="hidden" name="from_month" value="{{ $fromMonth }}">
                    <input type="hidden" name="from_year"  value="{{ $fromYear }}">
                    <input type="hidden" name="to_month"   value="{{ $toMonth }}">
                    <input type="hidden" name="to_year"    value="{{ $toYear }}">
                    <input type="hidden" name="type"       value="{{ $type }}">
                    <span class="cr-perpage-label">Show</span>
                    <select name="per_page" class="cr-perpage-select" onchange="document.getElementById('perPageForm').submit()">
                        @foreach([25,50,100,200] as $pp)
                            <option value="{{ $pp }}" {{ $perPage==$pp?'selected':'' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                    <span class="cr-perpage-label">per page</span>
                </form>

                @if($data->count()>0)
                <span class="cr-info-text">{{ $data->firstItem() }}–{{ $data->lastItem() }} of {{ $data->total() }}</span>
                @endif

                {{-- Fullscreen --}}
                <button class="cr-fullscreen-btn" id="fullscreenBtn" onclick="toggleFullscreen()">
                    <svg id="fsIconExpand" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                    <svg id="fsIconCollapse" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
                    <span id="fsLabel">Full Screen</span>
                </button>
            </div>
        </div>

        @if($data->count()>0)

        {{-- SCROLL WRAP: both axes scroll, header frozen --}}
        <div class="cr-scroll-wrap" id="scrollWrap">
            <table class="cr-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Policy No.</th>
                        <th>Schedule Date</th>
                        <th>Process Date</th>
                        <th>Draft Date</th>
                        <th>Last Updated</th>
                        <th>Insured Name</th>
                        <th>CRM Name</th>
                        <th>Closer</th>
                        <th>Client</th>
                        <th>Monthly</th>
                        <th>Annual</th>
                        <th>Description</th>
                        <th>Calc. Commission</th>
                        <th>Calc. %</th>
                        <th>Total Revenue</th>
                        <th>Pending</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    @php $idx = $loop->index; @endphp
                    <tr>
                        <td><span class="row-num">{{ $data->firstItem()+$loop->index }}</span></td>
                        <td><span class="policy-no">{{ $row['policy_no'] }}</span></td>
                        <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ $row['schedule_date'] ? $row['schedule_date']->format('m/d/Y') : '—' }}</td>
                        <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ $row['process_date']  ? $row['process_date']->format('m/d/Y')  : '—' }}</td>
                        <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ $row['draft_date']    ? $row['draft_date']->format('m/d/Y')    : '—' }}</td>
                        <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ $row['last_updated']  ? $row['last_updated']->format('m/d/Y')  : '—' }}</td>
                        <td style="font-size:12px;color:var(--text-primary);">{{ $row['insured_name_excel'] ?? '—' }}</td>
                        <td>
                            @if($row['customer_full_name'])
                                <span class="chip chip-green">{{ $row['customer_full_name'] }}</span>
                            @else
                                <span class="chip chip-gray">Not in CRM</span>
                            @endif
                        </td>
                        <td>
                            @if($row['is_closer'])
                                <span class="chip chip-teal">{{ $row['closer_name'] }}</span>
                            @else
                                <span class="chip chip-gray">N/A</span>
                            @endif
                        </td>
                        <td style="font-size:12px;font-weight:500;color:var(--text-primary);">{{ $row['client_name'] }}</td>
                        <td><span class="amt-neutral">${{ number_format($row['monthly_premium'],2) }}</span></td>
                        <td><span class="amt-neutral">${{ number_format($row['annual_premium'],2) }}</span></td>
                        <td>
                            <span class="chip {{ str_contains(strtolower($row['description']),'advance') ? 'chip-yellow' : 'chip-blue' }}">
                                {{ $row['description'] }}
                            </span>
                        </td>
                        @php
                            // Get first statement's commission rate for theoretical calculation
                            $firstStmt = $row['statements']->first();
                            $commissionRate = (float) ($firstStmt->commission_rate ?? 0);
                            $monthlyPremium = (float) ($row['monthly_premium'] ?? 0);
                            $annualPremium  = (float) ($row['annual_premium'] ?? 0);
                            $totalRevenue   = (float) ($row['total_revenue'] ?? 0);
                            
                            // Calculated commission (theoretical): monthly × rate × 12 months
                            $calculatedCommission = $monthlyPremium * ($commissionRate / 100) * 12;
                            
                            // Calculated percentage: what % of annual premium is the theoretical commission
                            $calculatedPercentage = $annualPremium > 0 ? ($calculatedCommission / $annualPremium) * 100 : 0;
                        @endphp
                        <td>
                            <span class="{{ $calculatedCommission >= 0 ? 'amt-neutral' : 'amt-neg' }}" style="font-weight:600;">
                                ${{ number_format(abs($calculatedCommission), 2) }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:{{ $calculatedCommission >= 0 ? 'var(--purple)' : 'var(--red)' }};">
                                {{ number_format($calculatedPercentage, 1) }}%
                            </span>
                        </td>
                        <td><span class="{{ $totalRevenue >= 0 ? 'amt-pos' : 'amt-neg' }}">${{ number_format($totalRevenue, 2) }}</span></td>
                        <td><span class="amt-pending">${{ number_format($row['pending_amount'],2) }}</span></td>
                        <td>
                            <button class="btn-view" id="viewBtn-{{ $idx }}" onclick="toggleDetail({{ $idx }})">
                                <i class="fas fa-eye" style="font-size:10px;"></i> View
                            </button>
                        </td>
                    </tr>

                    {{-- Expand detail --}}
                    <tr class="cr-detail-row" id="detail-{{ $idx }}" style="display:none;">
                        <td colspan="18">
                            <div class="cr-detail-inner">
                                <div class="cr-detail-title">
                                    <i class="fas fa-list-ul"></i>
                                    Month-by-Month Breakdown —
                                    <span style="font-family:var(--font-mono);color:var(--accent);">{{ $row['policy_no'] }}</span>
                                </div>
                                <table class="cr-detail-table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Process Date</th>
                                            <th>Due Date</th>
                                            <th>Rate %</th>
                                            <th>Calculated Commission</th>
                                            <th>Commission Credit</th>
                                            <th>Match</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($row['statements'] as $stmt)
                                        @php
                                            // Safely parse numeric values with defaults
                                            $monthlyPremium = (float) ($row['monthly_premium'] ?? 0);
                                            $commissionRate = (float) ($stmt->commission_rate ?? 0);
                                            $actualComm     = (float) ($stmt->commission_credit ?? 0);
                                            
                                            // Calculate expected commission: monthly_premium × rate × 1 month
                                            $expectedComm = $monthlyPremium * ($commissionRate / 100);
                                            $isMatch      = abs($expectedComm - $actualComm) < 0.01; // within 1 cent
                                        @endphp
                                        <tr>
                                            <td style="font-family:var(--font-mono);font-weight:600;color:var(--text-primary);">{{ $stmt->month }}</td>
                                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ $stmt->process_date ? $stmt->process_date->format('m/d/Y') : '—' }}</td>
                                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ $stmt->due_date ? $stmt->due_date->format('m/d/Y') : '—' }}</td>
                                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary);">{{ number_format($commissionRate, 2) }}%</td>
                                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-muted);">${{ number_format($expectedComm, 2) }}</td>
                                            <td><span class="{{ $actualComm>=0 ? 'amt-pos' : 'amt-neg' }}">${{ number_format($actualComm, 2) }}</span></td>
                                            <td style="text-align:center;">
                                                @if($isMatch)
                                                    <span style="color:var(--green);font-size:14px;">✓</span>
                                                @else
                                                    <span style="color:var(--red);font-size:14px;">✗</span>
                                                @endif
                                            </td>
                                            <td style="font-size:12px;color:var(--text-secondary);">{{ $stmt->description }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" style="text-align:right;color:var(--text-secondary);font-size:12px;">
                                                <strong>Total for this policy:</strong>
                                            </td>
                                            <td><span class="{{ $row['total_revenue']>=0 ? 'amt-pos' : 'amt-neg' }}">${{ number_format($row['total_revenue'],2) }}</span></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        @if($row['annual_premium'] > 0)
                                        @php
                                            $annualPremium = (float) ($row['annual_premium'] ?? 0);
                                            $totalRevenue  = (float) ($row['total_revenue'] ?? 0);
                                            $paymentPercentage = $annualPremium > 0 ? ($totalRevenue / $annualPremium) * 100 : 0;
                                        @endphp
                                        <tr style="background:#f0f6ff;">
                                            <td colspan="5" style="text-align:right;color:var(--text-secondary);font-size:11px;padding:6px 12px;">
                                                Payment Progress (of Annual Premium ${{ number_format($annualPremium, 2) }}):
                                            </td>
                                            <td colspan="3" style="padding:6px 12px;">
                                                <div style="display:flex;align-items:center;gap:8px;">
                                                    <div style="flex:1;height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden;">
                                                        <div style="width:{{ min(100, $paymentPercentage) }}%;height:100%;background:var(--accent);transition:width .3s;"></div>
                                                    </div>
                                                    <span style="font-family:var(--font-mono);font-size:11px;font-weight:600;color:var(--accent);min-width:45px;">
                                                        {{ number_format($paymentPercentage, 1) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="cr-pagination-wrap">
            <span class="cr-pagination-info">
                Page {{ $data->currentPage() }} of {{ $data->lastPage() }}
                &nbsp;·&nbsp;
                {{ $data->firstItem() }}–{{ $data->lastItem() }} of {{ $data->total() }} records
            </span>
            {{ $data->appends([
                'from_month' => $fromMonth,
                'from_year'  => $fromYear,
                'to_month'   => $toMonth,
                'to_year'    => $toYear,
                'type'       => $type,
                'per_page'   => $perPage,
            ])->links('pagination::bootstrap-5') }}
        </div>

        @else
        <div class="cr-empty">
            <i class="fas fa-inbox"></i>
            <h5>No data found</h5>
            <p>Try adjusting your filters or upload more statements</p>
            <a href="{{ route('commission.index') }}" class="cr-back-btn" style="display:inline-flex;margin-top:12px;">
                <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to Dashboard
            </a>
        </div>
        @endif

    </div>{{-- .cr-table-card --}}

</div>{{-- .cr-page --}}

<script>
function toggleDetail(idx) {
    const row  = document.getElementById('detail-' + idx);
    const btn  = document.getElementById('viewBtn-' + idx);
    const open = row.style.display !== 'none';
    row.style.display = open ? 'none' : 'table-row';
    btn.classList.toggle('active', !open);
    btn.innerHTML = open
        ? '<i class="fas fa-eye" style="font-size:10px;"></i> View'
        : '<i class="fas fa-eye-slash" style="font-size:10px;"></i> Close';
}

function toggleFullscreen() {
    const card     = document.getElementById('tableCard');
    const expand   = document.getElementById('fsIconExpand');
    const collapse = document.getElementById('fsIconCollapse');
    const label    = document.getElementById('fsLabel');
    const wrap     = document.getElementById('scrollWrap');
    const isFs     = card.classList.toggle('is-fullscreen');

    expand.style.display   = isFs ? 'none'  : 'block';
    collapse.style.display = isFs ? 'block' : 'none';
    label.textContent      = isFs ? 'Exit Full Screen' : 'Full Screen';
    document.body.style.overflow = isFs ? 'hidden' : '';

    /* Normal mode: restore scrollable wrapper height & horizontal scroll */
    if (!isFs) {
        wrap.style.maxHeight  = '60vh';
        wrap.style.overflowX  = 'auto';
        wrap.style.overflowY  = 'auto';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const card = document.getElementById('tableCard');
        if (card && card.classList.contains('is-fullscreen')) toggleFullscreen();
    }
});
</script>

@endsection