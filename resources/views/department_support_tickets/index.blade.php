@extends('layouts.admin')

@section('page-title')
    {{ __('Department Support Tickets') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Department Support Tickets') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">


                        @if(session('success'))
                            <div class="alert alert-success mt-2">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($tickets->isEmpty())
                            <div class="alert alert-info mt-2">
                                {{ __('No support tickets found.') }}
                            </div>
                        @else
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-striped datatable text-center align-middle">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Users') }}</th>
                                            <th>{{ __('Subject') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Response') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $ticket->support ? $ticket->support->title : 'N/A' }}</td>

                                                <td>
                                                    @foreach($ticket->assignedUsers as $user)
                                                        <span class="badge bg-info">{{ $user->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $ticket->subject }}</td>
                                                <td>{{ $ticket->description }}</td>
                                                <td>
                                                    <span class="badge 
                                                                @if($ticket->status == 'Solved') bg-success 
                                                                @elseif($ticket->status == 'Declined') bg-danger 
                                                                @else bg-warning 
                                                                @endif">
                                                        {{ ucfirst($ticket->status ?? 'Pending') }}
                                                    </span>
                                                </td>
                                                <td>
    @php
        $responses = json_decode($ticket->response, true);
    @endphp

    @if(!empty($responses))
        @foreach($responses as $resp)
            <div>
                <strong>{{ $resp['by'] }}:</strong> {{ $resp['msg'] }} <br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($resp['time'])->format('d M, Y H:i') }}</small>
            </div>
            <hr>
        @endforeach
    @else
        —
    @endif
</td>

                                                <td>
                                                    <a href="{{ route('department_support_tickets.show', $ticket->id) }}"
                                                        class="btn btn-info btn-sm w-100 mb-1">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection