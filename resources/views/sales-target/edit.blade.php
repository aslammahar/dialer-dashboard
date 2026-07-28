@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Sales Target Settings')

@push('css-page')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --as-bg:#090d12; --as-surface:#11161d; --as-surface-alt:#171f29;
        --as-border:rgba(255,255,255,.07); --as-text:#f3f6f7; --as-text-sec:#93a2ac;
        --as-text-muted:#586872; --as-accent:#34f5c5; --as-accent-dim:#0f6e56;
        --as-font-display:'Space Grotesk',sans-serif; --as-font-body:'Inter',sans-serif;
    }
    .as-wrap{background:var(--as-bg);color:var(--as-text);font-family:var(--as-font-body);
        min-height:100vh;padding:36px 20px;display:flex;flex-direction:column;align-items:center;gap:20px}
    .as-wrap *{box-sizing:border-box}
    .as-card{width:100%;max-width:640px;background:var(--as-surface);border:1px solid var(--as-border);
        border-radius:18px;padding:32px}
    .as-title{font-family:var(--as-font-display);font-weight:700;font-size:19px;margin-bottom:2px}
    .as-sub{font-size:12.5px;color:var(--as-text-muted);margin-bottom:24px}
    .as-alert{border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:20px;
        background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--as-accent)}
    .as-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    @media(max-width:560px){.as-grid{grid-template-columns:1fr}}
    .as-field{display:flex;flex-direction:column;gap:6px;margin-bottom:18px}
    .as-field.full{grid-column:1 / -1}
    .as-label{font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--as-text-muted);font-weight:600}
    .as-input,.as-select{background:var(--as-surface-alt);border:1px solid var(--as-border);color:var(--as-text);
        border-radius:9px;padding:11px 13px;font-size:13.5px;font-family:var(--as-font-body);outline:none;width:100%}
    .as-input:focus,.as-select:focus{border-color:var(--as-accent-dim)}
    .as-submit{background:var(--as-accent);color:#06231b;border:none;border-radius:9px;
        padding:13px 20px;font-size:14px;font-weight:700;cursor:pointer}
    .as-cancel{color:var(--as-text-muted);font-size:13px;text-decoration:none;padding:13px 16px}
    .as-team-list{display:flex;flex-direction:column;gap:0}
    .as-team-row{display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--as-border);font-size:13px}
    .as-team-row:last-child{border-bottom:none}
</style>
@endpush

@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--as-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:0;align-self:flex-start;max-width:640px;width:100%">
    ← Back to Leaderboard
</a>

@if(session('status'))
    <div class="as-alert" style="max-width:640px;width:100%">✓ {{ session('status') }}</div>
@endif

<div class="as-wrap" style="padding-top:0">
    <div class="as-card">
        <div class="as-title">🎯 Monthly Sales Target</div>
        <div class="as-sub">Controls the "Hit X sales" widget on the dashboard</div>

        @if($canEdit)
        <form method="POST" action="{{ route('sales-target.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="month" value="{{ $monthStart }}">

            <div class="as-grid">
                <div class="as-field">
                    <label class="as-label">Raw Target (sales count)</label>
                    <input type="number" name="raw_target" class="as-input" value="{{ old('raw_target', $target->raw_target) }}" required>
                </div>
                <div class="as-field">
                    <label class="as-label">SPD Target (per day)</label>
                    <input type="number" step="0.1" name="spd_target" class="as-input" value="{{ old('spd_target', $target->spd_target) }}" required>
                </div>
                <div class="as-field">
                    <label class="as-label">Monthly SPD Target</label>
                    <input type="number" step="0.1" name="monthly_spd_target" class="as-input" value="{{ old('monthly_spd_target', $target->monthly_spd_target) }}" required>
                </div>
                <div class="as-field">
                    <label class="as-label">Reward Headline</label>
                    <input type="text" name="reward_headline" class="as-input" value="{{ old('reward_headline', $target->reward_headline) }}" placeholder="the whole team earns a trip" required>
                </div>

                <div class="as-field">
                    <label class="as-label">Milestone 1 Label (45%)</label>
                    <input type="text" name="milestone_1_label" class="as-input" value="{{ old('milestone_1_label', $target->milestone_1_label) }}" required>
                </div>
                <div class="as-field">
                    <label class="as-label">Milestone 2 Label (75%)</label>
                    <input type="text" name="milestone_2_label" class="as-input" value="{{ old('milestone_2_label', $target->milestone_2_label) }}" required>
                </div>
                <div class="as-field">
                    <label class="as-label">Milestone 2 Amount</label>
                    <input type="text" name="milestone_2_amount" class="as-input" value="{{ old('milestone_2_amount', $target->milestone_2_amount) }}" placeholder="e.g. 100k">
                </div>
                <div class="as-field">
                    <label class="as-label">Milestone 3 Label (100%)</label>
                    <input type="text" name="milestone_3_label" class="as-input" value="{{ old('milestone_3_label', $target->milestone_3_label) }}" required>
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="as-submit">Save Target</button>
                <a href="{{ route('dialer-dashboard') }}" class="as-cancel">Cancel</a>
            </div>
        </form>
        @else
        <div style="color:var(--as-text-muted);text-align:center;padding:20px">You don't have permission to edit this target.</div>
        @endif
    </div>

    <div class="as-card">
        <div class="as-title">👥 Team-Wise Target</div>
        <div class="as-sub">Set each team's own monthly sales target</div>

        @if($canEdit)
        <form method="POST" action="{{ route('sales-teams.set-target') }}" style="display:flex;gap:10px;align-items:end;margin-bottom:20px">
            @csrf
            <div class="as-field" style="margin-bottom:0;flex:1">
                <label class="as-label">Team</label>
                <select name="team_id" id="teamTargetSelect" class="as-select" required>
                    <option value="">-- Select Team --</option>
                    @foreach($teams as $t)
                        <option value="{{ $t->id }}" data-target="{{ $t->target }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="as-field" style="margin-bottom:0;width:140px">
                <label class="as-label">Target</label>
                <input type="number" name="target" id="teamTargetInput" class="as-input" min="0" placeholder="e.g. 40" required>
            </div>
            <button type="submit" class="as-submit" style="white-space:nowrap">Set Target</button>
            
        </form>
        @endif

        <div class="as-team-list">
            @forelse($teams as $t)
                {{-- <div class="as-team-row">
                    <span>{{ $t->name }}</span>
                    <span style="font-weight:600;color:var(--as-accent)">{{ $t->target }}</span>
                </div> --}}
            <div class="as-team-row">
    <span>{{ $t->name }}</span>
    <span style="display:flex;align-items:center;gap:10px">
        <span style="font-weight:600;color:var(--as-accent)">{{ $t->target }}</span>
        @if($canEdit)
        <form method="POST" action="{{ route('sales-teams.set-target') }}" style="margin:0">
            @csrf
            <input type="hidden" name="team_id" value="{{ $t->id }}">
            <input type="hidden" name="target" value="0">
            <button type="submit" style="background:none;border:none;color:var(--as-text-muted);font-size:12px;cursor:pointer" title="Reset target to 0, team stays">Clear Target</button>
        </form>
        <form method="POST" action="{{ route('sales-teams.destroy', $t) }}" onsubmit="return confirm('Remove {{ $t->name }}? Its closers will become unassigned.')" style="margin:0">
            @csrf @method('DELETE')
            <button type="submit" style="background:none;border:none;color:#ff5a5a;font-size:12px;cursor:pointer">Remove Team</button>
        </form>
        @endif
    </span>
