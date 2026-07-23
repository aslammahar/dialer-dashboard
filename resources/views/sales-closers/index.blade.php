@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Manage Closers')

@push('css-page')

<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --sc-bg:#090d12; --sc-surface:#11161d; --sc-border:rgba(255,255,255,.07);
        --sc-text:#f3f6f7; --sc-text-muted:#586872; --sc-accent:#34f5c5;
        --sc-font-display:'Space Grotesk',sans-serif; --sc-font-body:'Inter',sans-serif;
    }
    .sc-wrap{background:var(--sc-bg);color:var(--sc-text);font-family:var(--sc-font-body);padding:28px;border-radius:18px;min-height:100vh}
    .sc-title{font-family:var(--sc-font-display);font-weight:700;font-size:19px;margin-bottom:18px}
    .sc-alert{background:rgba(52,245,197,.1);border:1px solid rgba(52,245,197,.3);color:var(--sc-accent);
        border-radius:9px;padding:10px 14px;font-size:13px;margin-bottom:16px}
    .sc-table{width:100%;border-collapse:collapse;background:var(--sc-surface);border:1px solid var(--sc-border);border-radius:12px;overflow:hidden}
    .sc-table th{font-size:11px;text-transform:uppercase;color:var(--sc-text-muted);text-align:left;padding:12px 14px;border-bottom:1px solid var(--sc-border)}
    .sc-table td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--sc-border)}
    .sc-select{background:#171f29;border:1px solid var(--sc-border);color:var(--sc-text);border-radius:7px;padding:7px 10px;font-size:13px}
    .sc-save{background:var(--sc-accent);color:#06231b;border:none;border-radius:7px;padding:7px 14px;font-size:12.5px;font-weight:600;cursor:pointer}
</style>
@endpush

@section('content')
<a href="{{ route('dialer-dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--as-text-muted);
    font-size:12.5px;text-decoration:none;margin-bottom:16px">
    ← Back to Leaderboard
</a>
<div class="sc-wrap">
    <div class="sc-title">👤 Manage Closers — Team Assignment</div>

    @if(session('status'))
        <div class="sc-alert">✓ {{ session('status') }}</div>
    @endif

    <table class="sc-table">
        <thead>
            <tr><th>Closer</th><th>Current Team</th><th>Reassign</th></tr>
        </thead>
        <tbody>
            @foreach($closers as $closer)
                <tr>
                    <td>{{ $closer->name }}</td>
                    <td>{{ $closer->team->name ?? '— None —' }}</td>
                  <td>
    
                
        <form method="POST" action="{{ route('sales-closers.update', $closer) }}" style="display:flex;gap:8px;align-items:center">
            @csrf
            @method('PUT')
            <select name="sales_team_id" class="sc-select">
                <option value="">— None —</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ $closer->sales_team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="sc-save">Save</button>
        </form>
        <form method="POST" action="{{ route('sales-closers.destroy', $closer) }}" onsubmit="return confirm('Remove {{ $closer->name }}? This cannot be undone.')" style="margin-top:6px">
            @csrf
            @method('DELETE')
            <button type="submit" style="background:none;border:none;color:#ff5a5a;font-size:12px;cursor:pointer;padding:0">Remove Closer</button>
        </form>
   
        {{ $closer->team->name ?? '— None —' }}
   
</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection