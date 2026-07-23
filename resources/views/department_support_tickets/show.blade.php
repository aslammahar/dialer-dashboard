@extends('layouts.admin')

@section('page-title', 'View Ticket')

@section('content')
<div class="container mt-4">

    <!-- Button to Open Assigned Tickets Modal -->
    <div class="mb-4 text-end">
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#assignedTicketsModal">
            <i class="fas fa-clipboard-list"></i> My Assigned Tickets ({{ $myAssignedSummary['total'] }})
        </button>
    </div>

    <hr class="my-4">

    <!-- TICKET DETAIL -->
    <h4 class="mb-4 text-dark">Ticket Detail</h4>

    <div class="card shadow-sm">
        <div class="card-header {{ $ticket->status == 'Solved' ? 'bg-success' : ($ticket->status == 'Declined' ? 'bg-danger' : 'bg-primary') }} text-white">
            <h5 class="mb-0">
                Ticket #{{ $ticket->id }} - {{ $ticket->subject }}
                <span class="badge bg-light text-dark ms-3">{{ ucfirst($ticket->status) }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Department:</strong> <span class="text-primary">{{ $ticket->support->title ?? 'N/A' }}</span></p>
                    <p><strong>Created By:</strong> <span class="text-success fw-bold">{{ $ticket->user->name ?? 'Unknown' }}</span></p>
                    <p><strong>Created At:</strong> {{ $ticket->created_at->format('d M, Y - h:i A') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Assigned To:</strong>
                        @if(!empty($assignedUsers))
                            @foreach($assignedUsers as $user)
                                <span class="badge bg-info text-dark me-1">{{ $user }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Not Assigned</span>
                        @endif
                    </p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $ticket->status == 'Solved' ? 'bg-success' : ($ticket->status == 'Declined' ? 'bg-danger' : 'bg-warning text-dark') }} fs-6">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <hr>

            <p><strong>Description:</strong></p>
            <div class="bg-light p-3 rounded border-start border-primary border-4 mb-4">
                {{ $ticket->description ?? 'No description.' }}
            </div>

            <!-- Conversation History -->
            @if(!empty($ticket->response))
                <p><strong>Conversation History:</strong></p>
                @php $responses = json_decode($ticket->response, true); @endphp
                @foreach($responses as $index => $resp)
                    <div class="border-start {{ $index == count($responses)-1 && ($ticket->status == 'Solved' || $ticket->status == 'Declined') ? 'border-success border-5' : 'border-primary' }} ps-3 py-2 mb-3 bg-light rounded">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $resp['by'] ?? 'User' }}</strong>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($resp['time'])->format('d M, Y - h:i A') }}</small>
                        </div>
                        <div class="mt-2">{!! nl2br(e($resp['msg'])) !!}</div>
                    </div>
                @endforeach

                <!-- Last Reply Highlight -->
                @if($ticket->status == 'Solved' || $ticket->status == 'Declined')
                    @php $lastReply = end($responses); @endphp
                    <div class="alert {{ $ticket->status == 'Solved' ? 'alert-success' : 'alert-danger' }} mt-4">
                        <h6 class="alert-heading">
                            {{ $ticket->status == 'Solved' ? 'Ticket Solved' : 'Ticket Declined' }} Reason:
                        </h6>
                        <hr>
                        <p class="mb-0">
                            <strong>{{ $lastReply['by'] }}</strong>:<br>
                            <em>{{ $lastReply['msg'] }}</em>
                        </p>
                        <small class="text-muted float-end">
                            {{ \Carbon\Carbon::parse($lastReply['time'])->format('d M Y, h:i A') }}
                        </small>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Reply Forms -->
    @if($ticket->status === 'Pending')
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Take Action</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <form action="{{ route('department_support_tickets.updateStatus', ['id' => $ticket->id, 'status' => 'Solved']) }}" method="POST">
                            @csrf
                            <textarea name="reply" class="form-control" rows="4" placeholder="Write solution..." required></textarea>
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Mark as Solved</button>
                        </form>
                    </div>
                    <div class="col-md-6 mb-3">
                        <form action="{{ route('department_support_tickets.updateStatus', ['id' => $ticket->id, 'status' => 'Declined']) }}" method="POST">
                            @csrf
                            <textarea name="reply" class="form-control" rows="4" placeholder="Reason for decline..." required></textarea>
                            <button type="submit" class="btn btn-danger btn-lg w-100 mt-3">Decline Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-4">
            This ticket is <strong>{{ ucfirst($ticket->status) }}</strong>. No further action needed.
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">Back to Tickets</a>
    </div>
</div>

<!-- Modal: My Assigned Tickets List -->
<div class="modal fade" id="assignedTicketsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    My Assigned Tickets ({{ $myAssignedSummary['total'] }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($myAssignedTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Ticket ID</th>
                                    <th>Created By</th>
                                    <th>Subject</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myAssignedTickets as $index => $assignedTicket)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>#{{ $assignedTicket->id }}</strong></td>
                                    <td>{{ $assignedTicket->user->name ?? 'Unknown' }}</td>
                                    <td>{{ Str::limit($assignedTicket->subject, 40) }}</td>
                                    <td>{{ $assignedTicket->support->title ?? '—' }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $assignedTicket->status == 'Solved' ? 'bg-success' : 
                                               ($assignedTicket->status == 'Declined' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($assignedTicket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('department_support_tickets.show', $assignedTicket->id) }}" 
                                           class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">No tickets assigned to you yet.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection