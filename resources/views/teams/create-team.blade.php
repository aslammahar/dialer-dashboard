@extends('layouts.admin')

    

@section('page-title')

    {{ __('Teams Leads') }}
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

    


</div>





 <h2> create  team  </h2>

 @if (session('success'))
     <div class="alert alert-success">
        {{session('success')}}
     </div>

     @endif

     <form method="POST" action="{{ route('teams.store') }}">
        @csrf
    
        <!-- Team Name -->
        <div class="form-group">
            <label for="name">Team Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
    
        <!-- Team Leader -->
        <div class="form-group">
            <label for="leader">Team Leader</label>
            <select name="leader" id="leader" class="form-control" required>
                @foreach ($teamLeaders as $leader)
                    <option value="{{ $leader->id }}">
                        {{ $leader->name }} 
                    </option>
                @endforeach
            </select>
        </div>
        
        
        
    
        <!-- Team Agents -->
        <div class="form-group">
            <label for="agents">Team Agents</label>
            <select name="agents[]" id="agents" class="form-control" >
                @foreach ($usersWithTeams as $user)
                    @php
                        $hasTeam = $user->teams->isNotEmpty();
                        $bgColorClass = $hasTeam ? '' : 'bg-warning'; // Add bg-warning class if no team
                    @endphp
                    <option value="{{ $user->id }}" class="{{ $bgColorClass }}">
                        {{ $user->name }} ({{ $user->type }}) - Team: {{ $hasTeam ? $user->teams->first()->name : 'No Team' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        
    
        <button type="submit" class="btn btn-primary">Create Team</button>
    </form>
    

@endsection

