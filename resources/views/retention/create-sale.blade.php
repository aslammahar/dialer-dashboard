@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Add Retention Sale Entry')

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
        min-height:100vh;padding:36px 20px;display:flex;justify-content:center;flex-direction:column;align-items:center}
    .as-wrap *{box-sizing:border-box}
    .as-card{width:100%;max-width:640px;background:var(--as-surface);border:1px solid var(--as-border);
        border-radius:18px;padding:32px;opacity:0;transform:translateY(10px);animation:as-rise .5s ease forwards;margin-bottom:24px}
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

    .as-actions{display:flex;gap:12px;margin-top:8px;align-items:center}
    .as-submit{flex:1;background:var(--as-accent);color:#06231b;border:none;border-radius:9px;
        padding:13px 20px;font-size:14px;font-weight:700;cursor:pointer;font-family:var(--as-font-body);
        transition:filter .15s ease}
    .as-submit:hover{filter:brightness(1.08)}
    .as-cancel{color:var(--as-text-muted);font-size:13px;text-decoration:none;padding:13px 16px}
    .as-cancel:hover{color:var(--as-text-sec)}

    .as-entry-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px}
    .as-remove-btn{background:none;border:none;color:var(--as-danger);font-size:11.5px;cursor:pointer;padding:4px 8px;
        border:1px solid rgba(255,90,90,.3);border-radius:6px;white-space:nowrap}
    .as-remove-btn:hover{background:rgba(255,90,90,.1)}
</style>
@endpush

@section('content')
<div style="background:#090d12;padding:15px 20px 0 20px">
    <a href="{{ route('retention.dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:#93a2ac;
        font-size:13px;text-decoration:none">
        ← Back to Retention Dashboard
    </a>
</div>

<div class="as-wrap">
    <div class="as-card">
        <div class="as-head">
            <div class="as-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
            </div>
            <div class="as-title">+ Add Retention Sale Entry</div>
        </div>
        <div class="as-sub">Log a closer's retention sale for the daily board</div>

        @if(session('success'))
            <div id="saleSuccess" class="as-alert as-alert-success" style="opacity:0;transform:translateY(-20px);animation:fadeSlide 1.5s forwards;">
                ✓ {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const el = document.getElementById('saleSuccess');
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

        @if($errors->any())
            <div class="as-alert as-alert-error">
                <div>
                    Please fix the following errors:
                    <ul style="margin:4px 0 0 16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('retention.sales.store') }}">
            @csrf

            <div class="as-grid">
                <div class="as-field full">
                    <label class="as-label">Entry Date <span class="as-required">*</span></label>
                    <input type="date" name="entry_date" value="{{ old('entry_date', now('America/New_York')->toDateString()) }}" class="as-input" required>
                </div>

                <div class="as-field">
                    <label class="as-label">Closer <span class="as-required">*</span></label>
                    <select name="sales_closer_id" id="closerSelect" class="as-select" required>
                        <option value="">-- Select Closer --</option>
                        @foreach($closers as $closer)
                            <option value="{{ $closer->id }}"
                                data-team-name="{{ $closer->team->name ?? 'Retention' }}"
                                {{ old('sales_closer_id') == $closer->id ? 'selected' : '' }}>
                                {{ $closer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="as-field">
                    <label class="as-label">Team</label>
                    <input type="text" id="teamDisplay" class="as-input" value="" placeholder="Auto-filled after selecting closer" readonly style="cursor:not-allowed;opacity:.75">
                </div>

                <div class="as-field">
                    <label class="as-label">Retention Type <span class="as-required">*</span></label>
                    <select name="retention_type" class="as-select" required>
                        <option value="rewrite" {{ old('retention_type') == 'rewrite' ? 'selected' : '' }}>Rewrite</option>
                        <option value="fixed" {{ old('retention_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="correspondence" {{ old('retention_type') == 'correspondence' ? 'selected' : '' }}>Correspondence</option>
                        <option value="new_policy" {{ old('retention_type') == 'new_policy' ? 'selected' : '' }}>New Policy</option>
                    </select>
                </div>

                <div class="as-field">
                    <label class="as-label">Sale Type</label>
                    <select name="sale_type" class="as-select">
                        <option value="">-- None / N/A --</option>
                        <option value="level" {{ old('sale_type') == 'level' ? 'selected' : '' }}>Level</option>
                        <option value="gi" {{ old('sale_type') == 'gi' ? 'selected' : '' }}>GI</option>
                    </select>
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

                <div class="as-field full">
                    <label class="as-label">Avg Premium ($)</label>
                    <input type="number" step="0.01" name="avg_pre" class="as-input" value="{{ old('avg_pre') }}" placeholder="e.g. 50.00">
                </div>

                <div class="as-field full">
                    <label class="as-label">Notes</label>
                    <input type="text" name="notes" class="as-input" value="{{ old('notes') }}" maxlength="255" placeholder="Optional notes...">
                </div>
            </div>

            <div class="as-actions">
                <button type="submit" class="as-submit">Save Retention Entry</button>
                <a href="{{ route('retention.dashboard') }}" class="as-cancel">Cancel</a>
            </div>
        </form>
    </div>

    @if($recentEntries->count())
    <div class="as-card">
        <div class="as-title" style="margin-bottom:2px">📋 Recent Retention Entries</div>
        <div class="as-sub" style="margin:2px 0 20px 0">Log history and quick management</div>

        @foreach($recentEntries as $entry)
            <div style="border-bottom:1px solid var(--as-border);padding-bottom:14px;margin-bottom:14px">
                <div class="as-entry-head">
                    <div style="font-size:13px;color:var(--as-text-sec)">
                        <strong style="color:var(--as-accent)">{{ $entry->closer->name ?? '—' }}</strong>
                        · <span style="text-transform:uppercase;color:#fff">{{ $entry->retention_type }}</span>
                        · {{ $entry->client->name ?? 'No client' }}
                        · {{ $entry->carrier->name ?? 'No carrier' }}
                        · {{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}
                    </div>
                    <form method="POST" action="{{ route('retention.sales.destroy', $entry) }}" onsubmit="return confirm('Remove this retention sale entry?')" style="margin:0">
                        @csrf @method('DELETE')
                        <button type="submit" class="as-remove-btn">🗑 Remove</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>

<script>
(function(){
    var closerSelect = document.getElementById('closerSelect');
    var teamDisplay = document.getElementById('teamDisplay');

    function syncTeam(){
        var selected = closerSelect.options[closerSelect.selectedIndex];
        var teamName = selected ? selected.getAttribute('data-team-name') : '';
        teamDisplay.value = teamName || 'Retention';
    }

    closerSelect.addEventListener('change', syncTeam);
    syncTeam();
})();
</script>
@endsection
