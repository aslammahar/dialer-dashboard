@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Policy') }}
@endsection

@section('content')

<div class="create-link mb-4">
    @can('fe calls')
        <a href="{{ route('closer.salesagentshow') }}" class="btn btn-primary">FE Closed Sales</a>
    @endcan
    <a href="{{ route('closer.create') }}" class="btn btn-primary">Create</a>
</div>

<div class="container mt-4">
    <!-- Dropdown for Selecting Records Per Page -->
    <div class="d-flex justify-content-between mb-3">
        <form method="GET" id="paginationForm">
            <label for="per_page" class="form-label fw-bold me-2">Show Records:</label>
            <select name="per_page" id="per_page" class="form-select d-inline-block w-20">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
            </select>
        </form>

        <!-- Search Input -->
        <div class="d-inline-flex align-items-center">
            <input type="text" id="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table id="closedCallsTable" class="table table-bordered table-striped align-middle">
            <thead style="background-color: #000000; color: #ffffff; font-weight: bold;">
                <tr>
                <th>#</th>
                                <th>Timestamp</th>

                    <th>Customer</th>
                    <th>Address</th>
                    <th>Personal Info</th>
                    <th>Physical Info</th>
                    <th>Medical Info</th>
                    <th>Insurance</th>
                    <th>Beneficiary</th>
                    <th>Drafts</th>
                    <th>Remarks</th>
                    <th>Closer</th>
                    <th>Junior Closer</th>
                    <th>Center</th>
                    <th>Sale By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @include('outsource-closer.table-body', ['closedCalls' => $closedCalls])
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div id="paginationLinks" class="d-flex justify-content-center">
            {{ $closedCalls->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {
        // Fetch data on dropdown change
        $('#per_page').change(function () {
            fetchData();
        });

        // Live search while typing
        $('#search').on('keyup', function () {
            fetchData();
        });

        // Function to fetch data using AJAX
        function fetchData() {
            let perPage = $('#per_page').val();
            let search = $('#search').val();

            $.ajax({
                url: '{{ route("closed_calls.index") }}',
                method: 'GET',
                data: { per_page: perPage, search: search },
                success: function (response) {
                    $('#closedCallsTable tbody').html(response.table_body);
                    $('#paginationLinks').html(response.pagination_links);
                },
                error: function () {
                    alert('Failed to fetch data. Please try again.');
                }
            });
        }
    });
</script>

@endsection

<style>
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
</style>
