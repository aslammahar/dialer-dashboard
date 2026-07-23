@extends('layouts.admin')

@section('page-title')
    {{ __('Teams Leads') }}
@endsection

@section('content')

{{-- Success toast --}}
@if(session('hc_success'))
<div id="hcToast" style="
    position: fixed; bottom: 1.5rem; right: 1.5rem;
    background: #166534; color: #bbf7d0;
    border: 1px solid #16a34a;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    z-index: 9999;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
">
    ✓ {{ session('hc_success') }}
</div>
@endif

<div class="row">
    {{-- Nav Cards --}}
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-primary"><i class="ti ti-cast"></i></div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Leaderboard')}}</small>
                                <li><a href="{{ route('leaderboard') }}">{{ __('Leaderboard') }}</a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-success"><i class="ti ti-cast"></i></div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Get Team Reports')}}</small>
                                <li><a href="{{ route('all.team.reports') }}">{{ __('Get Team Reports') }}</a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-info"><i class="ti ti-cast"></i></div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Create New Team')}}</small>
                                <li><a href="{{ url('teams-create') }}"><u>Create New Team</u></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-warning"><i class="ti ti-cast"></i></div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Assign Agents')}}</small>
                                <li><a href="{{ url('team-assignment') }}"><u>Assign Agents</u></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-danger"><i class="ti ti-cast"></i></div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Remove Agents') }}</small>
                                <li><a href="{{ url('teams-overview') }}"><u>Remove Agents</u></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-secondary"><i class="ti ti-cast"></i></div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Teams Management') }}</small>
                                <li><a href="{{ url('list-teams') }}"><u>Teams Management</u></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Active HC Table --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Active HC Management</h5>
                <small class="text-muted">Yahan Active HC update karo — leaderboard pe automatically reflect hoga</small>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Team Name</th>
                            <th>Leader</th>
                            <th class="text-center">Actual HC</th>
                            <th class="text-center" style="width: 180px;">Active HC <small class="text-warning">(editable)</small></th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teams as $i => $team)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $team->name }}</strong></td>
                            <td>{{ $team->leader->name ?? '—' }}</td>
                            <td class="text-center">{{ $team->agents->count() }}</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('team.hc-override', $team) }}" style="margin:0;">
                                    @csrf
                                    <div class="input-group input-group-sm justify-content-center" style="max-width: 140px; margin: 0 auto;">
                                        <input
                                            type="number"
                                            name="hc_override"
                                            value="{{ $team->hc_override ?? $team->agents->count() }}"
                                            min="1"
                                            max="500"
                                            class="form-control text-center"
                                            style="max-width: 80px;"
                                            onchange="this.form.submit()"
                                        >
                                        <button type="submit" class="btn btn-primary btn-sm">✓</button>
                                    </div>
                                </form>
                            </td>
                            <td class="text-center">
                                @if($team->hc_override)
                                    <span class="badge bg-warning text-dark">Manual ({{ $team->hc_override }})</span>
                                    <form method="POST" action="{{ route('team.hc-override', $team) }}" style="display:inline; margin-left: 5px;">
                                        @csrf
                                        <input type="hidden" name="hc_override" value="">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Auto pe wapas jao">Reset</button>
                                    </form>
                                @else
                                    <span class="badge bg-success">Auto</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
var toast = document.getElementById('hcToast');
if (toast) {
    setTimeout(function() {
        toast.style.transition = 'opacity 0.4s';
        toast.style.opacity = '0';
        setTimeout(function() { toast.remove(); }, 400);
    }, 3000);
}
</script>

@endsection