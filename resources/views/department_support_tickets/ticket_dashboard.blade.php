@extends('layouts.admin')

@section('page-title', 'Tickets Summary Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tickets Summary Dashboard</li>
@endsection

@section('content')
    <div class="container-fluid py-4">

        <!-- Main Card -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- Header with Gradient -->
            <div class="card-header bg-gradient text-white border-0 py-4"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0 fw-bold">
                            Tickets Summary Dashboard
                        </h3>
                        <p class="mb-0 mt-2 opacity-90">Real-time overview of all support tickets</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">

                <!-- Filters -->
                <form method="GET" action="{{ route('tickets.dashboard') }}" class="bg-light rounded-3 p-4 mb-5 shadow-sm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark">From Date</label>
                            <input type="date" name="from" class="form-control form-control-lg shadow-sm"
                                value="{{ request('from') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark">To Date</label>
                            <input type="date" name="to" class="form-control form-control-lg shadow-sm"
                                value="{{ request('to') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">Department</label>
                            <select name="department" class="form-select form-select-lg shadow-sm">
                                <option value="">All Departments</option>
                                @foreach($departments as $role)
                                    <option value="{{ $role->id }}" {{ request('department') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-lg w-100 shadow">
                                Apply
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow h-100 hover-shadow-lg transition"
                            style="border-left: 6px solid #5a67d8 !important;">
                            <div class="card-body text-center p-4">
                                <div class="display-4 fw-bold text-primary mb-2">{{ $summary['total'] }}</div>
                                <p class="text-muted mb-0 fw-bold fs-5">Total Tickets</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow h-100 hover-shadow-lg transition"
                            style="border-left: 6px solid #f39c12 !important;">
                            <div class="card-body text-center p-4">
                                <div class="display-4 fw-bold text-warning mb-2">{{ $summary['pending'] }}</div>
                                <p class="text-muted mb-0 fw-bold fs-5">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow h-100 hover-shadow-lg transition"
                            style="border-left: 6px solid #27ae60 !important;">
                            <div class="card-body text-center p-4">
                                <div class="display-4 fw-bold text-success mb-2">{{ $summary['solved'] }}</div>
                                <p class="text-muted mb-0 fw-bold fs-5">Solved</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow h-100 hover-shadow-lg transition"
                            style="border-left: 6px solid #e74c3c !important;">
                            <div class="card-body text-center p-4">
                                <div class="display-4 fw-bold text-danger mb-2">{{ $summary['declined'] }}</div>
                                <p class="text-muted mb-0 fw-bold fs-5">Declined</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Wise Tickets -->
                <h4 class="text-primary mb-4 fw-bold">
                    Employee Wise Tickets Detail
                </h4>

                @forelse($employeeTickets as $employeeId => $data)
                    <div class="card mb-4 border-0 shadow-lg rounded-4 overflow-hidden hover-shadow-xl transition">

                        <!-- Employee Header -->
                        <!-- Employee Header - Updated with Icon -->
                        <div class="bg-dark text-white py-4 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <span class="fs-3 fw-bold">{{ Str::substr($data['name'], 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-white fw-bold">{{ $data['name'] }}</h4>
                                        <p class="mb-0 text-warning fw-bold fs-5 d-flex align-items-center gap-2">
                                            <i class="fas fa-building"></i>
                                            {{ $data['department'] }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark fs-5 px-4 py-3 rounded-pill shadow-sm">
                                        {{ $data['tickets']->count() }} Ticket{{ $data['tickets']->count() > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light border-top border-bottom border-2 border-primary">
                                    <tr>
                                        <th class="text-center fw-bold text-dark py-4"
                                            style="width: 90px; min-width: 90px; font-size: 1rem;">
                                            ID
                                        </th>
                                        <th class="px-4 py-4 fw-bold text-dark">Department</th>
                                        <th class="px-4 py-4 fw-bold text-dark">Subject</th>
                                        <th class="px-4 py-4 fw-bold text-dark">Title</th>
                                        <th class="px-4 py-4 fw-bold text-dark text-center">Status</th>
                                        <th class="px-4 py-4 fw-bold text-dark">Last Reply</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['tickets'] as $ticket)
                                                        @php
                                                            $responses = json_decode($ticket->response, true) ?? [];
                                                            $lastReply = $responses ? end($responses) : null;

                                                            $departmentName = $ticket->support?->role_id
                                                                ? \App\Models\Role::find($ticket->support->role_id)?->name ?? '—'
                                                                : '—';
                                                        @endphp

                                                        <!-- Main Ticket Row -->
                                                        <tr class="border-bottom hover-bg-light transition">
                                                            <!-- ID - Perfectly Centered & Padded -->
                                                            <td class="text-center py-4" style="width: 90px; font-size: 1.1rem;">
                                                                <strong
                                                                    class="text-primary">#{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</strong>
                                                            </td>

                                                            <!-- Department -->
                                                            <td class="py-4 align-middle">
                                                                <span class="badge bg-primary text-white px-4 py-2 fw-bold fs-6 rounded-pill">
                                                                    {{ $departmentName }}
                                                                </span>
                                                            </td>

                                                            <!-- Subject + Date -->
                                                            <td class="py-4 align-middle">
                                                                <div>
                                                                    <strong class="text-dark">{{ $ticket->subject }}</strong><br>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M, Y') }}
                                                                    </small>
                                                                </div>
                                                            </td>

                                                            <!-- Title -->
                                                            <td class="py-4 align-middle">
                                                                <span class="badge bg-info text-dark px-3 py-2 fs-6">
                                                                    {{ $ticket->support->title ?? '—' }}
                                                                </span>
                                                            </td>

                                                            <!-- Status -->
                                                            <td class="py-4 text-center align-middle">
                                                                <span class="badge px-4 py-2 fs-6 fw-bold
                                                    {{ $ticket->status == 'Solved' ? 'bg-success text-white' :
                                        ($ticket->status == 'Declined' ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                                                                    {{ ucfirst($ticket->status) }}
                                                                </span>
                                                            </td>

                                                            <!-- Last Reply -->
                                                            <td class="py-4 align-middle">
                                                                @if($lastReply)
                                                                    <div>
                                                                        <strong class="text-dark d-block">{{ $lastReply['by'] }}</strong>
                                                                        <small class="text-muted">{{ Str::limit($lastReply['msg'], 50) }}</small>
                                                                    </div>
                                                                @else
                                                                    <em class="text-muted small">No reply yet</em>
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        <!-- Detail Row -->
                                                        <tr class="detail-row-{{ $ticket->id }} bg-light" style="display: none;">
                                                            <td colspan="6" class="p-5">
                                                                <div class="row g-5">
                                                                    <div class="col-lg-6">
                                                                        <h6 class="fw-bold text-primary mb-3">Full Description</h6>
                                                                        <div class="bg-white p-4 rounded-3 shadow-sm border">
                                                                            {!! nl2br(e($ticket->description)) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <h6 class="fw-bold text-primary mb-3">Conversation History</h6>
                                                                        <div class="border rounded-3 p-4 bg-white"
                                                                            style="max-height: 380px; overflow-y: auto;">
                                                                            @forelse($responses as $resp)
                                                                                <div class="border-bottom pb-3 mb-3 last:border-0">
                                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                                        <strong class="text-primary">{{ $resp['by'] }}</strong>
                                                                                        <small class="text-muted">
                                                                                            {{ \Carbon\Carbon::parse($resp['time'])->format('d M Y - h:i A') }}
                                                                                        </small>
                                                                                    </div>
                                                                                    <div class="mt-2 text-dark">{!! nl2br(e($resp['msg'])) !!}</div>
                                                                                </div>
                                                                            @empty
                                                                                <p class="text-muted text-center py-4 mb-0">No conversation yet.</p>
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-center mt-4">
                                                                    <button type="button" class="btn btn-outline-danger btn-lg px-5 rounded-pill"
                                                                        onclick="closeDetail({{ $ticket->id }})">
                                                                        Close Details
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <!-- Expand Button Row -->
                                                        <tr class="expand-trigger-{{ $ticket->id }}">
                                                            <td colspan="6" class="text-center py-4 bg-light border-0">
                                                                <button type="button" class="btn btn-link text-primary fw-bold fs-6"
                                                                    onclick="openDetail({{ $ticket->id }})">
                                                                    View Full Details & Conversation
                                                                </button>
                                                            </td>
                                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <img src="{{ asset('assets/img/empty.svg') }}" alt="No data" class="mb-4 opacity-50"
                            style="max-width: 250px;">
                        <h5 class="text-muted">No tickets found for the selected date range.</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- JavaScript for Toggle (100% Working) -->
    <script>
        function openDetail(id) {
            document.querySelector('.detail-row-' + id).style.display = 'table-row';
            document.querySelectorAll('.expand-trigger-' + id).forEach(row => {
                row.style.display = 'none';
            });
        }

        function closeDetail(id) {
            document.querySelector('.detail-row-' + id).style.display = 'none';
            document.querySelectorAll('.expand-trigger-' + id).forEach(row => {
                row.style.display = 'table-row';
            });
        }
    </script>

    <!-- Custom Styles -->
    <style>
        .hover-shadow-lg:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
        }

        .hover-shadow-xl:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
        }

        .transition {
            transition: all 0.3s ease;
        }

        .card {
            border-radius: 1rem !important;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .rounded-4 {
            border-radius: 1rem;
        }

        .shadow-lg {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection