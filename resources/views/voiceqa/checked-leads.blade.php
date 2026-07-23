
{{-- <style>
 .styled-table {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
.styled-table thead tr {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
}

.styled-table th,
.styled-table td {
    padding: 12px 15px;
}
.styled-table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #009879;
}
.styled-table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}

</style> --}}



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
    <li class="breadcrumb-item">{{__('avatarLead')}}</li>
@endsection

    <!-- Add the download button -->
  <!--- BY IMRAN NIAZ THE SMEXY MAN  -->
  
 @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Order')}}</li>
@endsection

   


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    

    <table class="table datatable">
        <thead>
            <tr>
                <th>Agent ID</th>
                <th>Lead ID</th>
                <th>Recording</th>
                <th>GREETINGS</th>
                <th>PITCH_Call_About</th>
                <th>AGE</th>
                <th>Smoker</th>
                <th>Health1</th>
                <th>Beneficiary</th>
                <th>Account</th>
                <th>Plan</th>
                <th>Transfer_details</th>
                <th>Xfer_Consent</th>
                <th>Rebuttals</th>
                <th>COMMENTS</th>
                <th>Status</th>
                <th>QA_Person</th>
                <th>Use_of_Rebuttals</th>
                <th>No_of_Refusals</th>
                <th>count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userLeads as $lead)
            @php
            $statusColor = $lead->Status === 'Approved' ? 'green' : ($lead->Status === 'Rejected' ? 'red' : 'inherit');
        @endphp
        <tr style="background-color: {{ $statusColor }}; color: white;">
                    <td>{{ $lead->agent->name }}</td>
                    <td>{{ $lead->lead_id }}</td>
                    <td>{{ $lead->recording }}</td>
                    <td>{{ $lead->GREETINGS }}</td>
                    <td>{{ $lead->PITCH_Call_About }}</td>
                    <td>{{ $lead->AGE }}</td>
                    <td>{{ $lead->Smoker }}</td>
                    <td>{{ $lead->Health1 }}</td>
                    <td>{{ $lead->Beneficiary }}</td>
                    <td>{{ $lead->Account }}</td>
                    <td>{{ $lead->Plan }}</td>
                    <td>{{ $lead->Transfer_details }}</td>
                    <td>{{ $lead->Xfer_Consent }}</td>
                    <td>{{ $lead->Rebuttals }}</td>
                    <td>{{ $lead->COMMENTS }}</td>
                    <td>{{ $lead->Status }}</td>
                    <td>{{ $lead->QA_Person }}</td>
                    <td>{{ $lead->Use_of_Rebuttals }}</td>
                    <td>{{ $lead->No_of_Refusals }}</td>
                    <td>{{ $lead->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    
    
</div>
</div>
</div>
</div>
</div>

@endsection