</div>
            @empty
                <div style="color:var(--as-text-muted);font-size:13px;text-align:center;padding:10px">No teams created yet.</div>
            @endforelse
        </div>
    </div>
    <div class="as-card">
    <div class="as-title">🗂️ Client-Wise Target</div>
    <div class="as-sub">Set each client's own monthly sales target</div>

    @if($canEdit)
    <form method="POST" action="{{ route('sales-clients.set-target') }}" style="display:flex;gap:10px;align-items:end;margin-bottom:20px">
        @csrf
        <div class="as-field" style="margin-bottom:0;flex:1">
            <label class="as-label">Client</label>
            <select name="client_id" id="clientTargetSelect" class="as-select" required>
                <option value="">-- Select Client --</option>
                @foreach($clients as $cl)
                    <option value="{{ $cl->id }}" data-target="{{ $cl->target }}">{{ $cl->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="as-field" style="margin-bottom:0;width:140px">
            <label class="as-label">Target</label>
            <input type="number" name="target" id="clientTargetInput" class="as-input" min="0" required>
        </div>
        <button type="submit" class="as-submit" style="white-space:nowrap">Set Target</button>
    </form>
    @endif

   <div class="as-team-list">
    @forelse($clients as $cl)
        <div class="as-team-row">
            <span>{{ $cl->name }}</span>
            <span style="display:flex;align-items:center;gap:10px">
                <span style="font-weight:600;color:var(--as-accent)">{{ $cl->target }}</span>
                @if($canEdit)
                <form method="POST" action="{{ route('sales-clients.set-target') }}" style="margin:0">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $cl->id }}">
                    <input type="hidden" name="target" value="0">
                    <button type="submit" style="background:none;border:none;color:var(--as-text-muted);font-size:12px;cursor:pointer" title="Reset target to 0, client stays">Clear Target</button>
                </form>
                <form method="POST" action="{{ route('sales-clients.destroy', $cl) }}" onsubmit="return confirm('Remove {{ $cl->name }}?')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;color:#ff5a5a;font-size:12px;cursor:pointer">Remove Client</button>
                </form>
                @endif
            </span>
        </div>
    @empty
        <div style="color:var(--as-text-muted);font-size:13px;text-align:center;padding:10px">No clients created yet.</div>
    @endforelse
</div>
</div>

<div class="as-card">
    <div class="as-title">🚚 Carrier-Wise Target</div>
    <div class="as-sub">Set each carrier's own monthly sales target</div>

    @if($canEdit)
    <form method="POST" action="{{ route('sales-carriers.set-target') }}" style="display:flex;gap:10px;align-items:end;margin-bottom:20px">
        @csrf
        <div class="as-field" style="margin-bottom:0;flex:1">
            <label class="as-label">Carrier</label>
            <select name="carrier_id" id="carrierTargetSelect" class="as-select" required>
                <option value="">-- Select Carrier --</option>
                @foreach($carriers as $ca)
                    <option value="{{ $ca->id }}" data-target="{{ $ca->target }}">{{ $ca->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="as-field" style="margin-bottom:0;width:140px">
            <label class="as-label">Target</label>
            <input type="number" name="target" id="carrierTargetInput" class="as-input" min="0" required>
        </div>
        <button type="submit" class="as-submit" style="white-space:nowrap">Set Target</button>
    </form>
    @endif

  <div class="as-team-list">
    @forelse($carriers as $ca)
        <div class="as-team-row">
            <span>{{ $ca->name }}</span>
            <span style="display:flex;align-items:center;gap:10px">
                <span style="font-weight:600;color:var(--as-accent)">{{ $ca->target }}</span>
                @if($canEdit)
                <form method="POST" action="{{ route('sales-carriers.set-target') }}" style="margin:0">
                    @csrf
                    <input type="hidden" name="carrier_id" value="{{ $ca->id }}">
                    <input type="hidden" name="target" value="0">
                    <button type="submit" style="background:none;border:none;color:var(--as-text-muted);font-size:12px;cursor:pointer" title="Reset target to 0, carrier stays">Clear Target</button>
                </form>
                <form method="POST" action="{{ route('sales-carriers.destroy', $ca) }}" onsubmit="return confirm('Remove {{ $ca->name }}?')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;color:#ff5a5a;font-size:12px;cursor:pointer">Remove Carrier</button>
                </form>
                @endif
            </span>
        </div>
    @empty
        <div style="color:var(--as-text-muted);font-size:13px;text-align:center;padding:10px">No carriers created yet.</div>
    @endforelse
</div>
</div>
</div>

<script>
(function(){
    var select = document.getElementById('teamTargetSelect');
    var input = document.getElementById('teamTargetInput');
    if (!select || !input) return;

    select.addEventListener('change', function(){
        var opt = select.options[select.selectedIndex];
        var currentTarget = opt ? opt.getAttribute('data-target') : '';
        input.value = currentTarget || '';
    });
})();

['clientTargetSelect', 'carrierTargetSelect'].forEach(function(selectId){
    var select = document.getElementById(selectId);
    if (!select) return;
    var input = document.getElementById(selectId.replace('Select','Input'));
    select.addEventListener('change', function(){
        var opt = select.options[select.selectedIndex];
        input.value = opt ? (opt.getAttribute('data-target') || '') : '';
    });
});
</script>
@endsection