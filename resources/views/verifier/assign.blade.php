{{-- resources/views/verifier/assign.blade.php --}}
@extends('layouts.admin')

<style>
    :root {
        --ink:    #1a1a2e;
        --paper:  #f5f6fa;
        --muted:  #8492a6;
        --border: #e2e8f0;
        --blue:   #3b82f6;
        --green:  #16a34a;
        --red:    #dc2626;
        --white:  #ffffff;
    }
    * { box-sizing: border-box; }

    .va-wrap {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--paper);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
        font-size: 14px;
        color: var(--ink);
    }

    .va-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: .875rem;
        border-bottom: 2px solid var(--border);
    }
    .va-header h1  { font-size: 1.5rem; font-weight: 600; margin: 0; letter-spacing: -.3px; }
    .va-header .sub { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 500; }

    .va-alert {
        border-radius: 6px; padding: .75rem 1rem; font-size: .8125rem;
        margin-bottom: 1.25rem; display: flex; align-items: flex-start;
        gap: .625rem; font-weight: 500;
    }
    .va-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .va-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

    /* ── Assign panel ── */
    .va-assign-panel {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.125rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .va-assign-panel h2 {
        font-size: .875rem;
        font-weight: 700;
        margin: 0 0 .875rem;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .va-assign-row {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
        align-items: flex-end;
        margin-bottom: .75rem;
    }
    .va-field label {
        display: block;
        font-size: .72rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: .3rem;
    }
    .va-field select,
    .va-field textarea {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .45rem .75rem;
        background: var(--white);
        color: var(--ink);
        outline: none;
    }
    .va-field select { min-width: 220px; }
    .va-field textarea {
        min-width: 320px;
        min-height: 60px;
        resize: vertical;
        width: 100%;
    }
    .va-field select:focus,
    .va-field textarea:focus { border-color: var(--blue); box-shadow: 0 0 0 2px rgba(59,130,246,.1); }
    .va-assign-footer {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }
    .va-selected-count {
        font-size: .8rem; font-weight: 600; color: var(--blue);
        background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: 20px; padding: 3px 12px; white-space: nowrap;
    }
    .btn-assign {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem; font-weight: 600;
        padding: .475rem 1.25rem;
        background: var(--green); color: #fff;
        border: none; border-radius: 6px;
        cursor: pointer; transition: opacity .15s; white-space: nowrap;
    }
    .btn-assign:hover { opacity: .85; }
    .btn-assign:disabled { opacity: .35; cursor: not-allowed; }

    /* ── Filters ── */
    .va-filters {
        display: flex; gap: .5rem; flex-wrap: wrap;
        align-items: center; margin-bottom: 1rem;
    }
    .va-filters input,
    .va-filters select {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem; border: 1px solid var(--border);
        border-radius: 6px; padding: .375rem .625rem;
        background: var(--white); color: var(--ink); outline: none;
    }
    .va-filters input:focus,
    .va-filters select:focus { border-color: var(--blue); box-shadow: 0 0 0 2px rgba(59,130,246,.1); }
    .va-filters button {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem; font-weight: 600;
        padding: .375rem .875rem; background: var(--ink);
        color: #fff; border: none; border-radius: 6px; cursor: pointer;
    }
    .va-filters button:hover { opacity: .85; }
    .va-filters a.clear-link { font-size: .8rem; color: var(--muted); text-decoration: none; padding: .375rem .5rem; }
    .va-filters a.clear-link:hover { color: var(--ink); }

    /* ── Select-all bar ── */
    .va-bulk-bar {
        display: flex; align-items: center; gap: .75rem;
        margin-bottom: .625rem; font-size: .8125rem; color: var(--muted);
    }
    .va-bulk-bar input[type="checkbox"] { width: 15px; height: 15px; cursor: pointer; accent-color: var(--blue); }

    /* ── Table ── */
    .va-table-wrap {
        background: var(--white); border: 1px solid var(--border);
        border-radius: 8px; overflow: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    table.va-table { width: 100%; border-collapse: collapse; font-size: .8125rem; }
    .va-table thead tr { background: #f8fafc; border-bottom: 2px solid var(--border); }
    .va-table th {
        padding: .75rem .875rem; text-align: left;
        font-size: .75rem; letter-spacing: .5px; text-transform: uppercase;
        font-weight: 600; white-space: nowrap; color: var(--muted);
    }
    .va-table td {
        padding: .6rem .875rem; border-bottom: 1px solid var(--border);
        color: var(--ink); vertical-align: middle;
    }
    .va-table tbody tr:last-child td { border-bottom: none; }
    .va-table tbody tr:hover { background: #fafbfc; }
    .va-table tbody tr.row-selected { background: #eff6ff; }
    .va-table td.cb-col,
    .va-table th.cb-col { width: 40px; text-align: center; }
    .va-table td.cb-col input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--blue); cursor: pointer; }

    .status-pill {
        display: inline-block; padding: 3px 9px; border-radius: 20px;
        font-size: .7rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: .3px; white-space: nowrap;
    }
    .pill-funded   { background:#dcfce7; color:#166534; }
    .pill-lapse    { background:#fef9c3; color:#854d0e; }
    .pill-cb       { background:#fee2e2; color:#991b1b; }
    .pill-pending  { background:#e0e7ff; color:#3730a3; }
    .pill-approved { background:#d1fae5; color:#065f46; }
    .pill-rejected { background:#fce7f3; color:#9d174d; }
    .pill-default  { background:#f1f5f9; color:var(--muted); }

    .assigned-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .75rem; font-weight: 600; color: #166534;
        background: #dcfce7; border: 1px solid #bbf7d0;
        border-radius: 20px; padding: 2px 9px; white-space: nowrap;
    }
    .assigned-reason {
        font-size: .72rem; color: var(--muted); font-style: italic;
        margin-top: .2rem; display: block; max-width: 200px;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .unassigned-badge { font-size: .75rem; color: var(--muted); font-style: italic; }

    .btn-unassign {
        display: inline-flex; align-items: center; gap: .25rem;
        background: none; border: 1px solid #fecaca; color: var(--red);
        border-radius: 5px; padding: 3px 10px; font-size: .75rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        cursor: pointer; font-weight: 600; transition: background .1s;
        margin-top: .3rem;
    }
    .btn-unassign:hover { background: #fee2e2; }

    .va-empty { text-align: center; padding: 3rem 1rem; color: var(--muted); }
    .va-empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
    .va-empty p { font-size: .875rem; margin: 0; font-weight: 500; }

    .va-pagination { margin-top: 1rem; display: flex; justify-content: flex-end; }
    .va-pagination .pagination { margin: 0; }

    /* ── Per-call reason modal ── */
    .modal-backdrop {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-backdrop.open { display: flex; }
    .modal-box {
        background: var(--white);
        border-radius: 10px;
        box-shadow: 0 20px 60px rgba(0,0,0,.25);
        width: 660px;
        max-width: 95vw;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .modal-head {
        padding: 1.125rem 1.375rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
        flex-shrink: 0;
    }
    .modal-head h3 { font-size: 1rem; font-weight: 700; margin: 0; color: var(--ink); }
    .modal-head .modal-sub { font-size: .775rem; color: var(--muted); margin-top: .15rem; }
    .modal-close {
        background: none; border: none; font-size: 1.25rem;
        color: var(--muted); cursor: pointer; line-height: 1;
        padding: .25rem .5rem; border-radius: 4px;
    }
    .modal-close:hover { background: #f1f5f9; color: var(--ink); }
    .modal-body {
        padding: 1rem 1.375rem;
        overflow-y: auto;
        flex: 1;
    }
    .modal-verifier-row {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: .625rem .875rem;
        font-size: .8125rem;
        font-weight: 600;
        color: #1d4ed8;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .call-reason-item {
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: .75rem .875rem;
        margin-bottom: .625rem;
        background: var(--white);
        transition: border-color .15s;
    }
    .call-reason-item:focus-within { border-color: var(--blue); }
    .call-reason-header {
        display: flex;
        align-items: center;
        gap: .625rem;
        margin-bottom: .5rem;
    }
    .call-num {
        font-size: .72rem;
        font-weight: 700;
        color: var(--muted);
        background: #f1f5f9;
        border-radius: 4px;
        padding: 2px 7px;
        white-space: nowrap;
    }
    .call-name {
        font-size: .8375rem;
        font-weight: 700;
        color: var(--ink);
    }
    .call-meta {
        font-size: .75rem;
        color: var(--muted);
        margin-left: auto;
        white-space: nowrap;
    }
    .call-reason-item textarea {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem;
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: .45rem .65rem;
        width: 100%;
        min-height: 62px;
        resize: vertical;
        outline: none;
        color: var(--ink);
        background: #fafbfc;
    }
    .call-reason-item textarea:focus {
        border-color: var(--blue);
        background: var(--white);
        box-shadow: 0 0 0 2px rgba(59,130,246,.08);
    }
    .modal-foot {
        padding: .875rem 1.375rem;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .625rem;
        background: #fafbfc;
        flex-shrink: 0;
    }
    .btn-modal-cancel {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem; font-weight: 600;
        padding: .45rem 1rem;
        background: transparent; color: var(--muted);
        border: 1px solid var(--border); border-radius: 6px;
        cursor: pointer; transition: all .15s;
    }
    .btn-modal-cancel:hover { border-color: var(--muted); color: var(--ink); }
    .btn-modal-confirm {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem; font-weight: 700;
        padding: .45rem 1.375rem;
        background: var(--green); color: #fff;
        border: none; border-radius: 6px;
        cursor: pointer; transition: opacity .15s;
    }
    .btn-modal-confirm:hover { opacity: .87; }
</style>

@section('content')
<div class="va-wrap">

    {{-- Header --}}
    <div class="va-header">
        <div>
            <h1>Assign Calls to Verifier</h1>
            <span class="sub">Select records &amp; assign to a verifier user</span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="va-alert va-alert-success">&#10003; {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="va-alert va-alert-error">&#10005; {{ session('error') }}</div>
    @endif

    {{-- ── ASSIGN PANEL ── --}}
    <div class="va-assign-panel">
        <h2>&#128203; Assign Selected Calls</h2>

        {{-- The real form — submitted programmatically from the modal --}}
        <form method="POST" action="{{ route('verifier.store') }}" id="assign-form">
            @csrf
            <div id="hidden-ids-container"></div>
            <div id="hidden-reasons-container"></div>
            <input type="hidden" name="verifier_id" id="form-verifier-id">
        </form>

        <div class="va-assign-row">
            <div class="va-field">
                <label>Verifier</label>
                <select id="panel-verifier-select">
                    <option value="">-- Select Verifier --</option>
                    @foreach($verifiers as $v)
                        <option value="{{ $v->id }}" data-name="{{ $v->name }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="va-assign-footer">
            <span class="va-selected-count" id="selected-count">0 selected</span>
            <button type="button" class="btn-assign" id="btn-open-modal" disabled>
                &#9998; Add Reasons &amp; Assign
            </button>
        </div>
    </div>

    {{-- ── PER-CALL REASON MODAL ── --}}
    <div class="modal-backdrop" id="reason-modal">
        <div class="modal-box">
            <div class="modal-head">
                <div>
                    <h3>&#128196; Add Reason Per Call</h3>
                    <div class="modal-sub">Each call can have its own instruction for the verifier</div>
                </div>
                <button type="button" class="modal-close" id="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-verifier-row" id="modal-verifier-label">
                    &#128100; Verifier: —
                </div>
                <div id="modal-calls-list">
                    {{-- Populated by JS --}}
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-modal-cancel" id="modal-cancel-btn">Cancel</button>
                <button type="button" class="btn-modal-confirm" id="modal-confirm-btn">
                    &#10003; Confirm &amp; Assign
                </button>
            </div>
        </div>
    </div>

    {{-- ── FILTER FORM ── --}}
    <form method="GET" action="{{ route('verifier.assign') }}" class="va-filters">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name, policy, phone…">
        <select name="status">
            <option value="">All Statuses</option>
            @foreach($statuses as $st)
                <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
            @endforeach
        </select>
        <select name="verifier_filter">
            <option value="all"        @selected(!$verifier || $verifier === 'all')>All Records</option>
            <option value="unassigned" @selected($verifier === 'unassigned')>Unassigned Only</option>
            @foreach($verifiers as $v)
                <option value="{{ $v->id }}" @selected($verifier == $v->id)>Assigned to: {{ $v->name }}</option>
            @endforeach
        </select>
        <select name="per_page">
            @foreach([20, 50, 100] as $pp)
                <option value="{{ $pp }}" @selected($perPage == $pp)>{{ $pp }} / page</option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
        @if($search || $status || ($verifier && $verifier !== 'all'))
        <a href="{{ route('verifier.assign') }}" class="clear-link">Clear</a>
        @endif
    </form>

    {{-- Select-all bar --}}
    <div class="va-bulk-bar">
        <input type="checkbox" id="select-all" title="Select / deselect all on this page">
        <label for="select-all" style="cursor:pointer; font-size:.8rem;">Select all on this page</label>
        <span style="font-size:.8rem;">— {{ $calls->total() }} total record(s)</span>
    </div>

    {{-- ── TABLE (NO form wrapper — each unassign is its own form) ── --}}
    <div class="va-table-wrap">
        @if($calls->count())
        <table class="va-table">
            <thead>
                <tr>
                    <th class="cb-col"><input type="checkbox" id="select-all-th"></th>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Policy ID</th>
                    <th>Carrier</th>
                    <th>Closer</th>
                    <th>Assigned To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calls as $call)
                <tr id="row-{{ $call->id }}"
                    data-name="{{ $call->customer_full_name ?? 'Unknown' }}"
                    data-status="{{ $call->status ?? '' }}"
                    data-policy="{{ $call->policy_id ?? '' }}">
                    <td class="cb-col">
                        <input type="checkbox" class="row-cb" value="{{ $call->id }}">
                    </td>
                    <td style="color:var(--muted); font-size:.75rem;">{{ $call->id }}</td>
                    <td style="font-size:.75rem; white-space:nowrap;">
                        {{ $call->created_at->format('d M Y') }}<br>
                        <span style="color:var(--muted);">{{ $call->created_at->format('H:i') }}</span>
                    </td>
                    <td style="font-weight:600; font-size:.8125rem;">{{ $call->customer_full_name ?? '—' }}</td>
                    <td style="font-size:.8rem;">{{ $call->phone_number ?? '—' }}</td>
                    <td style="font-size:.8rem;">{{ $call->state ?? '—' }}</td>
                    <td>
                        @php
                            $s   = strtolower($call->status ?? '');
                            $cls = str_contains($s,'fund') ? 'pill-funded'
                                 : (str_contains($s,'lapse') ? 'pill-lapse'
                                 : ((str_contains($s,'charge')||str_contains($s,'claw')) ? 'pill-cb'
                                 : (str_contains($s,'reject') ? 'pill-rejected'
                                 : (str_contains($s,'approv') ? 'pill-approved'
                                 : ($s === 'pending' ? 'pill-pending' : 'pill-default')))));
                        @endphp
                        <span class="status-pill {{ $cls }}">{{ $call->status ?? '—' }}</span>
                    </td>
                    <td style="font-size:.8rem; font-family:monospace;">{{ $call->policy_id ?: '—' }}</td>
                    <td style="font-size:.8rem;">{{ $call->carrier ?? '—' }}</td>
                    <td style="font-size:.8rem;">{{ $call->closername ?? '—' }}</td>
                    <td>
                        @if($call->verifierAssignment)
                            <span class="assigned-badge">&#10003; {{ $call->verifierAssignment->verifier?->name ?? 'Unknown' }}</span>
                            @if($call->verifierAssignment->reason)
                                <span class="assigned-reason" title="{{ $call->verifierAssignment->reason }}">
                                    {{ $call->verifierAssignment->reason }}
                                </span>
                            @endif
                            {{-- Standalone unassign form – NOT nested inside any other form --}}
                            <form method="POST"
                                  action="{{ route('verifier.unassign', $call->id) }}"
                                  onsubmit="return confirm('Remove this assignment?')"
                                  style="display:inline-block; margin-top:.3rem;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-unassign">&#10005; Unassign</button>
                            </form>
                        @else
                            <span class="unassigned-badge">Unassigned</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="va-empty">
            <div class="va-empty-icon">&#128202;</div>
            <p>No records found for the selected filters.</p>
        </div>
        @endif
    </div>

    {{-- Pagination --}}
    @if($calls->hasPages())
    <div class="va-pagination">
        {{ $calls->appends(['search' => $search, 'status' => $status, 'verifier_filter' => $verifier, 'per_page' => $perPage])->links('pagination::bootstrap-5') }}
    </div>
    @endif

</div>

<script>
(function () {
    // ── Elements ──
    const assignForm      = document.getElementById('assign-form');
    const hiddenIdsCont   = document.getElementById('hidden-ids-container');
    const hiddenReasons   = document.getElementById('hidden-reasons-container');
    const formVerifierId  = document.getElementById('form-verifier-id');
    const panelSelect     = document.getElementById('panel-verifier-select');
    const selectAll       = document.getElementById('select-all');
    const selectAllTh     = document.getElementById('select-all-th');
    const countEl         = document.getElementById('selected-count');
    const btnOpenModal    = document.getElementById('btn-open-modal');
    const modal           = document.getElementById('reason-modal');
    const modalCallsList  = document.getElementById('modal-calls-list');
    const modalVerLabel   = document.getElementById('modal-verifier-label');
    const modalCloseBtn   = document.getElementById('modal-close-btn');
    const modalCancelBtn  = document.getElementById('modal-cancel-btn');
    const modalConfirmBtn = document.getElementById('modal-confirm-btn');

    // ── Checkbox helpers ──
    const allCbs    = () => document.querySelectorAll('.row-cb');
    const checkedCbs= () => document.querySelectorAll('.row-cb:checked');

    function updateUI() {
        const checked = checkedCbs();
        const n = checked.length;
        countEl.textContent = n + ' selected';

        const verifierChosen = panelSelect.value !== '';
        btnOpenModal.disabled = (n === 0 || !verifierChosen);

        allCbs().forEach(cb => {
            const row = document.getElementById('row-' + cb.value);
            if (row) row.classList.toggle('row-selected', cb.checked);
        });

        const all = allCbs().length > 0 && n === allCbs().length;
        selectAll.checked   = all;
        selectAllTh.checked = all;
    }

    function toggleAll(state) {
        allCbs().forEach(cb => { cb.checked = state; });
        updateUI();
    }

    selectAll.addEventListener('change',   () => toggleAll(selectAll.checked));
    selectAllTh.addEventListener('change', () => toggleAll(selectAllTh.checked));
    document.addEventListener('change', e => {
        if (e.target.classList.contains('row-cb')) updateUI();
    });
    panelSelect.addEventListener('change', updateUI);

    // ── Open modal ──
    btnOpenModal.addEventListener('click', function () {
        const checked = checkedCbs();
        if (checked.length === 0) { alert('Select at least one call.'); return; }
        if (!panelSelect.value)   { alert('Select a verifier first.'); return; }

        const verifierName = panelSelect.options[panelSelect.selectedIndex].dataset.name;
        modalVerLabel.innerHTML = '&#128100; Verifier: <strong>' + verifierName + '</strong>';

        // Build per-call textarea list
        modalCallsList.innerHTML = '';
        let idx = 1;
        checked.forEach(cb => {
            const row    = document.getElementById('row-' + cb.value);
            const name   = row ? (row.dataset.name   || 'Call #' + cb.value) : 'Call #' + cb.value;
            const status = row ? (row.dataset.status || '') : '';
            const policy = row ? (row.dataset.policy || '') : '';

            const item = document.createElement('div');
            item.className = 'call-reason-item';
            item.innerHTML =
                '<div class="call-reason-header">' +
                    '<span class="call-num">#' + idx + '</span>' +
                    '<span class="call-name">' + escHtml(name) + '</span>' +
                    '<span class="call-meta">' +
                        (policy ? 'Policy: ' + escHtml(policy) + '&nbsp;&nbsp;' : '') +
                        (status ? '<span style="font-size:.72rem; padding:2px 7px; border-radius:10px; background:#f1f5f9;">' + escHtml(status) + '</span>' : '') +
                    '</span>' +
                '</div>' +
                '<textarea name="modal_reasons[' + cb.value + ']" ' +
                          'placeholder="Reason / instructions for this call (optional)…" ' +
                          'rows="2"></textarea>';
            modalCallsList.appendChild(item);
            idx++;
        });

        modal.classList.add('open');
        // Focus first textarea
        const first = modalCallsList.querySelector('textarea');
        if (first) setTimeout(() => first.focus(), 80);
    });

    // ── Close modal ──
    function closeModal() { modal.classList.remove('open'); }
    modalCloseBtn.addEventListener('click',  closeModal);
    modalCancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    // ── Confirm & submit ──
    modalConfirmBtn.addEventListener('click', function () {
        const checked = checkedCbs();
        if (checked.length === 0) return;

        // Inject verifier_id
        formVerifierId.value = panelSelect.value;

        // Inject closed_call_ids[]
        hiddenIdsCont.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'closed_call_ids[]';
            inp.value = cb.value;
            hiddenIdsCont.appendChild(inp);
        });

        // Inject reasons[call_id] from modal textareas
        hiddenReasons.innerHTML = '';
        modalCallsList.querySelectorAll('textarea').forEach(ta => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = ta.name.replace('modal_', ''); // reasons[id]
            inp.value = ta.value;
            hiddenReasons.appendChild(inp);
        });

        assignForm.submit();
    });

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
@endsection
@push('scripts')
@endpush
