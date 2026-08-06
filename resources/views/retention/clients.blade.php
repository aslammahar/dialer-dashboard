@extends('layouts.dashboard-fullscreen')

@section('page-title', 'Retention Clients Report')

@push('css-page')
@include('dialer-dashboard._styles')
@endpush

@section('content')
<div class="dd-wrap">

    <div class="dd-topbar">
        <div class="dd-brand">
            <div class="dd-brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06231b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                </svg>
            </div>
            <div>
                <div class="dd-brand-title">Retention Clients & Carriers</div>
                <div class="dd-brand-sub">Client Performance & Carrier Matrix</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px">
            <a href="{{ route('retention.dashboard') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-arrow-left"></i> Retention Dashboard
            </a>
            <a href="{{ route('retention.report') }}" class="dd-readonly-badge" style="text-decoration:none">
                <i class="ti ti-report"></i> Retention Report
            </a>
        </div>
    </div>

    {{-- Top Panel: Client Overview --}}
    <div class="dd-panel" style="margin-bottom:16px">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Client Performance Overview</div>
                <div class="dd-panel-sub">Performance metrics per client</div>
            </div>
        </div>
        <div class="dd-table-wrap">
            <table class="dd-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Work Days</th>
                        <th>MTD</th>
                        <th>SPD</th>
                        <th>LEVEL</th>
                        <th>GI</th>
                        <th>Level %</th>
                        <th>AVG Pre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $c)
                        <tr>
                            <td style="font-weight:700;color:var(--dd-accent)">{{ $c['client'] }}</td>
                            <td>{{ $c['work_days'] }}</td>
                            <td style="font-weight:800;color:#34d399">{{ $c['mtd'] }}</td>
                            <td><span class="dd-badge">{{ $c['spd'] }}</span></td>
                            <td>{{ $c['level'] }}</td>
                            <td>{{ $c['gi'] }}</td>
                            <td>{{ $c['level_pct'] }}%</td>
                            <td>${{ number_format($c['avg_pre'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;color:var(--dd-text-muted)">No client data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bottom Panel: Carrier Matrix --}}
    <div class="dd-panel">
        <div class="dd-panel-head">
            <div>
                <div class="dd-panel-title">Carrier Breakdown Matrix</div>
                <div class="dd-panel-sub">Carriers breakdown across clients</div>
            </div>
        </div>
        <div class="dd-table-wrap">
            <table class="dd-table">
                <thead>
                    <tr>
                        <th>Carrier</th>
                        <th colspan="4" style="background:rgba(30,58,138,0.5);text-align:center">D6</th>
                        <th colspan="4" style="background:rgba(6,95,70,0.5);text-align:center">PM7</th>
                        <th colspan="4" style="background:rgba(112,26,117,0.5);text-align:center">FCF</th>
                        <th colspan="4" style="background:rgba(131,24,67,0.5);text-align:center">UL5</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Rw</th><th>Fx</th><th>Cr</th><th>NP</th>
                        <th>Rw</th><th>Fx</th><th>Cr</th><th>NP</th>
                        <th>Rw</th><th>Fx</th><th>Cr</th><th>NP</th>
                        <th>Rw</th><th>Fx</th><th>Cr</th><th>NP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carriers as $car)
                        <tr>
                            <td style="font-weight:700;color:#fff">{{ $car['carrier'] }}</td>
                            <td>{{ $car['D6_rewrite'] ?? 0 }}</td>
                            <td>{{ $car['D6_fixed'] ?? 0 }}</td>
                            <td>{{ $car['D6_correspondence'] ?? 0 }}</td>
                            <td>{{ $car['D6_new_policy'] ?? 0 }}</td>

                            <td>{{ $car['PM7_rewrite'] ?? 0 }}</td>
                            <td>{{ $car['PM7_fixed'] ?? 0 }}</td>
                            <td>{{ $car['PM7_correspondence'] ?? 0 }}</td>
                            <td>{{ $car['PM7_new_policy'] ?? 0 }}</td>

                            <td>{{ $car['FCF_rewrite'] ?? 0 }}</td>
                            <td>{{ $car['FCF_fixed'] ?? 0 }}</td>
                            <td>{{ $car['FCF_correspondence'] ?? 0 }}</td>
                            <td>{{ $car['FCF_new_policy'] ?? 0 }}</td>

                            <td>{{ $car['UL5_rewrite'] ?? 0 }}</td>
                            <td>{{ $car['UL5_fixed'] ?? 0 }}</td>
                            <td>{{ $car['UL5_correspondence'] ?? 0 }}</td>
                            <td>{{ $car['UL5_new_policy'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="17" style="text-align:center;color:var(--dd-text-muted)">No carrier matrix data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
