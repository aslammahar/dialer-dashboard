@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Add Sale')

@push('css-page')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --as-bg:#090d12;
        --as-surface:#11161d;
        --as-surface-alt:#171f29;
        --as-border:rgba(255,255,255,.07);
        --as-border-strong:rgba(255,255,255,.14);
        --as-text:#f3f6f7;
        --as-text-sec:#93a2ac;
        --as-text-muted:#586872;
        --as-accent:#34f5c5;
        --as-accent-dim:#0f6e56;
        --as-danger:#ff5a5a;
        --as-font-display:'Space Grotesk',sans-serif;
        --as-font-body:'Inter',sans-serif;
    }
    .as-wrap{background:var(--as-bg);color:var(--as-text);font-family:var(--as-font-body);
        min-height:100vh;padding:36px 20px;display:flex;justify-content:center}
    .as-wrap *{box-sizing:border-box}
    .as-card{width:100%;max-width:640px;background:var(--as-surface);border:1px solid var(--as-border);
        border-radius:18px;padding:32px;opacity:0;transform:translateY(10px);animation:as-rise .5s ease forwards}
    @keyframes as-rise{to{opacity:1;transform:translateY(0)}}

    .as-head{display:flex;align-items:center;gap:12px;margin-bottom:6px}
    .as-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(160deg,var(--as-accent),var(--as-accent-dim));
        display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .as-icon svg{width:18px;height:18px}
    .as-title{font-family:var(--as-font-display);font-weight:700;font-size:19px}
    .as-sub{font-size:12.5px;color:var(--as-text-muted);margin:2px 0 24px 48px}

    .as-alert{border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:20px;display:flex;align-items:flex-start;gap:8px}
    .as-alert-success{background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--as-accent)}
    .as-alert-error{background:rgba(255,90,90,.1);border:1px solid rgba(255,90,90,.3);color:var(--as-danger)}
    .as-alert ul{margin:4px 0 0 18px;padding:0}

    .as-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    @media(max-width:560px){.as-grid{grid-template-columns:1fr}}
    .as-field{display:flex;flex-direction:column;gap:6px;margin-bottom:18px}
    .as-field.full{grid-column:1 / -1}
    .as-label{font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--as-text-muted);font-weight:600}
    .as-required{color:var(--as-accent)}

    .as-input,.as-select,.as-textarea{
        background:var(--as-surface-alt);border:1px solid var(--as-border);color:var(--as-text);
        border-radius:9px;padding:11px 13px;font-size:13.5px;font-family:var(--as-font-body);
        outline:none;width:100%;transition:border-color .15s ease}
    .as-input:focus,.as-select:focus,.as-textarea:focus{border-color:var(--as-accent-dim)}
    .as-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2393a2ac'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 14px center;padding-right:32px}

    .as-status-toggle{display:flex;gap:10px}
    .as-status-option{flex:1;position:relative}
    .as-status-option input{position:absolute;opacity:0;inset:0;cursor:pointer;margin:0}
    .as-status-option label{display:block;text-align:center;padding:11px;border-radius:9px;font-size:13px;font-weight:600;
        background:var(--as-surface-alt);border:1px solid var(--as-border);color:var(--as-text-sec);cursor:pointer;
        transition:all .15s ease}
    .as-status-option input:checked + label{background:rgba(52,245,197,.12);border-color:var(--as-accent);color:var(--as-accent)}
    .as-status-option input:focus-visible + label{outline:2px solid var(--as-accent-dim)}

    #saleTypeField{transition:opacity .2s ease,max-height .2s ease}

    .as-actions{display:flex;gap:12px;margin-top:8px;align-items:center}
    .as-submit{flex:1;background:var(--as-accent);color:#06231b;border:none;border-radius:9px;
        padding:13px 20px;font-size:14px;font-weight:700;cursor:pointer;font-family:var(--as-font-body);
        transition:filter .15s ease}
    .as-submit:hover{filter:brightness(1.08)}
    .as-cancel{color:var(--as-text-muted);font-size:13px;text-decoration:none;padding:13px 16px}
    .as-cancel:hover{color:var(--as-text-sec)}
    .as-entry-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px}
    .as-remove-form{margin:0;flex-shrink:0}
    .as-remove-btn{background:none;border:none;color:var(--as-danger);font-size:11.5px;cursor:pointer;padding:4px 8px;
        border:1px solid rgba(255,90,90,.3);border-radius:6px;white-space:nowrap}
    .as-remove-btn:hover{background:rgba(255,90,90,.1)}
