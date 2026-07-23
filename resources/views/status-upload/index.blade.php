{{-- resources/views/status-upload/index.blade.php --}}
@extends('layouts.admin')

<style>
    :root {
        --ink:     #1a1a2e;
        --paper:   #f5f6fa;
        --muted:   #8492a6;
        --border:  #e2e8f0;
        --funded:  #16a34a;
        --lapse:   #d97706;
        --cb:      #dc2626;
        --blue:    #3b82f6;
        --tag-bg:  #f1f5f9;
        --white:   #ffffff;
    }

    * { box-sizing: border-box; }

    .su-wrap {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--paper);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
        font-size: 14px;
        color: var(--ink);
    }

    /* ── Page title ── */
    .su-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: .875rem;
        border-bottom: 2px solid var(--border);
    }
    .su-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        color: var(--ink);
        letter-spacing: -.3px;
    }
    .su-header span {
        font-size: .75rem;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 500;
    }

    /* ── Upload cards ── */
    .su-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    @media(max-width: 768px) { .su-grid { grid-template-columns: 1fr; } }

    .su-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .su-card-header {
        padding: .875rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .625rem;
        border-bottom: 1px solid var(--border);
        background: #fafbfc;
    }
    .su-card-header .badge {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .badge-lapse   { background: var(--lapse); }
    .badge-advance { background: var(--blue); }
    .su-card-header h2 {
        font-size: .9375rem;
        font-weight: 600;
        margin: 0;
        color: var(--ink);
        flex: 1;
    }
    .su-card-header .chip {
        font-size: .7rem;
        background: var(--tag-bg);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 2px 10px;
        color: var(--muted);
        font-weight: 500;
    }
    .su-card-body { padding: 1.25rem; }

    /* Rules list */
    .su-rules {
        list-style: none;
        padding: 0;
        margin: 0 0 1.25rem;
    }
    .su-rules li {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        font-size: .8125rem;
        color: var(--muted);
        margin-bottom: .4rem;
        line-height: 1.5;
    }
    .su-rules li::before {
        content: '›';
        flex-shrink: 0;
        color: var(--blue);
        font-size: .9rem;
        font-weight: 700;
        margin-top: 0px;
    }
    .su-rules .tag-funded  { color: var(--funded);  font-weight: 600; }
    .su-rules .tag-lapse   { color: var(--lapse);   font-weight: 600; }
    .su-rules .tag-cb      { color: var(--cb);      font-weight: 600; }

    /* Drop zone */
    .drop-zone {
        border: 2px dashed var(--border);
        border-radius: 8px;
        padding: 1.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all .2s ease;
        background: var(--paper);
        margin-bottom: .875rem;
        position: relative;
    }
    .drop-zone:hover {
        border-color: var(--blue);
        background: #eff6ff;
    }
    .drop-zone.drag-over {
        border-color: var(--blue);
        background: #eff6ff;
        transform: scale(1.01);
    }
    .drop-zone.file-selected {
        border-color: var(--funded);
        background: #f0fdf4;
        border-style: solid;
    }
    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .drop-zone .dz-icon {
        font-size: 2rem;
        margin-bottom: .375rem;
        display: block;
        transition: transform .2s;
    }
    .drop-zone.file-selected .dz-icon {
        transform: scale(.85);
    }
    .drop-zone .dz-label {
        font-size: .8125rem;
        color: var(--muted);
        display: block;
        font-weight: 400;
    }
    .drop-zone.file-selected .dz-label {
        display: none;
    }
    .drop-zone .dz-filename {
        font-size: .8125rem;
        color: var(--funded);
        font-weight: 600;
        display: none;
        margin-top: .25rem;
        word-break: break-all;
    }
    .drop-zone.file-selected .dz-filename {
        display: block;
    }
    .drop-zone .dz-change {
        font-size: .75rem;
        color: var(--muted);
        display: none;
        margin-top: .2rem;
    }
    .drop-zone.file-selected .dz-change {
        display: block;
    }
    .dz-selected-badge {
        display: none;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        background: #dcfce7;
        color: var(--funded);
        font-size: .7rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        margin: .4rem auto 0;
        width: fit-content;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .drop-zone.file-selected .dz-selected-badge {
        display: flex;
    }

    /* Buttons */
    .btn-upload {
        width: 100%;
        padding: .625rem 1rem;
        background: #cbd5e1;
        color: #94a3b8;
        border: none;
        border-radius: 6px;
        font-size: .8125rem;
        font-weight: 600;
        letter-spacing: .3px;
        cursor: not-allowed;
        transition: all .2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .btn-upload.ready {
        background: var(--ink);
        color: #fff;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,.15);
    }
    .btn-upload.ready:hover {
        background: #2d3748;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,.2);
    }
    .btn-upload.ready:active {
        transform: translateY(0);
    }
    .btn-upload:disabled {
        opacity: .6;
        cursor: not-allowed;
        transform: none !important;
    }
    .btn-upload.processing {
        background: var(--blue);
        color: #fff;
        cursor: not-allowed;
    }
    .btn-upload.processing::after {
        content: '';
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Alerts ── */
    .su-alert {
        border-radius: 6px;
        padding: .75rem 1rem;
        font-size: .8125rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: .625rem;
        font-weight: 500;
    }
    .su-alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    .su-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .su-alert-icon { font-size: 1rem; flex-shrink: 0; }

    /* ── Log table ── */
    .su-log-section { margin-top: 2rem; }
    .su-log-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: .75rem;
    }
    .su-log-header h3 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: var(--ink);
    }
    .su-filters {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .su-filters input,
    .su-filters select {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .375rem .625rem;
        background: var(--white);
        color: var(--ink);
        outline: none;
    }
    .su-filters input:focus,
    .su-filters select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 2px rgba(59,130,246,.1);
    }
    .su-filters button {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        font-weight: 600;
        padding: .375rem .875rem;
        background: var(--ink);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .su-filters button:hover { opacity: .85; }

    .su-table-wrap {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    table.su-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .8125rem;
    }
    .su-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid var(--border);
    }
    .su-table th {
        padding: .75rem .875rem;
        text-align: left;
        font-size: .75rem;
        letter-spacing: .5px;
        text-transform: uppercase;
        font-weight: 600;
        white-space: nowrap;
        color: var(--muted);
    }
    .su-table td {
        padding: .625rem .875rem;
        border-bottom: 1px solid var(--border);
        color: var(--ink);
        vertical-align: middle;
    }
    .su-table tbody tr:last-child td { border-bottom: none; }
    .su-table tbody tr:hover { background: #fafbfc; }

    /* Status pills */
    .status-pill {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: .7rem;
        letter-spacing: .3px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .pill-funded        { background: #dcfce7; color: #166534; }
    .pill-lapse         { background: #fef9c3; color: #854d0e; }
    .pill-cb            { background: #fee2e2; color: #991b1b; }
    .pill-pending       { background: #e0e7ff; color: #3730a3; }
    .pill-default       { background: var(--tag-bg); color: var(--muted); }

    /* Source tag */
    .src-tag {
        font-size: .7rem;
        padding: 3px 9px;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .src-lapse   { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
    .src-advance { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

    /* Arrow */
    .change-arrow {
        display: flex;
        align-items: center;
        gap: .375rem;
        white-space: nowrap;
    }
    .change-arrow .arrow-sep { font-size: .75rem; color: var(--muted); }

    .btn-del {
        background: none;
        border: 1px solid #fecaca;
        color: var(--cb);
        border-radius: 5px;
        padding: 3px 9px;
        font-size: .75rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        cursor: pointer;
        transition: background .1s;
        font-weight: 500;
    }
    .btn-del:hover { background: #fee2e2; }

    /* Empty state */
    .su-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--muted);
    }
    .su-empty .su-empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
    .su-empty p { font-size: .875rem; margin: 0; font-weight: 500; }

    /* Batch callout */
    .batch-callout {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: .625rem 1rem;
        font-size: .8125rem;
        color: #166534;
        margin-bottom: 1.25rem;
        font-weight: 500;
    }
    .batch-callout strong { font-weight: 700; }

    /* Pagination */
    .su-pagination {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }
    .su-pagination .pagination { margin: 0; }

    /* ── Unmatched Records Section ── */
    .unmatched-section {
        margin-top: 2rem;
        border: 1px solid #fde68a;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .unmatched-header {
        background: #fffbeb;
        border-bottom: 1px solid #fde68a;
        padding: .875rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .unmatched-header h3 {
        margin: 0;
        font-size: .9375rem;
        font-weight: 600;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .unmatched-count {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: .75rem;
        font-weight: 700;
    }

    /* ── Missing Policy ID Section ── */
    .missing-section {
        margin-top: 2rem;
        border: 1px solid #fecaca;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .missing-header {
        background: #fef2f2;
        border-bottom: 1px solid #fecaca;
        padding: .875rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
    }
    .missing-header h3 {
        margin: 0;
        font-size: .9375rem;
        font-weight: 600;
        color: #991b1b;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .missing-count {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: .75rem;
        font-weight: 700;
    }
    .missing-filters {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        align-items: center;
        padding: .875rem 1.25rem;
        background: #fafafa;
        border-bottom: 1px solid var(--border);
    }
    .missing-filters input,
    .missing-filters select {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .375rem .625rem;
        background: var(--white);
        color: var(--ink);
        outline: none;
    }
    .missing-filters input:focus,
    .missing-filters select:focus {
        border-color: var(--cb);
        box-shadow: 0 0 0 2px rgba(220,38,38,.1);
    }
    .btn-filter-missing {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        font-weight: 600;
        padding: .375rem .875rem;
        background: var(--ink);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-filter-missing:hover { opacity: .85; }
    .btn-export {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        font-weight: 600;
        padding: .375rem .875rem;
        background: var(--cb);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: opacity .15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .btn-export:hover { opacity: .85; color: #fff; }
</style>

@section('content')
<div class="su-wrap">

    {{-- ── Header ── --}}
    <div class="su-header">
        <h1>Status Bulk Update</h1>
        <span>Upload Reports · Audit Log</span>
        <a href="{{ route('status-upload.client-report') }}"
           style="margin-left:auto; font-size:.8125rem; color:var(--blue); text-decoration:none; font-weight:500; display:flex; align-items:center; gap:.25rem; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:.35rem .75rem;">
            &#128202; Client Report
        </a>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('success'))
    <div class="su-alert su-alert-success">
        <span class="su-alert-icon">✓</span>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="su-alert su-alert-error">
        <span class="su-alert-icon">✕</span>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="su-alert su-alert-error">
        <span class="su-alert-icon">✕</span>
        <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    </div>
    @endif

    @if(session('batch'))
    <div class="batch-callout">
        ✓ Upload complete — Batch ID: <strong>{{ session('batch') }}</strong>. Filter the log below to see what changed.
    </div>
    @endif

    {{-- ── Upload cards ── --}}
    <div class="su-grid">

        {{-- Card 1: Lapse Report --}}
        <div class="su-card">
            <div class="su-card-header">
                <div class="badge badge-lapse"></div>
                <h2>Lapse Report</h2>
                <span class="chip">Policy Status</span>
            </div>
            <div class="su-card-body">
                <ul class="su-rules">
                    <li>Matches records by <strong>Policy #</strong> column</li>
                    <li>If <strong>Paid To Date</strong> ≥ today → sets status to <span class="tag-funded">Funded</span></li>
                    <li>If <strong>Paid To Date</strong> &lt; today → sets status to <span class="tag-lapse">Potential Lapsed</span></li>
                    <li>Accepted formats: .xlsx, .xls, .csv</li>
                </ul>

                <form action="{{ url('status-upload/lapse-report') }}" method="POST" enctype="multipart/form-data" id="form-lapse">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="drop-zone" id="dz-lapse">
                        <input type="file" name="lapse_file" accept=".xlsx,.xls,.csv" id="file-lapse">
                        <span class="dz-icon">📋</span>
                        <span class="dz-label">Drop file here or click to browse</span>
                        <span class="dz-filename" id="fn-lapse"></span>
                        <span class="dz-change">Click to change file</span>
                        <span class="dz-selected-badge">✓ File Ready</span>
                    </div>
                    <button type="submit" class="btn-upload" id="btn-lapse" disabled>
                        <span class="btn-icon">⬆</span>
                        <span class="btn-text">Select a file to upload</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Card 2: Monthly Advance Summary --}}
        <div class="su-card">
            <div class="su-card-header">
                <div class="badge badge-advance"></div>
                <h2>Monthly Advance Summary</h2>
                <span class="chip">Commission Status</span>
            </div>
            <div class="su-card-body">
                <ul class="su-rules">
                    <li>Matches records by <strong>Policy Number</strong> column</li>
                    <li>If Description is <em>Advance / As Earned / Balance Correction Advance</em> → sets status to <span class="tag-funded">Funded</span></li>
                    <li>If Description is <em>ClawBack / Chargeback</em> → sets status to <span class="tag-cb">Charged Back</span></li>
                    <li>Other descriptions are skipped</li>
                </ul>

                <form action="{{ url('status-upload/monthly-advance') }}" method="POST" enctype="multipart/form-data" id="form-advance">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="drop-zone" id="dz-advance">
                        <input type="file" name="advance_file" accept=".xlsx,.xls,.csv" id="file-advance">
                        <span class="dz-icon">📊</span>
                        <span class="dz-label">Drop file here or click to browse</span>
                        <span class="dz-filename" id="fn-advance"></span>
                        <span class="dz-change">Click to change file</span>
                        <span class="dz-selected-badge">✓ File Ready</span>
                    </div>
                    <button type="submit" class="btn-upload" id="btn-advance" disabled>
                        <span class="btn-icon">⬆</span>
                        <span class="btn-text">Select a file to upload</span>
                    </button>
                </form>
            </div>
        </div>

    </div>{{-- /su-grid --}}


    {{-- ── Audit Log Section ── --}}
    <div class="su-log-section">
        <div class="su-log-header">
            <h3>Change Audit Log</h3>

            <form method="GET" class="su-filters" action="{{ url('status-upload') }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search policy, name, status…">
                <select name="source_filter">
                    <option value="all" @selected(!$source || $source==='all')>All Sources</option>
                    <option value="lapse_report"    @selected($source==='lapse_report')>Lapse Report</option>
                    <option value="monthly_advance" @selected($source==='monthly_advance')>Monthly Advance</option>
                </select>
                <select name="batch_filter">
                    <option value="">All Batches</option>
                    @foreach($batches as $b)
                    <option value="{{ $b['batch'] }}" @selected($batch===$b['batch'])>
                        {{ ($b['source'] ?? '') === 'lapse_report' ? 'Lapse' : 'Advance' }}
                        — {{ \Carbon\Carbon::parse($b['changed_at'])->format('d M Y H:i') }}
                        ({{ $b['count'] }} changes)
                    </option>
                    @endforeach
                </select>
                <select name="per_page">
                    @foreach([20,50,100] as $pp)
                    <option value="{{ $pp }}" @selected($perPage==$pp)>{{ $pp }} / page</option>
                    @endforeach
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <div class="su-table-wrap">
            @if($logs->count())
            <table class="su-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Record ID</th>
                        <th>Policy ID</th>
                        <th>Customer</th>
                        <th>Status Change</th>
                        <th>Detail</th>
                        <th>Changed By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td style="color:var(--muted); font-size:.75rem;">{{ $log->id }}</td>
                        <td style="font-size:.75rem; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($log->changed_at)->format('d M Y') }}<br>
                            <span style="color:var(--muted)">{{ \Carbon\Carbon::parse($log->changed_at)->format('H:i:s') }}</span>
                        </td>
                        <td>
                            @if($log->source_file === 'lapse_report')
                                <span class="src-tag src-lapse">Lapse</span>
                            @else
                                <span class="src-tag src-advance">Advance</span>
                            @endif
                        </td>
                        <td>
                            @if($log->closedCall)
                                <a href="{{ route('closed-calls.show', $log->closed_call_id) }}" style="color:var(--blue); font-size:.8rem; font-weight:600; text-decoration:none;">
                                    #{{ $log->closed_call_id }}
                                </a>
                            @else
                                <span style="color:var(--muted); font-size:.8rem;">#{{ $log->closed_call_id }}</span>
                            @endif
                        </td>
                        <td style="font-size:.8rem;">{{ $log->policy_id ?? '—' }}</td>
                        <td style="font-size:.8125rem; font-weight:500;">{{ $log->customer_name ?? '—' }}</td>
                        <td>
    <div class="change-arrow" style="flex-wrap:wrap; gap:.3rem;">
        @php
            $chain       = $statusChains[$log->closed_call_id] ?? collect([$log]);
            $allStatuses = collect([$chain->first()->old_status]);
            foreach ($chain as $entry) { $allStatuses->push($entry->new_status); }
            $allStatuses = $allStatuses->filter()->unique()->values();
        @endphp
        @foreach($allStatuses as $status)
            @php
                $s   = strtolower($status ?? '');
                $cls = str_contains($s,'fund') ? 'pill-funded'
                     : (str_contains($s,'lapse') ? 'pill-lapse'
                     : ((str_contains($s,'charge')||str_contains($s,'claw')) ? 'pill-cb'
                     : ($s==='pending' ? 'pill-pending' : 'pill-default')));
            @endphp
            <span class="status-pill {{ $cls }}">{{ $status }}</span>
            @if(!$loop->last)<span class="arrow-sep">→</span>@endif
        @endforeach
    </div>
</td>
                        <td style="font-size:.75rem; color:var(--muted);">
                            @if($log->paid_to_date)
                                Paid To: {{ $log->paid_to_date }}
                            @elseif($log->description)
                                {{ \Str::limit($log->description, 30) }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="font-size:.8rem; font-weight:500;">{{ $log->changedBy?->name ?? '—' }}</td>
                        <td>
                            <form action="{{ url('status-upload/log/' . $log->id) }}" method="POST" onsubmit="return confirm('Delete this log entry?')">
                                @csrf @method('DELETE')
                                <button class="btn-del" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="su-empty">
                <div class="su-empty-icon">📭</div>
                <p>No change logs found. Upload a report above to get started.</p>
            </div>
            @endif
        </div>

        @if($logs->hasPages())
        <div class="su-pagination">
            {{ $logs->appends(['search' => $search, 'source_filter' => $source, 'batch_filter' => $batch, 'per_page' => $perPage])->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

    {{-- ── Unmatched Records from Last Upload (session-based) ── --}}
    @if(session('unmatched_lapse_count') > 0 || session('unmatched_advance_count') > 0)
    <div class="unmatched-section">
        <div class="unmatched-header">
            <h3>&#9888; Unmatched Records from Last Upload</h3>
            <span style="font-size:.75rem; color:#92400e; background:#fef3c7; border:1px solid #fde68a; padding:3px 10px; border-radius:20px;">
                &#9888; These will clear on your next upload — export now to save
            </span>
        </div>

        @if(session('unmatched_lapse_count') > 0)
        <div style="padding:.75rem 1.25rem; border-bottom:1px solid #fde68a; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; background:#fffdf5;">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <span class="src-tag src-lapse">Lapse Report</span>
                <span style="font-size:.8rem; color:#92400e; font-weight:600;">{{ session('unmatched_lapse_count') }} unmatched policy IDs</span>
            </div>
            <a href="{{ url('status-upload/export-unmatched-lapse') }}" class="btn-export">
                &#8595; Export CSV
            </a>
        </div>

        <div style="overflow:auto;">
            <table class="su-table">
                <thead>
                    <tr><th>#</th><th>Policy ID</th><th>Paid To Date</th><th>Owner</th></tr>
                </thead>
                <tbody>
                    @foreach(session('unmatched_lapse', []) as $i => $row)
                    <tr>
                        <td style="color:var(--muted); font-size:.75rem;">{{ $i + 1 }}</td>
                        <td style="font-size:.8rem; font-weight:600; color:#92400e;">{{ $row['policy_id'] }}</td>
                        <td style="font-size:.8rem;">{{ $row['paid_to_date'] }}</td>
                        <td style="font-size:.8rem;">{{ $row['owner'] ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if(session('unmatched_advance_count') > 0)
        <div style="padding:.75rem 1.25rem; border-bottom:1px solid #fde68a; border-top:{{ session('unmatched_lapse_count') > 0 ? '1px solid #fde68a' : 'none' }}; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; background:#fffdf5;">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <span class="src-tag src-advance">Monthly Advance</span>
                <span style="font-size:.8rem; color:#92400e; font-weight:600;">{{ session('unmatched_advance_count') }} unmatched policy IDs</span>
            </div>
            <a href="{{ url('status-upload/export-unmatched-advance') }}" class="btn-export">
                &#8595; Export CSV
            </a>
        </div>

        <div style="overflow:auto;">
            <table class="su-table">
                <thead>
                    <tr><th>#</th><th>Policy ID</th><th>Description</th><th>Insured</th></tr>
                </thead>
                <tbody>
                    @foreach(session('unmatched_advance', []) as $i => $row)
                    <tr>
                        <td style="color:var(--muted); font-size:.75rem;">{{ $i + 1 }}</td>
                        <td style="font-size:.8rem; font-weight:600; color:#92400e;">{{ $row['policy_id'] }}</td>
                        <td style="font-size:.8rem;">{{ $row['description'] }}</td>
                        <td style="font-size:.8rem;">{{ $row['insured'] ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    {{-- Missing Policy ID Section --}}
    <div class="missing-section">
        <div class="missing-header">
            <h3>Approved Records Missing Policy ID
                <span class="missing-count">{{ $missingTotalCount }} total</span>
            </h3>
            <a href="{{ url('status-upload/export-missing-policy-ids') }}?missing_status={{ $missingStatus }}&missing_start_date={{ $missingStartDate }}&missing_end_date={{ $missingEndDate }}"
               class="btn-export">Export CSV</a>
        </div>

        <form method="GET" action="{{ url('status-upload') }}" class="missing-filters">
            <input type="hidden" name="search"        value="{{ $search }}">
            <input type="hidden" name="source_filter" value="{{ $source }}">
            <input type="hidden" name="batch_filter"  value="{{ $batch }}">
            <input type="hidden" name="per_page"      value="{{ $perPage }}">
            <select name="missing_status">
                <option value="">All Statuses</option>
                @foreach(['Funded','charged_backed','Approved','Potential Lapsed'] as $st)
                <option value="{{ $st }}" @selected($missingStatus === $st)>{{ $st }}</option>
                @endforeach
            </select>
            <input type="date" name="missing_start_date" value="{{ $missingStartDate }}">
            <input type="date" name="missing_end_date"   value="{{ $missingEndDate }}">
            <select name="missing_per_page">
                @foreach([20,50,100] as $pp)
                <option value="{{ $pp }}" @selected($missingPerPage == $pp)>{{ $pp }} / page</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter-missing">Filter</button>
            @if($missingStatus || $missingStartDate || $missingEndDate)
            <a href="{{ url('status-upload') }}" style="font-size:.8rem; color:var(--muted); text-decoration:none; padding:.375rem .5rem;">Clear</a>
            @endif
        </form>

        <div style="overflow:auto;">
            @if($missingRecords->count())
            <table class="su-table">
                <thead>
                    <tr>
                        <th>Record ID</th><th>Date</th><th>Customer</th><th>Phone</th>
                        <th>State</th><th>Status</th><th>Carrier</th><th>Premium</th>
                        <th>Closer</th><th>Center</th><th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($missingRecords as $rec)
                    <tr>
                        <td style="font-size:.75rem; color:var(--muted);">{{ $rec->id }}</td>
                        <td style="font-size:.75rem; white-space:nowrap;">{{ $rec->created_at->format('d M Y') }}</td>
                        <td style="font-size:.8125rem; font-weight:600;">{{ $rec->customer_full_name ?? '-' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->phone_number ?? '-' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->state ?? '-' }}</td>
                        <td>
                            @php
                                $s = strtolower($rec->status ?? '');
                                $cls = str_contains($s,'fund') ? 'pill-funded' : (str_contains($s,'lapse') ? 'pill-lapse' : ((str_contains($s,'charge')||str_contains($s,'claw')) ? 'pill-cb' : 'pill-pending'));
                            @endphp
                            <span class="status-pill {{ $cls }}">{{ $rec->status }}</span>
                        </td>
                        <td style="font-size:.8rem;">{{ $rec->carrier ?? '-' }}</td>
<td style="font-size:.8rem;">${{ number_format((float)($rec->monthly_premium ?? 0), 2) }}</td>                        <td style="font-size:.8rem;">{{ $rec->closername ?? '-' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->center_name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('closed-calls.show', $rec->id) }}"
                               style="color:var(--blue); font-size:.8rem; font-weight:600; text-decoration:none;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="su-empty">
                <div class="su-empty-icon">&#10003;</div>
                <p>No approved records with missing policy IDs{{ ($missingStatus || $missingStartDate || $missingEndDate) ? ' for selected filters' : '' }}.</p>
            </div>
            @endif
        </div>

        @if($missingRecords->hasPages())
        <div class="su-pagination" style="padding:.75rem 1rem;">
            {{ $missingRecords->appends([
                'missing_status' => $missingStatus, 'missing_start_date' => $missingStartDate,
                'missing_end_date' => $missingEndDate, 'missing_per_page' => $missingPerPage,
                'search' => $search, 'source_filter' => $source,
                'batch_filter' => $batch, 'per_page' => $perPage,
            ])->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
<script>
['lapse','advance'].forEach(type => {
    const dz  = document.getElementById(`dz-${type}`);
    const fi  = document.getElementById(`file-${type}`);
    const fn  = document.getElementById(`fn-${type}`);
    const btn = document.getElementById(`btn-${type}`);
    const frm = document.getElementById(`form-${type}`);
    const btnText = btn.querySelector('.btn-text');
    const btnIcon = btn.querySelector('.btn-icon');

    function onFileSelected(file) {
        if (!file) return;
        // Update filename display
        fn.textContent = file.name;
        // Activate drop zone selected state
        dz.classList.add('file-selected');
        // Activate button
        btn.disabled = false;
        btn.classList.add('ready');
        btnIcon.textContent = '⬆';
        btnText.textContent = 'Upload ' + file.name;
    }

    fi.addEventListener('change', () => {
        if (fi.files[0]) onFileSelected(fi.files[0]);
    });

    dz.addEventListener('dragover', e => {
        e.preventDefault();
        dz.classList.add('drag-over');
    });
    dz.addEventListener('dragleave', () => {
        dz.classList.remove('drag-over');
    });
    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('drag-over');
        if (e.dataTransfer.files[0]) {
            // Assign dropped file to input
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            fi.files = dt.files;
            onFileSelected(e.dataTransfer.files[0]);
        }
    });

    frm.addEventListener('submit', () => {
        btn.disabled = true;
        btn.classList.remove('ready');
        btn.classList.add('processing');
        btnText.textContent = 'Processing…';
        btnIcon.textContent = '';
    });
});
</script>
@endsection
@push('scripts')

@endpush