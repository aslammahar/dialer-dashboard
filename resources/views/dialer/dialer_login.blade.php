@extends('layouts.admin')
@section('content')

<div class="row">




    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-success">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{__('All Leads')}}</small>
                                <li><a href="{{route('all_avatar-leads')}}" class="btn btn-sm btn-success">All Leads</a></li>
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
                                <small class="text-muted">{{ __('Leaderboard Daily') }} </small>
                                <li><a href="{{route('avatar-leaderboard-daily')}}" class="btn btn-sm btn-warning">Leaderboard Daily</a></li>
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
                                <small class="text-muted">{{ __('Leaderboard Monthly') }} </small>
                                <li><a href="{{route('avatar-leaderboard-monthly')}}" class="btn btn-sm btn-danger">Leaderboard Monthly</a></li>
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

<iframe src="{{ $dialerUrl }}" width="100%" height="600px" frameborder="0"></iframe>
@endsection