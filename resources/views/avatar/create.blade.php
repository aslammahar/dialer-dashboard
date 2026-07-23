<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Lead</title>
</head>

<body>
    @extends('layouts.admin')


    @section('page-title')

    {{ __('Create lead') }}
    @endsection

    @section('content')

    <!-- <a href="{{route('my-avatar-checked-leads')}}">My Checked Leads</a><br>
    <a href="{{route('avatar-leaderboard-daily')}}">Leaderboard daily</a><br>
    <a href="{{route('avatar-leaderboard-monthly')}}">Leaderboard Monthly</a><br> -->



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
                                    <small class="text-muted">{{__('Leaderboard Monthly')}}</small>
                                    <li><a href="{{route('avatar-leaderboard-monthly')}}" class="btn btn-sm btn-info">Leaderboard Monthly</a></li>
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
            {{-- <h2>{{ $sectionTitle }}</h2> --}}
            {{-- <a href="{{ route('') }}" class="h6 d-inline-block font-weight-400 mb-0">Monthly Leads</a> --}}
        </div>



    </div>


    <div class="container">
        {{--<iframe src="https://json.dialerhosting.com/" frameborder="0" width="700" height="700"  style="border:0">
          
         
        </iframe>--}}

        <form method="POST" action="{{ route('manual-leads.store') }}">
            @csrf
            <input type="hidden" name="isManual" value="1">

            <div class="col-6 form-group">
                <label for="agent_id" class="form-label">{{ __('Select Agent User') }}</label>
                <select name="agent_id" class="form-control" required>
                    @foreach($agents as $agentId => $agentName)
                    <option value="{{ $agentId }}" @if($agentName==auth()->user()->name) selected @endif>
                        {{ $agentName }}
                    </option>
                    @endforeach
                </select>
            </div>



            <div class="col-6 form-group">
                <label for="lead_id" class="form-label">{{ __('Lead ID / Phone Number') }}</label>
                <input type="text" name="lead_id" class="form-control" required>
            </div>

            <div class="col-6 form-group">
                <label for="dialer_id" class="form-label">{{ __('Dialer ID') }}</label>
                <input type="text" name="dialer_id" class="form-control" required>
            </div>

            <div class="col-6 form-group">
                <label for="AGE" class="form-label">{{ __('Age') }}</label>
                <input type="text" name="AGE" class="form-control" required>
            </div>

            <div class="col-6 form-group">
                <label for="Smoker" class="form-label">{{ __('Smoker') }}</label>
                <select name="Smoker" class="form-select" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>


            <div class="col-6 form-group">
                <label for="verifier" class="form-label">{{ __('Closer') }}</label>

                <select name="verifier" class="form-control" required>
                    @foreach($verifiers as $verifierId => $verifierName)
                    <option value="{{ $verifierId }}">{{ $verifierName }}</option>
                    @endforeach
                </select>
            </div>





            {{-- Center: uses auth user's center_id on submit (no selection needed) --}}

            <div class="col-12">
                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
    @endsection

</body>

</html>