{{-- resources/views/status-upload/client-report.blade.php --}}
@extends('layouts.admin')

<style>
    :root {
        --ink:    #1a1a2e;
        --paper:  #f5f6fa;
        --muted:  #8492a6;
        --border: #e2e8f0;
        --blue:   #3b82f6;
        --funded: #16a34a;
        --lapse:  #d97706;
        --cb:     #dc2626;
        --white:  #ffffff;
    }
    * { box-sizing: border-box; }

    .cr-wrap {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--paper);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
        font-size: 14px;
        color: var(--ink);
    }

    /* ── Header ── */
    .cr-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: .875rem;
        border-bottom: 2px solid var(--border);
    }
    .cr-header h1 { font-size: 1.5rem; font-weight: 600; margin: 0; color: var(--ink); letter-spacing: -.3px; }
    .cr-header .sub { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 500; }
    .cr-header .back-link {
        margin-left: auto;
        font-size: .8125rem;
        color: var(--blue);
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: .25rem;
    }
    .cr-header .back-link:hover { text-decoration: underline; }

    /* ── Filter card ── */
    .cr-filter-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .cr-filter-card h2 {
        font-size: .9375rem;
        font-weight: 600;
        margin: 0 0 1rem;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .cr-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
        gap: .75rem;
        align-items: end;
    }
    @media(max-width: 900px) {
        .cr-filter-grid { grid-template-columns: 1fr 1fr; }
    }
    @media(max-width: 540px) {
        .cr-filter-grid { grid-template-columns: 1fr; }
    }
    .cr-field label {
        display: block;
        font-size: .75rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: .35rem;
    }
    .cr-field select,
    .cr-field input[type="date"] {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .45rem .75rem;
        background: var(--white);
        color: var(--ink);
        outline: none;
        width: 100%;
    }
    .cr-field select:focus,
    .cr-field input[type="date"]:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 2px rgba(59,130,246,.1);
    }
    .cr-filter-actions {
        display: flex;
        gap: .5rem;
        align-items: flex-end;
        padding-bottom: 1px;
    }
    .btn-filter {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem;
        font-weight: 600;
        padding: .475rem 1.125rem;
        background: var(--ink);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: opacity .15s;
        white-space: nowrap;
    }
    .btn-filter:hover { opacity: .85; }
    .btn-clear {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        font-weight: 500;
        padding: .475rem .875rem;
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--border);
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all .15s;
        white-space: nowrap;
    }
    .btn-clear:hover { border-color: var(--muted); color: var(--ink); }

    /* ── Results header ── */
    .cr-results-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: .875rem;
    }
    .cr-results-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .cr-count-badge {
        background: #e0e7ff;
        color: #3730a3;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: .75rem;
        font-weight: 700;
    }
    .btn-export {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        font-weight: 600;
        padding: .4rem .875rem;
        background: var(--funded);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        transition: opacity .15s;
    }
    .btn-export:hover { opacity: .85; color: #fff; }

    /* ── Table ── */
    .cr-table-wrap {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    table.cr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .8125rem;
    }
    .cr-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid var(--border);
    }
    .cr-table th {
        padding: .75rem .875rem;
        text-align: left;
        font-size: .75rem;
        letter-spacing: .5px;
        text-transform: uppercase;
        font-weight: 600;
        white-space: nowrap;
        color: var(--muted);
    }
    .cr-table td {
        padding: .6rem .875rem;
        border-bottom: 1px solid var(--border);
        color: var(--ink);
        vertical-align: middle;
    }
    .cr-table tbody tr:last-child td { border-bottom: none; }
    .cr-table tbody tr:hover { background: #fafbfc; }

    /* Status pills */
    .status-pill {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: .7rem;
        letter-spacing: .3px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pill-funded   { background: #dcfce7; color: #166534; }
    .pill-lapse    { background: #fef9c3; color: #854d0e; }
    .pill-cb       { background: #fee2e2; color: #991b1b; }
    .pill-pending  { background: #e0e7ff; color: #3730a3; }
    .pill-approved { background: #d1fae5; color: #065f46; }
    .pill-rejected { background: #fce7f3; color: #9d174d; }
    .pill-default  { background: #f1f5f9; color: var(--muted); }

    /* Empty / placeholder */
    .cr-empty {
        text-align: center;
        padding: 3.5rem 1rem;
        color: var(--muted);
    }
    .cr-empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
    .cr-empty p { font-size: .875rem; margin: 0; font-weight: 500; }

    /* Placeholder prompt */
    .cr-prompt {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 3.5rem 1.5rem;
        text-align: center;
        color: var(--muted);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .cr-prompt-icon { font-size: 3rem; margin-bottom: .75rem; }
    .cr-prompt h3 { font-size: 1rem; font-weight: 600; color: var(--ink); margin: 0 0 .35rem; }
    .cr-prompt p  { font-size: .8375rem; margin: 0; }

    /* Pagination */
    .cr-pagination {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }
    .cr-pagination .pagination { margin: 0; }

    /* Per-page selector inline */
    .per-page-wrap {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .8rem;
        color: var(--muted);
    }
    .per-page-wrap select {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: .25rem .5rem;
        background: var(--white);
        color: var(--ink);
        outline: none;
    }
</style>

@section('content')
<div class="cr-wrap">

    {{-- ── Header ── --}}
    <div class="cr-header">
        <div>
            <h1>Client Report</h1>
            <span class="sub">Filter records by client &amp; date range</span>
        </div>
        <a href="{{ route('status-upload.index') }}" class="back-link">&#8592; Back to Status Upload</a>
    </div>

    {{-- ── Filter Form ── --}}
    <div class="cr-filter-card">
        <h2>&#128269; Search Filters</h2>
        <form method="GET" action="{{ route('status-upload.client-report') }}" id="filter-form">
            <div class="cr-filter-grid">

                {{-- Client Dropdown --}}
                <div class="cr-field">
                    <label>Client</label>
                    <select name="client_id">
                        <option value="">-- All Clients --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected((string)$clientId === (string)$c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div class="cr-field">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}">
                </div>

                {{-- End Date --}}
                <div class="cr-field">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}">
                </div>

                {{-- Status --}}
                <div class="cr-field">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Per Page --}}
                <div class="cr-field">
                    <label>Per Page</label>
                    <select name="per_page">
                        @foreach([20, 50, 100] as $pp)
                            <option value="{{ $pp }}" @selected($perPage == $pp)>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="cr-filter-actions">
                    <button type="submit" class="btn-filter">Search</button>
                    <a href="{{ route('status-upload.client-report') }}" class="btn-clear">Clear</a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Results ── --}}
    @if($records !== null)

        {{-- Results header row --}}
        <div class="cr-results-header">
            <div class="cr-results-title">
                Results
                <span class="cr-count-badge">{{ $records->total() }} record{{ $records->total() !== 1 ? 's' : '' }}</span>
            </div>
            <a href="{{ route('status-upload.export.client-report') }}?{{ http_build_query(['client_id' => $clientId, 'start_date' => $startDate, 'end_date' => $endDate, 'status' => $status]) }}"
               class="btn-export">
                &#8595; Export CSV
            </a>
        </div>

        {{-- Table --}}
        <div class="cr-table-wrap">
            @if($records->count())
            <table class="cr-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Policy ID</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Closer</th>
                        <th>Center</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $rec)
                    <tr>
                        <td style="color:var(--muted); font-size:.75rem;">{{ $rec->id }}</td>
                        <td style="font-size:.75rem; white-space:nowrap;">
                            {{ $rec->created_at->format('d M Y') }}<br>
                            <span style="color:var(--muted);">{{ $rec->created_at->format('H:i') }}</span>
                        </td>
                        <td style="font-size:.8rem; font-weight:600; color:var(--blue);">
                            {{ $rec->client?->name ?? '—' }}
                        </td>
                        <td style="font-size:.8125rem; font-weight:500;">{{ $rec->customer_full_name ?? '—' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->phone_number ?? '—' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->state ?? '—' }}</td>
                        <td>
                            @php
                                $s   = strtolower($rec->status ?? '');
                                $cls = str_contains($s, 'fund')    ? 'pill-funded'
                                     : (str_contains($s, 'lapse')  ? 'pill-lapse'
                                     : ((str_contains($s,'charge') || str_contains($s,'claw')) ? 'pill-cb'
                                     : (str_contains($s,'reject')  ? 'pill-rejected'
                                     : (str_contains($s,'approv')  ? 'pill-approved'
                                     : ($s === 'pending'           ? 'pill-pending'
                                     : 'pill-default')))));
                            @endphp
                            <span class="status-pill {{ $cls }}">{{ $rec->status ?? '—' }}</span>
                        </td>
                        <td style="font-size:.8rem; font-family:monospace;">{{ $rec->policy_id ?: '—' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->carrier ?? '—' }}</td>
                        <td style="font-size:.8rem;">${{ number_format((float)($rec->monthly_premium ?? 0), 2) }}</td>
                        <td style="font-size:.8rem;">{{ $rec->closername ?? '—' }}</td>
                        <td style="font-size:.8rem;">{{ $rec->center_name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('closed-calls.show', $rec->id) }}"
                               style="color:var(--blue); font-size:.8rem; font-weight:600; text-decoration:none;">
                               View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="cr-empty">
                <div class="cr-empty-icon">&#128269;</div>
                <p>No records found for the selected filters.</p>
            </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($records->hasPages())
        <div class="cr-pagination">
            {{ $records->appends([
                'client_id'  => $clientId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => $status,
                'per_page'   => $perPage,
            ])->links('pagination::bootstrap-5') }}
        </div>
        @endif

    @else

        {{-- Initial state — no search yet --}}
        <div class="cr-prompt">
            <div class="cr-prompt-icon">&#128202;</div>
            <h3>Select filters to load records</h3>
            <p>Choose a client, set a date range, and optionally filter by status, then click <strong>Search</strong>.</p>
        </div>

    @endif

</div>
@endsection
@push('scripts')
@endpush
