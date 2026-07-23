<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
        </script>
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <style>
        /* Add custom CSS to create horizontal scroll for the table */
        .table-container {
            overflow-x: auto;
        }
    </style>

    <style>
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination-container button {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: pointer;
        }

        .pagination-container button:disabled {
            cursor: not-allowed;
        }
    </style>

</head>

<body>

    @extends('layouts.admin')

    @section('page-title')
        <!-- <a href="daily-leads"> <u> Voice Leads </u> </a><br> -->









        {{ __('Manage Avatar Leads') }}
    @endsection

    @section('content')

        <!-- i am including another form here so add boxes for that -->

        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-auto mb-/3 mb-sm-0">
                                <div class="d-flex align-items-center">
                                    <div class="theme-avtar bg-info">
                                        <i class="ti ti-cast"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">{{ __('Assign Leads') }} </small>
                                        <li><a href="avatar-q-a-leads" class="btn btn-sm btn-info">Assign Leads</a></li>
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


        <div class="row">
            <div class="col-md-6">
                <h3>Filter Data</h3>
                <form action="{{ route('avatarleads') }}" method="get">
                    <div class="form-group">
                        <label for="dialer_id">Dialer ID (optional):</label>
                        <input type="text" id="dialer_id" name="dialer_id" class="form-control"
                            value="{{ $dialerId ?? '' }}" placeholder="e.g. EMP12345">
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>

        </div>





        <form action="{{ route('export.avatar.leads') }}" method="post" id="exportLeadsForm">
            @csrf

            <div style="text-align: right; margin-top: 10px;">
                <button type="button" class="btn btn-info" onclick="exportSelectedLeads()">Export Selected Leads</button>
                @if ($startDate && $endDate)
                    <button type="button" class="btn btn-success" onclick="exportAllLeads()">Export All
                        ({{ $avatarLeads->total() ?? 0 }} records)</button>
                @endif
            </div>

            <input type="hidden" name="selected_leads" id="selectedLeadsInput">
            <input type="hidden" name="export_all" id="exportAllInput" value="0">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <input type="hidden" name="dialer_id" value="{{ $dialerId ?? '' }}">
            <label for="selectAllCheckbox" style="font-size: 20px;">
                <input type="checkbox" id="selectAllCheckbox" style="transform: scale(1.5);"> Select All
            </label>



            @if ($startDate && $endDate)
                <div class="table-container">

                    <div class="input-group rounded">
                        <input type="search" class="form-control rounded" id="searchInput" placeholder="Search"
                            aria-label="Search" aria-describedby="search-addon" />
                    </div>


                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lead ID</th>
                                <th>Dialer ID</th>
                                <th>Agent Name</th>
                                <th>Team Name</th>
                                <th>Closer Name</th>
                                <th>Recording Link</th>
                                <th>Entry Time</th>
                                <th>QA Date</th>
                                <th>Qa Person</th>
                                <th>Qa Comments</th>
                                <th>Qa Status</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($avatarLeads as $avatarLead)
                                <tr>
                                    <td>{{ $avatarLead->id }}</td>
                                    <td>{{ $avatarLead->lead_id }}</td>
                                    <td>{{ $avatarLead->dialer_id }}</td>
                                    <td>{{ $avatarLead->agent_name }}</td>
                                    <td>{{ $avatarLead->team_name }}</td>
                                    <td>{{ $avatarLead->closer_name }}</td>
                                    <td>{{ $avatarLead->recording_link }}</td>
                                    <td>{{ $avatarLead->created_at }}</td>
                                    <td>{{ $avatarLead->Qadate }}</td>
                                    <td>{{ $avatarLead->qa_person_name }}</td>
                                    <td>{{ $avatarLead->Qacomments }}</td>
                                    <td>{{ $avatarLead->QAstatus }}</td>
                                    <td>
                                        <input type="checkbox" name="selected_leads[]" value="{{ $avatarLead->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    @if(isset($avatarLeads) && method_exists($avatarLeads, 'links'))
                        <div class="pagination-container mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    Showing {{ $avatarLeads->firstItem() ?? 0 }} to {{ $avatarLeads->lastItem() ?? 0 }}
                                    of {{ $avatarLeads->total() ?? 0 }} records
                                </div>
                                <div>
                                    {{ $avatarLeads->links() }}
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endif





        </form>

        <script>
            function exportSelectedLeads() {
                var selectedLeads = [];
                var checkboxes = document.querySelectorAll('input[name="selected_leads[]"]:checked');

                checkboxes.forEach(function (checkbox) {
                    selectedLeads.push(checkbox.value);
                });

                if (selectedLeads.length > 0) {
                    var form = document.getElementById('exportLeadsForm');
                    var input = document.getElementById('selectedLeadsInput');
                    var exportAllInput = document.getElementById('exportAllInput');
                    input.value = selectedLeads.join(',');
                    exportAllInput.value = '0';
                    form.submit();
                } else {
                    alert('Please select at least one lead to export.');
                }
            }

            function exportAllLeads() {
                var form = document.getElementById('exportLeadsForm');
                var exportAllInput = document.getElementById('exportAllInput');
                var startDate = '{{ $startDate }}';
                var endDate = '{{ $endDate }}';

                if (!startDate || !endDate) {
                    alert('Please select a date range first.');
                    return;
                }

                if (confirm('This will export ALL {{ $avatarLeads->total() ?? 0 }} records matching your date filter. Continue?')) {
                    // Uncheck all checkboxes so they don't get submitted and hit PHP max_input_vars=1000 limit
                    document.querySelectorAll('input[name="selected_leads[]"]').forEach(function (cb) {
                        cb.checked = false;
                    });
                    exportAllInput.value = '1';
                    form.submit();
                }
            }
        </script>
        <script>
            function selectAllLeads() {
                var checkboxes = document.querySelectorAll('input[name="selected_leads[]"]');
                var selectAllCheckbox = document.getElementById('selectAllCheckbox');

                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            }

            document.getElementById('selectAllCheckbox').addEventListener('change', selectAllLeads);
        </script>


        <script>
            // Function to perform the search
            function performSearch() {
                const searchInput = document.getElementById('searchInput').value.toLowerCase();
                const tableRows = document.querySelectorAll('.table tbody tr');

                tableRows.forEach((row) => {
                    const rowData = row.textContent.toLowerCase();
                    if (rowData.includes(searchInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Attach an event listener to the search input
            document.getElementById('searchInput').addEventListener('input', performSearch);
        </script>





    @endsection
</body>



</html>