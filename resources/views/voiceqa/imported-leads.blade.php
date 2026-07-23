

@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{__('Support')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('avatarLead')}}</h5>
    </div>
@endsection
<!-- Header script by imran niaz  -->
@push('theme-script')
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@section('breadcrumb')
<link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('VoiceLead')}}</li>
@endsection

    <!-- Add the download button -->
  <!--- BY IMRAN NIAZ THE SMEXY MAN  -->
  
 @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Order')}}</li>
@endsection

   


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <h3>Leads Summary for {{ now()->format('F Y') }}</h3>

                            @php
            $currentMonth = now()->month;
            $approvedLeadsThisMonth = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->status === 'approved' && $lead->created_at->month === $currentMonth;
            });

            $rejectedLeadsThisMonth = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->status === 'rejected' && $lead->created_at->month === $currentMonth;
            });



            $totalLeadsThisMonth = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->created_at->month === $currentMonth;
            });

            $totalApprovedLeads = $approvedLeadsThisMonth->count();
            $totalRejectedLeads = $rejectedLeadsThisMonth->count();
            $totalLeads = $totalLeadsThisMonth->count();
            $averageLeadsPerDay = $totalApprovedLeads > 0 ? round($totalApprovedLeads / now()->daysInMonth, 2) : 0;
        @endphp
         
        <p>Total Leads: {{ $totalLeads }}</p>
        <p>Total Approved Leads: {{ $totalApprovedLeads }}</p>
        <p>Total Rejected Leads: {{ $totalRejectedLeads }}</p>
        <p>Leads per Day (Approved): {{ $averageLeadsPerDay }}</p>



                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Phone Number</th>
                            <th>State</th>
                            <th>Licensed Agent Name</th>
                            <th>Status</th>
                            <th>Comments</th>
                            <th>Recordings</th>
                            <th>QA Person</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userLeads as $lead)
                            @php
                                $statusColor = $lead->status === 'approved' ? 'green' : ($lead->status === 'rejected' ? 'red' : 'inherit');
                            @endphp
                            <tr style="background-color: {{ $statusColor }}; color: white;">
                                <td>{{ $lead->user->name }}</td>
                                <td>{{ $lead->phone_number }}</td>
                                <td>{{ $lead->state }}</td>
                                <td>{{ $lead->licenced_agent_name }}</td>
                                <td>{{ $lead->status }}</td>
                                <td>{{ $lead->comments }}</td>
                                <td>{{ $lead->recordings }}</td>
                                <td>{{ $lead->qa_person }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
@endsection