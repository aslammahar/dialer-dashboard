<!-- resources/views/closer/index.blade.php -->
@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Policy') }}
@endsection

@section('content')
<div class="create-link mb-4">
    <a href="{{ route('closer.create') }}" class="btn btn-primary">Create</a>
</div>

<div class="container mt-4">
    <div class="mb-4 col-md-3">
        <label for="agent_status" class="form-label">Filter by Sale Status</label>
        <select class="form-control" id="agent_status" name="agent_status">
            <option value="">Select Sale Status</option>
            <option value="pending">Call Back</option>
            <option value="Dropped Call">Dropped Call</option>
            <option value="Sale made">Sale made</option>
            <option value="Scheduled Call Back">Scheduled Call Back</option>
        </select>
    </div>

    <div class="table-responsive">
        <table id="closedCallsTable" class="table table-bordered table-striped align-middle">
            <thead style="background-color: #1a1a1a; color: #ffffff; font-weight: bold;">
                <tr>
                    <th>Time</th>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>State</th>
                    <th>Closer</th>
                    <th>Underwriter</th>
                    <th>Client Comments</th>
                    <th>Carrier</th>
                    <th>Sale Status</th>
                    <th>Status</th>
                    <th>Monthly Premium</th>
                    <th>Junior Closer</th>
                    <th>Filled By</th>
                    <th>Dialer Id</th>

                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($closedCalls as $closedCall)
                    <tr>
                        <td>{{ $closedCall->created_at->format('Y-m-d') }}</td>
                        <td>{{ $closedCall->id }}</td>
                        <td>{{ $closedCall->customer_full_name }}</td>
                        <td>{{ $closedCall->state }}</td>
                        <td>{{ $closedCall->closername }}</td>
                        <td>{{ $closedCall->client->name ?? 'N/A' }}</td>
                        <td>{{ $closedCall->clients_comment ?? 'N/A' }}</td>
                        <td>{{ $closedCall->carrier ?? 'N/A' }}</td>
                        <td>{{ $closedCall->agent_status ?? 'N/A' }}</td>
                        <td>{{ $closedCall->status ?? 'N/A' }}</td>
                        <td>{{ $closedCall->monthly_premium ?? 'N/A' }}</td>
                        <td>{{ $closedCall->juniorcloser->name ?? $closedCall->junior_closer_name }}</td>
                        <td>{{ $closedCall->closer->name ?? 'N/A' }}</td>
                        <td>{{ $dialer_id ?? 'N/A' }}</td>

                        <td>

                            @if($closedCall->agent_status !== 'Sale made' && !empty($closedCall->phone_number))
                                <a href="{{ route('closer.callback', ['id' => $closedCall->id]) }}" 
                                class="btn btn-sm btn-warning" 
                                target="_blank">Callback</a>
                                <a href="{{ url('/closers-edit', parameters: $closedCall->id) }}" class="btn btn-info btn-sm"> Edit</a>

                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination">
                    {{ $closedCalls->links('pagination::bootstrap-5') }}
                </ul>
            </nav>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        // Filter functionality
        $('#agent_status').on('change', function () {
            var selectedStatus = $(this).val();
            $('#closedCallsTable tbody tr').each(function () {
                var agentStatus = $(this).find('td:eq(8)').text().trim();
                if (selectedStatus === '') {
                    $(this).show();
                } else if (agentStatus === selectedStatus) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Callback button functionality
        // $('.callback-btn').on('click', function() {
        //     var callId = $(this).data('id');
        //     $.ajax({
        //         url: '{{ url("/closer/callback") }}/' + callId,
        //         type: 'GET',
        //         success: function(response) {
        //             if (response.status === 'success') {
        //                 // Open dialer in new tab
        //                 window.open(response.dialer_url, '_blank');
        //             } else {
        //                 alert(response.message || 'Error initiating callback');
        //             }
        //         },
        //         error: function() {
        //             alert('Error connecting to the server');
        //         }
        //     });
        // });
    });
</script>

@if(session('dialer_open') && session('dialer_url'))
<script>
    $(document).ready(function() {
        var dialerUrl = "{{ session('dialer_url') }}";
        window.open(dialerUrl, '_blank');
    });
</script>
@endif

<style>
    /* Existing pagination styles */
    .pagination .page-item .page-link {
        color: #333;
    }
    .pagination .page-item .page-link:hover {
        color: #fff;
        background-color: #007bff;
    }
    .pagination .page-item.active .page-link {
        background-color: #000;
        border-color: #000;
        color: #fff;
    }
    table.table-bordered tbody tr:hover {
        background-color: #f0f0f0;
    }

    /* Enhanced table styling */
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    /* Table general styling */
    #closedCallsTable {
        margin-bottom: 0;
    }

    #closedCallsTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        font-weight: bold !important;
        border-bottom: 2px solid #000;
        padding: 12px 8px;
        white-space: nowrap;
        min-width: 120px;
    }

    #closedCallsTable tbody td {
        padding: 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    #closedCallsTable tbody tr {
        height: auto !important;
    }

    /* Client Comment Column Specific Styling (7th column) */
    #closedCallsTable tbody td:nth-child(7), 
    #closedCallsTable thead th:nth-child(7) {
        min-width: 800px !important;
        max-width: 400px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow: visible !important;
        text-overflow: clip !important;
        vertical-align: top !important;
        line-height: 1.4 !important;
        padding: 12px 8px !important;
    }

    /* Enhanced styling for client comment column */
    #closedCallsTable tbody td:nth-child(7) {
        font-size: 0.85rem;
        color: #333;
        background-color: #f8f9fa;
    }

    #closedCallsTable thead th:nth-child(7) {
        text-align: center !important;
        font-weight: bold !important;
    }

    /* Action column styling */
    #closedCallsTable tbody td:last-child {
        min-width: 150px !important;
        white-space: nowrap !important;
    }

    /* Button styling in action column */
    #closedCallsTable tbody td:last-child .btn {
        margin: 2px;
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    /* Scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .table-responsive {
            max-height: 60vh;
        }
        
        #closedCallsTable thead th {
            min-width: 100px;
            font-size: 0.875rem;
            padding: 8px 4px;
        }
        
        #closedCallsTable tbody td {
            font-size: 0.875rem;
            padding: 6px 4px;
            max-width: 150px;
        }

        /* Client comment column responsive adjustments */
        #closedCallsTable tbody td:nth-child(7), 
        #closedCallsTable thead th:nth-child(7) {
            min-width: 250px !important;
            max-width: 300px !important;
            font-size: 0.8rem !important;
        }

        /* Action column responsive */
        #closedCallsTable tbody td:last-child {
            min-width: 120px !important;
        }

        #closedCallsTable tbody td:last-child .btn {
            font-size: 0.7rem;
            padding: 3px 6px;
            margin: 1px;
        }
    }

    /* For very small screens */
    @media (max-width: 576px) {
        #closedCallsTable tbody td:nth-child(7), 
        #closedCallsTable thead th:nth-child(7) {
            min-width: 200px !important;
            max-width: 250px !important;
            font-size: 0.75rem !important;
            padding: 8px 4px !important;
        }
    }

    /* Filter dropdown styling */
    #agent_status {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    #agent_status:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        outline: 0;
    }

    /* Container spacing */
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }

    /* Create link buttons */
    .create-link .btn {
        margin-right: 10px;
        margin-bottom: 10px;
    }
</style>
@endsection