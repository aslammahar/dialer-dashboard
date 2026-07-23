@extends('layouts.admin')

@section('page-title')




    {{ __('Teams Management') }}
@endsection

@section('content')


<div class="row">





{{-- <a href="leaderboard">Leaderboard</a><br> --}}



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


{{-- <a href="teams-create">Create new team</a><br> --}}

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
                <div class="col-auto mb-/3 mb-sm-0">
                    <div class="d-flex align-items-center">
                        <div class="theme-avtar bg-success">
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


{{-- <a href="agent-reports">Agent Reports</a><br> --}}

<div class="col-lg-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mb-/3 mb-sm-0">
                    <div class="d-flex align-items-center">
                        <div class="theme-avtar bg-warning">
                            <i class="ti ti-cast"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">{{ __('Teams Managments ') }} </small>
                            <li><a href="{{ url('agent-reports') }}"><u>Agent Reports</u></a></li>
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

</div>

<div class="container">
    <h2>Teams List</h2>

    <style>
        .team-card {
            border: 1px solid #ccc; /* Add a border around each team card */
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>

    <div class="row">
        @foreach ($teams as $team)
        <div class="col-md-4 mb-4">
            <div class="team-card">
                <h4>{{ $team->name }}</h4>
                <p class="small">Leader: {{ $team->leader->name }}</p>
                <div class="actions">
                    <form method="POST" action="{{ route('update_team_name', ['id' => $team->id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="text" class="form-control form-control-sm" name="name" placeholder="New Team Name">
                        <button type="submit" class="btn btn-primary btn-sm"> Change Name</button> <br>
                    </form>

                    <form method="POST" action="{{ route('update_team_leader', ['id' => $team->id]) }}">
                        @csrf
                        @method('PUT')
<br>

                        <select class="form-control form-control-sm" name="leader_id">
                            @foreach ($leaders as $leader)
                                <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success btn-sm">Change Leader</button><br>
                    </form>

                    <form method="POST" action="{{ route('delete_team', ['id' => $team->id]) }}">
                        @csrf
                        @method('DELETE') <br>
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this team?')">Delete Team</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