</style>
@endpush


@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:#93a2ac;
    font-size:12.5px;text-decoration:none;margin:20px 0 0 20px">
    ← Back to Leaderboard
</a>

<div class="as-wrap">
    <div class="as-card">
        <div class="as-head">
            <div class="as-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
            </div>
            <div class="as-title">Add Sale Entry</div>
        </div>
        <div class="as-sub">Log a closer's sale for the daily board</div>

        @if(session('status'))
            <div class="as-alert as-alert-success">✓ {{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="as-alert as-alert-error">
                <div>
                    Please fix the following:
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('daily-sales.store') }}">
            @csrf

            <div class="as-grid">
                <input type="hidden" name="entry_date" value="{{ now('America/New_York')->toDateString() }}">

                <div class="as-field">
                    <label class="as-label">Closer <span class="as-required">*</span></label>
                    <select name="sales_closer_id" id="closerSelect" class="as-select" required>
                        <option value="">-- Select Closer --</option>
                        @foreach($closers as $closer)
                            <option value="{{ $closer->id }}"
                                data-team-id="{{ $closer->team->id ?? '' }}"
                                data-team-name="{{ $closer->team->name ?? '' }}"
                                {{ old('sales_closer_id') == $closer->id ? 'selected' : '' }}>
                                {{ $closer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="as-field">
                    <label class="as-label">Team</label>
                    <input type="text" id="teamDisplay" class="as-input" value="" placeholder="Auto-filled after selecting closer" readonly style="cursor:not-allowed;opacity:.75">
                    <input type="hidden" name="sales_team_id" id="teamHiddenInput" value="{{ old('sales_team_id') }}">
                </div>

                <div class="as-field">
                    <label class="as-label">Client</label>
                    <select name="sales_client_id" class="as-select">
                        <option value="">-- Select Client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('sales_client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="as-field">
                    <label class="as-label">Carrier</label>
                    <select name="sales_carrier_id" class="as-select">
                        <option value="">-- Select Carrier --</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" {{ old('sales_carrier_id') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="as-field">
                    <label class="as-label">Avg Pre</label>
                    <input type="number" step="0.01" name="avg_pre" class="as-input" value="{{ old('avg_pre') }}" placeholder="e.g. 82.00">
                </div>

                <div class="as-field">
                    <label class="as-label">Leads ID</label>
                    <input type="text" name="leads_id" class="as-input" value="{{ old('leads_id') }}" placeholder="e.g. LD-10234" required>
                </div>

                <div class="as-field full">
                    <label class="as-label">Status <span class="as-required">*</span></label>
                    <div class="as-status-toggle">
                        <div class="as-status-option">
                            <input type="radio" name="status" id="statusApproved" value="approved" {{ old('status', 'approved') == 'approved' ? 'checked' : '' }}>
                            <label for="statusApproved">✓ Approved</label>
                        </div>
                        <div class="as-status-option">
                            <input type="radio" name="status" id="statusPending" value="pending" {{ old('status') == 'pending' ? 'checked' : '' }}>
                            <label for="statusPending">⏳ Pending</label>
                        </div>
                    </div>
                </div>

                <div class="as-field full" id="saleTypeField">
                    <label class="as-label">Sale Type (only if Approved)</label>
                    <select name="sale_type" class="as-select">
                        <option value="">-- N/A --</option>
                        <option value="level" {{ old('sale_type') == 'level' ? 'selected' : '' }}>Level</option>
                        <option value="gi" {{ old('sale_type') == 'gi' ? 'selected' : '' }}>GI</option>
                    </select>
                </div>

                <div class="as-field full">
                    <label class="as-label">Notes</label>
                    <input type="text" name="notes" class="as-input" value="{{ old('notes') }}" maxlength="255" placeholder="Optional notes...">
                </div>
            </div>

            <div class="as-actions">
                <button type="submit" class="as-submit">Save Entry</button>
                <a href="{{ route('dialer-dashboard') }}" class="as-cancel">Cancel</a>
            </div>
        </form>
    </div>

    @if($pendingEntries->count())
    <div class="as-card">
        <div class="as-title" style="margin-bottom:2px">⏳ Pending Sales</div>
        <div class="as-sub" style="margin:2px 0 20px 0">Update details and approve when ready</div>

        @foreach($pendingEntries as $entry)
            @php
                $entryEditDeadline = \Carbon\Carbon::parse($entry->entry_date)->addDay()->endOfDay();
                $isLocked = now('America/New_York')->greaterThan($entryEditDeadline);
            @endphp

            @if($isLocked)
                <div style="border-bottom:1px solid var(--as-border);padding-bottom:18px;margin-bottom:18px;opacity:.55">
                    <div class="as-entry-head">
                        <div style="font-size:13px;color:var(--as-text-sec)">
                            <strong style="color:var(--as-text)">{{ $entry->closer->name ?? '—' }}</strong>
                            — {{ $entry->client->name ?? 'No client' }}
                            · {{ $entry->carrier->name ?? 'No carrier' }}
                            · {{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}
                        </div>
                        <form method="POST" action="{{ route('daily-sales.destroy', $entry) }}" onsubmit="return confirm('Remove this sale entry? This cannot be undone.')" class="as-remove-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="as-remove-btn">🗑 Remove</button>
                        </form>
                    </div>
                    <div style="font-size:12px;color:var(--as-danger)">
                        🔒 1-day edit window has passed — this entry is locked.
                    </div>
                </div>
            @else
                <div style="border-bottom:1px solid var(--as-border);padding-bottom:18px;margin-bottom:18px">
                    <div class="as-entry-head">
                        <div style="font-size:13px;color:var(--as-text-sec)">
                            <strong style="color:var(--as-text)">{{ $entry->closer->name ?? '—' }}</strong>
                            — {{ $entry->client->name ?? 'No client' }}
                            · {{ $entry->carrier->name ?? 'No carrier' }}
                            · {{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}
                        </div>
                        <form method="POST" action="{{ route('daily-sales.destroy', $entry) }}" onsubmit="return confirm('Remove this sale entry? This cannot be undone.')" class="as-remove-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="as-remove-btn">🗑 Remove</button>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('daily-sales.update', $entry) }}">
                        @csrf
                        @method('PATCH')

                        <div class="as-grid">
                            <div class="as-field">
                                <label class="as-label">Sale Date</label>
                                <input type="date" name="entry_date" class="as-input" value="{{ \Carbon\Carbon::parse($entry->entry_date)->toDateString() }}" max="{{ now()->toDateString() }}">
                            </div>
                            <div class="as-field">
                                <label class="as-label">Leads ID</label>
                                <input type="text" name="leads_id" class="as-input" value="{{ $entry->leads_id }}">
                            </div>
                            <div class="as-field">
                                <label class="as-label">Avg Pre</label>
                                <input type="number" step="0.01" name="avg_pre" class="as-input" value="{{ $entry->avg_pre }}">
                            </div>
                            <div class="as-field">
                                <label class="as-label">Sale Type</label>
                                <select name="sale_type" class="as-select">
                                    <option value="">-- N/A --</option>
                                    <option value="level" {{ $entry->sale_type == 'level' ? 'selected' : '' }}>Level</option>
                                    <option value="gi" {{ $entry->sale_type == 'gi' ? 'selected' : '' }}>GI</option>
                                </select>
                            </div>
                            <div class="as-field">
                                <label class="as-label">Status</label>
                                <select name="status" class="as-select">
                                    <option value="pending" selected>⏳ Pending</option>
                                    <option value="approved">✓ Approve</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="as-submit" style="width:auto;padding:9px 18px;font-size:13px">Save</button>
                    </form>
                </div>
            @endif
        @endforeach
    </div>
    @endif
</div>

<script>
(function(){
    var pendingRadio = document.getElementById('statusPending');
    var approvedRadio = document.getElementById('statusApproved');
    var saleTypeField = document.getElementById('saleTypeField');

    function toggleSaleType(){
        var isPending = pendingRadio.checked;
        saleTypeField.style.opacity = isPending ? '0.4' : '1';
        saleTypeField.querySelector('select').disabled = isPending;
    }

    pendingRadio.addEventListener('change', toggleSaleType);
    approvedRadio.addEventListener('change', toggleSaleType);
    toggleSaleType();

    var closerSelect = document.getElementById('closerSelect');
    var teamDisplay = document.getElementById('teamDisplay');
    var teamHiddenInput = document.getElementById('teamHiddenInput');

    function syncTeam(){
        var selected = closerSelect.options[closerSelect.selectedIndex];
        var teamId = selected ? selected.getAttribute('data-team-id') : '';
        var teamName = selected ? selected.getAttribute('data-team-name') : '';

        teamHiddenInput.value = teamId || '';
        teamDisplay.value = teamName || 'No team assigned';
    }

    closerSelect.addEventListener('change', syncTeam);
    syncTeam();
})();
</script>
@endsection