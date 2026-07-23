@extends('layouts.admin')

@section('page-title')

 

    {{ __('Teams Management') }}
@endsection

<style>
    /* Custom CSS to reduce the search bar size */
    #search-bar {
        height: 30px;
        font-size: 14px;
        width: 200px;
    }
</style>

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




<div class="container">
    <h3>Remove Agents from Teams</h3>

    <div class="mb-3">
        <input type="text" id="search-bar" class="form-control form-control-sm" placeholder="Search Agent">
    </div>

    <form action="{{ route('team.removeAgents') }}" method="POST">
        @csrf
        <table id="team-agents" class="table">
            <thead>
                <tr>
                    <th>Agent Name</th>
                    <th>Team</th>
                    <th>Remove Agent</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agentsInTeams as $agent)
                    <tr>
                        <td>{{ $agent->name }}</td>
                        <td>{{ $agent->teams->first()->name }}</td>
                        <td>
                            <label>
                                <input type="checkbox" name="agentsToRemove[]" value="{{ $agent->id }}">
                                Remove
                            </label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-danger">Remove Selected Agents</button>
    </form>
</div>

<script>
    // JavaScript to implement search functionality
    const searchBar = document.getElementById('search-bar');
    const tableRows = document.querySelectorAll('#team-agents tbody tr');

    searchBar.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        tableRows.forEach(row => {
            const agentName = row.querySelector('td:first-child').textContent.toLowerCase();
            if (agentName.includes(searchValue)) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

@endsection
