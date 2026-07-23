@extends('layouts.admin')

    

@section('page-title')


 <br>

    {{ __('Teams managements') }}
@endsection

@section('content')


<div class="row">


    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Leaderboard')}}</small>
                                <li><a href="{{ route('leaderboard') }}">{{ __('Leaderboard') }}</a></li> 
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
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
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Create New Team')}}</small>
                                <li><a href="{{ url('teams-create') }}"><u>Create New Team</u></a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
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
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('Assign Agents')}}</small>
                                <li><a href="{{ url('team-assignment') }}"><u>Assign Agents</u></a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Remove Agents') }} </small>
                                <li><a href="{{ url('teams-overview') }}"><u>Remove Agents</u></a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-secondary">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Teams Managments ') }} </small>
                                <li><a href="{{ url('list-teams') }}"><u>Teams Management</u></a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>

    </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-/3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-success">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Teams Managments ') }} </small>
                                    <li><a href="{{ url('agent-reports') }}"><u>Agent Report</u></a></li>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h3 class="m-0"> </h3>
                        </div>
                    </div>
                </div>
            </div>

</div>

</div>


@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif


<style>
    /* Increase the height of the select box */
    .custom-select {
        height: 200px; /* Adjust the height as needed */
    }

    /* Increase the width of the select box */
    /* You can set a specific width in pixels or percentage */
    .custom-select {
        width: 100%; /* Use 100% for full width */
    }
</style>




<div class="container">
    <h3>Assign Agents To Teams</h3>
    
    {{-- Form for assigning agents to teams --}}
    <form action="{{ route('team.assignment') }}" method="POST">
        @csrf
        



        <div class="form-group">
            <label for="agents">Select Team Agents</label>
            <select name="agents[]" id="agents" class="form-control custom-select" multiple>
                @foreach ($agents as $agent)
                    @php
                        $hasTeam = $agent->teams->isNotEmpty();
                        $bgColorClass = $hasTeam ? '' : 'bg-warning'; // Add bg-warning class if no team
                    @endphp
                    <option value="{{ $agent->id }}" class="{{ $bgColorClass }}">
                        {{ $agent->name }} ({{ $agent->type }}) - Team: {{ $hasTeam ? $agent->teams->first()->name : 'No Team' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        





        
        <div class="form-group">
            <label for="team">Assign to Team:</label>
            <select name="team" id="team" class="form-control">
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Assign to Team</button>
    </form>
    

    </div>
    <br>

    
    
@endsection
