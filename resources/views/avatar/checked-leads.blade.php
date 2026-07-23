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
<li class="breadcrumb-item">{{__('AvatarLeads')}}</li>
@endsection




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
                $pendingleads = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->QAstatus === 'pending' && $lead->created_at->month === $currentMonth;
                });

                $currentMonth = now()->month;
                $approvedLeadsThisMonth = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->QAstatus === 'approved' && $lead->created_at->month === $currentMonth;
                });

                $rejectedLeadsThisMonth = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->QAstatus === 'rejected' && $lead->created_at->month === $currentMonth;
                });



                $totalLeadsThisMonth = $userLeads->filter(function ($lead) use ($currentMonth) {
                return $lead->created_at->month === $currentMonth;
                });

                $totalApprovedLeads = $approvedLeadsThisMonth->count();
                $totalpendingleads = $pendingleads->count();
                $totalRejectedLeads = $rejectedLeadsThisMonth->count();
                $totalLeads = $totalLeadsThisMonth->count();
                $averageLeadsPerDay = $totalApprovedLeads > 0 ? round($totalApprovedLeads / now()->daysInMonth, 2) : 0;
                @endphp

                <p>Total Leads: {{ $totalLeads }}</p>
                <p>Total Pending Leads: {{ $totalpendingleads }}</p>
                <p>Total Approved Leads: {{ $totalApprovedLeads }}</p>
                <p>Total Rejected Leads: {{ $totalRejectedLeads }}</p>
                <p>Leads per Day (Approved): {{ $averageLeadsPerDay }}</p>



                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Agent</th>
                                        <th>Phone Number / Lead Id</th>
                                        <th>Dialer Id</th>
                                        <th>AGE</th>
                                        <th>Smoker</th>
                                        <th>Verifier</th>
                                        <th>Center</th>
                                        <th>Recording</th>
                                        <th>QA Person</th>
                                        <th>QA Comments</th>
                                        <th>QA Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userLeads as $lead)
                                    @php
                                    $statusColor = $lead->QAstatus === 'approved' ? '#66cc66' : ($lead->QAstatus === 'rejected' ? '#ff4d4d' : '#f2f2f2');
                                    @endphp
                                    <tr>
                                        <td>{{ $lead->agent->name }}</td>
                                        <td>{{ $lead->lead_id }}</td>
                                        <td>{{ $lead->dialer_id }}</td>
                                        <td>{{ $lead->AGE }}</td>
                                        <td>{{ $lead->Smoker }}</td>
                                        <td>{{ $lead->verifier }}</td>
                                        <td>{{ $lead->center }}</td>
                                        <td>{{ $lead->recording }}</td>
                                        <td>{{ isset($lead->qaPerson) ? $lead->qaPerson->name : 'N/A' }}</td>
                                        <td>{{ $lead->Qacomments }}</td>
                                        <td style="background-color: {{ $statusColor }}; color: black;">{{ $lead->QAstatus }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
</div>
@endsection