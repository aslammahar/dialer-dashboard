@extends('layouts.admin')


@section('page-title')
{{ __('Leaderboard') }}
@endsection

@section('content')

<!-- <a href="{{route('leads.create')}}">Create Lead</a><br>
<a href="{{route('my-avatar-checked-leads')}}">My Checked Leads</a><br>
<a href="{{route('avatar-leaderboard-daily')}}">Leaderboard Daily</a><br> -->

<!-- links goes here  -->
<div class="row">
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
                                    <small class="text-muted">{{__('Create Lead')}}</small>
                                    <li><a href="{{route('leads.create')}}" class="btn btn-sm btn-info">Create Lead</a></li>
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
                                <div class="theme-avtar bg-primary">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{__('Leaderboard')}}</small>
                                    <li><a href="{{ route('leaderboard') }}" class="btn btn-sm btn-primary">{{ __('Leaderboard') }}</a></li>
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
                                <div class="theme-avtar bg-warning">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Avatar Section') }} </small>
                                    <li><a href="avatar-section" class="btn btn-sm btn-warning">Avatar Section</a></li>
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
                                    <small class="text-muted">{{ __('Leaderboard Daily') }} </small>
                                    <li><a href="{{route('avatar-leaderboard-daily')}}" class="btn btn-sm btn-danger">Leaderboard Daily</a></li>
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
    <!-- links ends here  -->

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $sectionTitle }}</h2>

    </div>

    <div class="table-responsive">
        <table class="table table-bordered leaderboard-table">
            <thead>
                <tr class="table-header">
                    <th class="header__item">User</th>
                    <th class="header__item">Monthly</th>
                </tr>
            </thead>
            <tbody>
                @php
                // Sort the leaderboard array in descending order based on leads_count
                $sortedLeaderboard = $leaderboard->sortByDesc('leads_count');
                $totalLeadCount = $leaderboard->sum('leads_count');
                @endphp

                @foreach ($sortedLeaderboard as $user)
                <tr class="table-row">
                    <td class="table-data">{{ $user->name }}</td>
                    <td class="table-data">{{ $user->leads_count }}</td>
                </tr>
                @endforeach
                <tr class="table-header" style="background-color: #5e6b62; color: white;">
                    <th class="header__item">Total</th>
                    <th class="header__item">{{ $totalLeadCount }}</th>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection