{{-- resources/views/verifier/dashboard.blade.php --}}
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

    .vd-wrap {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--paper);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
        font-size: 14px;
        color: var(--ink);
    }

    /* Header */
    .vd-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: .875rem;
        border-bottom: 2px solid var(--border);
    }
    .vd-header h1  { font-size: 1.5rem; font-weight: 600; margin: 0; letter-spacing: -.3px; }
    .vd-header .sub { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 500; }
    .vd-header .user-badge {
        margin-left: auto;
        font-size: .8125rem;
        font-weight: 600;
        color: var(--green);
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        border-radius: 20px;
        padding: .35rem .875rem;
    }

    /* Alert */
    .vd-alert {
        border-radius: 6px;
        padding: .75rem 1rem;
        font-size: .8125rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: .625rem;
        font-weight: 500;
    }
    .vd-alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .vd-alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    /* Filters */
    .vd-filters {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 1.25rem;
    }
    .vd-filters input,
    .vd-filters select {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .375rem .625rem;
        background: var(--white);
        color: var(--ink);
        outline: none;
    }
    .vd-filters input:focus,
    .vd-filters select:focus { border-color: var(--blue); box-shadow: 0 0 0 2px rgba(59,130,246,.1); }
    .vd-filters button {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8rem;
        font-weight: 600;
        padding: .375rem .875rem;
        background: var(--ink);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .vd-filters button:hover { opacity: .85; }
    .total-badge {
        font-size: .8rem;
        font-weight: 600;
        color: var(--blue);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        padding: 3px 12px;
    }

    /* Table */
    .vd-table-wrap {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    table.vd-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .8125rem;
    }
    .vd-table thead tr { background: #f8fafc; border-bottom: 2px solid var(--border); }
    .vd-table th {
        padding: .75rem .875rem;
        text-align: left;
        font-size: .75rem;
        letter-spacing: .5px;
        text-transform: uppercase;
        font-weight: 600;
        white-space: nowrap;
        color: var(--muted);
    }
    .vd-table td {
        padding: .6rem .875rem;
        border-bottom: 1px solid var(--border);
        color: var(--ink);
        vertical-align: middle;
    }
    .vd-table tbody tr:last-child td { border-bottom: none; }
    .vd-table tbody tr:hover { background: #fafbfc; }

    /* Status pills */
    .status-pill {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: .7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .pill-funded   { background: #dcfce7; color: #166534; }
    .pill-lapse    { background: #fef9c3; color: #854d0e; }
    .pill-cb       { background: #fee2e2; color: #991b1b; }
    .pill-pending  { background: #e0e7ff; color: #3730a3; }
    .pill-approved { background: #d1fae5; color: #065f46; }
    .pill-rejected { background: #fce7f3; color: #9d174d; }
    .pill-default  { background: #f1f5f9; color: var(--muted); }

    /* Details card */
    .detail-card {
        background: #fafbfc;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .875rem 1rem;
        font-size: .8rem;
        display: none;
    }
    .detail-card.open { display: block; }
    .detail-section-title {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--blue);
        border-bottom: 1px solid var(--border);
        padding-bottom: .3rem;
        margin-bottom: .5rem;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: .5rem .875rem;
        margin-bottom: .25rem;
    }
    .detail-field label {
        display: block;
        font-size: .7rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: .15rem;
    }
    .detail-field span { font-size: .8125rem; color: var(--ink); }
    .detail-sensitive { background: #fff7ed; border-radius: 5px; padding: .25rem .4rem; }
    .detail-sensitive label { color: #92400e; }
    .sensitive-val { font-family: monospace; font-size: .8125rem; font-weight: 600; color: #92400e; }

    /* Remarks form */
    .remarks-form {
        margin-top: .875rem;
        border-top: 1px solid var(--border);
        padding-top: .875rem;
    }
    .remarks-form label {
        display: block;
        font-size: .75rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: .4rem;
    }
    .remarks-form textarea {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .8125rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .5rem .75rem;
        width: 100%;
        min-height: 80px;
        resize: vertical;
        outline: none;
        color: var(--ink);
        background: var(--white);
    }
    .remarks-form textarea:focus { border-color: var(--blue); box-shadow: 0 0 0 2px rgba(59,130,246,.1); }
    .btn-save-remarks {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin-top: .5rem;
        font-size: .8125rem;
        font-weight: 600;
        padding: .45rem 1.125rem;
        background: var(--green);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-save-remarks:hover { opacity: .85; }

    /* Toggle btn */
    .btn-view {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: .775rem;
        font-weight: 600;
        padding: .3rem .75rem;
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-view:hover { opacity: .85; }

    /* Remarks preview */
    .remarks-preview {
        font-size: .8rem;
        color: var(--muted);
        font-style: italic;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .remarks-preview.has-remarks { color: var(--ink); font-style: normal; }

    /* Assigned by chip */
    .assigned-by { font-size: .72rem; color: var(--muted); }

    /* Empty */
    .vd-empty { text-align: center; padding: 3rem 1rem; color: var(--muted); }
    .vd-empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
    .vd-empty p { font-size: .875rem; margin: 0; font-weight: 500; }

    /* Pagination */
    .vd-pagination { margin-top: 1rem; display: flex; justify-content: flex-end; }
    .vd-pagination .pagination { margin: 0; }
</style>

@section('content')
<div class="vd-wrap">

    {{-- Header --}}
    <div class="vd-header">
        <div>
            <h1>My Assigned Calls</h1>
            <span class="sub">Verifier Dashboard — review &amp; add remarks</span>
        </div>
        <span class="user-badge">&#10003; {{ auth()->user()->name }}</span>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="vd-alert vd-alert-success">&#10003; {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="vd-alert vd-alert-error">&#10005; {{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('verifier.dashboard') }}" class="vd-filters">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name, policy, phone…">
        <select name="status">
            <option value="">All Statuses</option>
            @foreach($statuses as $st)
                <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
            @endforeach
        </select>
        <select name="per_page">
            @foreach([20, 50, 100] as $pp)
                <option value="{{ $pp }}" @selected($perPage == $pp)>{{ $pp }} / page</option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
        <span class="total-badge">{{ $calls->total() }} assigned</span>
    </form>

    {{-- Table --}}
    <div class="vd-table-wrap">
        @if($calls->count())
        <table class="vd-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Policy ID</th>
                    <th>Carrier</th>
                    <th>Premium</th>
                    <th>Remarks</th>
                    <th>Assigned By</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calls as $call)
                <tr>
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
                    <td style="font-size:.8rem;">${{ number_format((float)($call->monthly_premium ?? 0), 2) }}</td>
                    <td>
                        <span class="remarks-preview {{ $call->remarks ? 'has-remarks' : '' }}" title="{{ $call->remarks }}">
                            {{ $call->remarks ?: 'No remarks yet' }}
                        </span>
                    </td>
                    <td>
                        <span class="assigned-by">
                            {{ $call->verifierAssignment?->assigner?->name ?? '—' }}<br>
                            <span style="font-size:.7rem;">{{ $call->verifierAssignment?->assigned_at?->format('d M Y') }}</span>
                        </span>
                        @if($call->verifierAssignment?->reason)
                        <span style="display:block; font-size:.72rem; color:#92400e; background:#fff7ed; border:1px solid #fde68a; border-radius:4px; padding:2px 6px; margin-top:.25rem; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                              title="{{ $call->verifierAssignment->reason }}">
                            &#128196; {{ $call->verifierAssignment->reason }}
                        </span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn-view" onclick="toggleDetail({{ $call->id }})">
                            View / Edit
                        </button>
                    </td>
                </tr>

                {{-- Expandable detail + remarks row --}}
                <tr id="detail-row-{{ $call->id }}" style="display:none;">
                    <td colspan="12" style="padding:.5rem 1rem 1rem; background:#f8fafc;">
                        <div class="detail-card open">

                            {{-- ── Personal Info ── --}}
                            <div class="detail-section-title">Personal Information</div>
                            <div class="detail-grid">
                                <div class="detail-field">
                                    <label>Customer Name</label>
                                    <span>{{ $call->customer_full_name ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Phone</label>
                                    <span>{{ $call->phone_number ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Alt Phone</label>
                                    <span>{{ $call->alternate_phone_number ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Email</label>
                                    <span>{{ $call->cx_email ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Address</label>
                                    <span>{{ $call->address ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>City / State / ZIP</label>
                                    <span>{{ implode(', ', array_filter([$call->city, $call->state, $call->zip_code])) ?: '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Gender</label>
                                    <span>{{ $call->gender ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Marital Status</label>
                                    <span>{{ $call->martial_status ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>DOB</label>
                                    <span>{{ $call->dob ? $call->dob->format('d M Y') : '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Age</label>
                                    <span>{{ $call->age ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Place of Birth</label>
                                    <span>{{ $call->palce_of_birth ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>&#128274; Social Security #</label>
                                    <span class="sensitive-val">{{ $call->social_security ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Height / Weight</label>
                                    <span>{{ $call->height ?? '—' }} / {{ $call->weight ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Smoker</label>
                                    <span>{{ $call->smoker ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Health Condition</label>
                                    <span>{{ $call->health_condition ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Medication</label>
                                    <span>{{ $call->medication ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Hospital</label>
                                    <span>{{ $call->hospital_name ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Physician</label>
                                    <span>{{ $call->physician_name ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- ── Policy Info ── --}}
                            <div class="detail-section-title" style="margin-top:.875rem;">Policy &amp; Coverage</div>
                            <div class="detail-grid">
                                <div class="detail-field">
                                    <label>Policy ID</label>
                                    <span style="font-family:monospace; font-weight:700;">{{ $call->policy_id ?: '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Status</label>
                                    <span>{{ $call->status ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Carrier</label>
                                    <span>{{ $call->carrier ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Coverage Plan</label>
                                    <span>{{ $call->coverage_plan ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Monthly Premium</label>
                                    <span>${{ number_format((float)($call->monthly_premium ?? 0), 2) }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Customer Eligibility</label>
                                    <span>{{ $call->customer_eligibility ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Initial Draft Date</label>
                                    <span>{{ $call->initial_draft_date ? $call->initial_draft_date->format('d M Y') : '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Future Draft Date</label>
                                    <span>{{ $call->future_draft_date ? $call->future_draft_date->format('d M Y') : '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Payor</label>
                                    <span>{{ $call->payor ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Underwriter</label>
                                    <span>{{ $call->underwriter_name ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Signature Type</label>
                                    <span>{{ $call->signature_type ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- ── Beneficiary ── --}}
                            <div class="detail-section-title" style="margin-top:.875rem;">Beneficiary</div>
                            <div class="detail-grid">
                                <div class="detail-field">
                                    <label>Beneficiary Name</label>
                                    <span>{{ $call->beneficiary ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Relation</label>
                                    <span>{{ $call->beneficiary_relation ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Beneficiary Phone</label>
                                    <span>{{ $call->beneficiary_phone ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Beneficiary DOB</label>
                                    <span>{{ $call->beneficiary_dob ? $call->beneficiary_dob->format('d M Y') : '—' }}</span>
                                </div>
                            </div>

                            {{-- ── Bank / Payment ── --}}
                            <div class="detail-section-title" style="margin-top:.875rem;">&#127974; Bank &amp; Payment Details</div>
                            <div class="detail-grid">
                                <div class="detail-field detail-sensitive">
                                    <label>Bank Name</label>
                                    <span class="sensitive-val">{{ $call->bank_name ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>Bank Address</label>
                                    <span class="sensitive-val">{{ $call->bank_address ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>&#128274; Routing Number</label>
                                    <span class="sensitive-val">{{ $call->routing_number ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>&#128274; Account Number</label>
                                    <span class="sensitive-val">{{ $call->bank_account_number ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Account Type</label>
                                    <span>{{ $call->account_type ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>&#128274; Debit / Express Card #</label>
                                    <span class="sensitive-val">{{ $call->debit_card_direct_express_no ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>Card Expiration</label>
                                    <span class="sensitive-val">{{ $call->debit_card_direct_express_expiration ?? '—' }}</span>
                                </div>
                                <div class="detail-field detail-sensitive">
                                    <label>&#128274; Card CVV</label>
                                    <span class="sensitive-val">{{ $call->debit_card_direct_express_cvv ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- ── Assignment Info ── --}}
                            @if($call->verifierAssignment?->reason)
                            <div class="detail-section-title" style="margin-top:.875rem;">Assignment Instructions</div>
                            <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:6px; padding:.625rem .875rem; font-size:.8125rem; color:#92400e;">
                                {{ $call->verifierAssignment->reason }}
                            </div>
                            @endif

                            {{-- ── Sale Info ── --}}
                            <div class="detail-section-title" style="margin-top:.875rem;">Sale Info</div>
                            <div class="detail-grid">
                                <div class="detail-field">
                                    <label>Closer</label>
                                    <span>{{ $call->closername ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Center</label>
                                    <span>{{ $call->center_name ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Team</label>
                                    <span>{{ $call->teamname ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Agent</label>
                                    <span>{{ $call->agentname ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Dialer</label>
                                    <span>{{ $call->dialername ?? '—' }}</span>
                                </div>
                                <div class="detail-field">
                                    <label>Assigned By</label>
                                    <span>{{ $call->verifierAssignment?->assigner?->name ?? '—' }}
                                        @if($call->verifierAssignment?->assigned_at)
                                            <span style="color:var(--muted); font-size:.72rem;">on {{ $call->verifierAssignment->assigned_at->format('d M Y H:i') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Remarks — editable --}}
                            <div class="remarks-form">
                                <form method="POST" action="{{ route('verifier.remarks', $call->id) }}">
                                    @csrf
                                    <label>Remarks (editable)</label>
                                    <textarea name="remarks" placeholder="Enter your remarks…">{{ old('remarks', $call->remarks) }}</textarea>
                                    <button type="submit" class="btn-save-remarks">&#10003; Save Remarks</button>
                                </form>
                            </div>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="vd-empty">
            <div class="vd-empty-icon">&#128203;</div>
            <p>No calls assigned to you yet{{ ($search || $status) ? ' for the current filters' : '' }}.</p>
        </div>
        @endif
    </div>

    {{-- Pagination --}}
    @if($calls->hasPages())
    <div class="vd-pagination">
        {{ $calls->appends(['search' => $search, 'status' => $status, 'per_page' => $perPage])->links('pagination::bootstrap-5') }}
    </div>
    @endif

</div>

<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-row-' + id);
    if (!row) return;
    const isOpen = row.style.display !== 'none';
    row.style.display = isOpen ? 'none' : 'table-row';
}
</script>
@endsection
@push('scripts')
@endpush
