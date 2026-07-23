@extends('layouts.admin')

@section('page-title', 'My Support Tickets')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Support Tickets</li>
@endsection

@section('content')
<div class="container-fluid py-4">

    <!-- Main Card -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        
        <!-- Header with Gradient -->
        <div class="card-header bg-gradient text-white border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0 fw-bold">
                        My Support Tickets
                    </h3>
                    <p class="mb-0 mt-2 opacity-90">View and manage your submitted tickets</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('department_support_tickets.create') }}" class="btn btn-light btn-lg shadow-sm">
                        + Create New Ticket
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-5">
            <!-- Premium Summary Cards (Sirf Apne Tickets Ka) -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-lg rounded-4 h-100 text-white hover-lift" 
             style="background: linear-gradient(135deg, #5a67d8, #4c51bf);">
            <div class="card-body text-center p-5">
                <div class="display-4 fw-bold">{{ $mySummary['total'] }}</div>
                <p class="fs-5 mb-0 opacity-90">My Total Tickets</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-lg rounded-4 h-100 text-white hover-lift" 
             style="background: linear-gradient(135deg, #f39c12, #e67e22);">
            <div class="card-body text-center p-5">
                <div class="display-4 fw-bold">{{ $mySummary['pending'] }}</div>
                <p class="fs-5 mb-0 opacity-90">Pending</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-lg rounded-4 h-100 text-white hover-lift" 
             style="background: linear-gradient(135deg, #27ae60, #1e8449);">
            <div class="card-body text-center p-5">
                <div class="display-4 fw-bold">{{ $mySummary['solved'] }}</div>
                <p class="fs-5 mb-0 opacity-90">Solved</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-lg rounded-4 h-100 text-white hover-lift" 
             style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
            <div class="card-body text-center p-5">
                <div class="display-4 fw-bold">{{ $mySummary['declined'] }}</div>
                <p class="fs-5 mb-0 opacity-90">Declined</p>
            </div>
        </div>
    </div>
</div>
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- My Tickets -->
            <h4 class="text-primary mb-4 fw-bold">
                My Submitted Tickets
            </h4>

            @if($myCreatedTickets->isEmpty())
                <div class="text-center py-5">
                    <img src="{{ asset('assets/img/empty.svg') }}" alt="No tickets" class="mb-4 opacity-50" style="max-width: 280px;">
                    <h5 class="text-muted">Aap ne abhi tak koi ticket submit nahi ki.</h5>
                    <a href="{{ route('department_support_tickets.create') }}" class="btn btn-primary mt-3">
                        Create Your First Ticket
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 fw-bold text-dark">#</th>
                                <th class="py-3 fw-bold text-dark">Department</th>
                                <th class="py-3 fw-bold text-dark">Title</th>
                                <th class="py-3 fw-bold text-dark">Subject</th>
                                <th class="py-3 fw-bold text-dark">Status</th>
                                <th class="py-3 fw-bold text-dark">Responses</th>
                                <th class="py-3 fw-bold text-dark">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myCreatedTickets as $ticket)
                                @php 
                                    $responses = json_decode($ticket->response, true) ?? [];
                                    $departmentName = $ticket->support?->role_id 
                                        ? \App\Models\Role::find($ticket->support->role_id)?->name 
                                        : '—';
                                @endphp
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><strong class="text-primary">#{{ $ticket->id }}</strong></td>
                                    <td class="py-3">
                                        <span class="badge bg-primary text-white px-3 py-2 fw-bold">
                                            {{ $departmentName }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-info text-dark px-3 py-2">
                                            {{ $ticket->support->title ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <strong>{{ $ticket->subject }}</strong><br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M, Y') }}</small>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge px-3 py-2 fs-6
                                            {{ $ticket->status == 'Solved' ? 'bg-success' : ($ticket->status == 'Declined' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-secondary fs-6">
                                            {{ count($responses) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#ticketModal{{ $ticket->id }}">
                                            View
                                        </button>
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

<!-- Modal for Ticket Details -->
@foreach($myCreatedTickets as $ticket)
    @php 
        $responses = json_decode($ticket->response, true) ?? [];
        $departmentName = $ticket->support?->role_id 
            ? \App\Models\Role::find($ticket->support->role_id)?->name 
            : '—';
    @endphp
    <div class="modal fade" id="ticketModal{{ $ticket->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Ticket #{{ $ticket->id }} - {{ $ticket->support->title ?? 'N/A' }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Department:</strong> <span class="badge bg-primary text-white">{{ $departmentName }}</span></p>
                            <p class="mb-2"><strong>Subject:</strong> {{ $ticket->subject }}</p>
                            <p class="mb-2"><strong>Status:</strong>
                                <span class="badge {{ $ticket->status == 'Solved' ? 'bg-success' : ($ticket->status == 'Declined' ? 'bg-danger' : 'bg-warning') }} fs-6">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </p>
                            <p class="mb-2"><strong>Submitted On:</strong> {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M, Y - h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Description</h6>
                            <div class="bg-light p-4 rounded border">{{ $ticket->description }}</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-primary mb-4">Conversation History</h6>
                    <div class="border rounded p-4 bg-white" style="max-height: 400px; overflow-y: auto;">
                        @forelse($responses as $resp)
                            <div class="mb-4 pb-3 border-bottom last:border-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">{{ $resp['by'] }}</strong>
                                        <small class="text-muted ms-2">
                                            {{ \Carbon\Carbon::parse($resp['time'])->format('d M Y - h:i A') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-2 bg-light p-3 rounded">{!! nl2br(e($resp['msg'])) !!}</div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">No replies yet. Your ticket is under review.</p>
                        @endforelse
                    </div>

                    @if($ticket->status !== 'Solved' && $ticket->status !== 'Declined')
                        <form action="{{ route('department_support_tickets.userupdateStatus', ['id' => $ticket->id, 'status' => $ticket->status]) }}" method="POST" class="mt-4">
                            @csrf
                            <textarea name="reply" class="form-control form-control-lg shadow-sm" rows="4" placeholder="Add your reply or update..." required></textarea>
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3 shadow">
                                Send Reply
                            </button>
                        </form>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    @if($ticket->status === 'Solved' || $ticket->status === 'Declined')
                        <form action="{{ route('department_support_tickets.userupdateStatus', ['id' => $ticket->id, 'status' => 'Pending']) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning shadow-sm">
                                Reopen Ticket
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Custom Styles -->
<style>
    .hover-shadow-lg:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important; 
    }
    .transition { transition: all 0.3s ease; }
    .card { border-radius: 1rem !important; }
    .bg-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .rounded-4 { border-radius: 1rem; }
    .shadow-lg { box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important; }
</style>
@endsection