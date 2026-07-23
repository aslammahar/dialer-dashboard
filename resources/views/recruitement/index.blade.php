@extends('layouts.admin')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Recruitment Records</h2>

    <!-- Filters -->
    <div class="filters mb-4">
        <div class="row">
            <!-- Dropdown for Status -->
            <div class="col-md-3">
                <label for="statusFilter">Filter by Status:</label>
                <select id="statusFilter" class="form-control">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Hired">Hired</option>
                    <option value="Rejected">Rejected</option>
                    <option value="No Answer">No Answer</option>
                    <option value="Not Interested">Not Interested</option>
                    <option value="Call Back">Call Back</option>
                    <option value="Interested">Interested</option>
                    <option value="Left">Left</option>
                </select>
            </div>
            <!-- Input for Name -->
            <div class="col-md-3">
                <label for="nameFilter">Search by Name:</label>
                <input type="text" id="nameFilter" class="form-control" placeholder="Enter name">
            </div>
            <!-- Input for Form No -->
            <div class="col-md-3">
                <label for="formFilter">Search by Form No:</label>
                <input type="text" id="formFilter" class="form-control" placeholder="Enter form number">
            </div>
            <!-- Dropdown for Records Per Page -->
            <div class="col-md-3">
                <label for="recordsPerPage">Records Per Page:</label>
                <select id="recordsPerPage" class="form-control">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="75">75</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Wrapper -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Form No</th>
                    <th>PIN</th>
                    <th>Interview Date</th>
                    <th>Name</th>
                    <th>Contact No</th>
                    <th>Source</th>
                    <th>Designation</th>
                    <th>Work From</th>
                    <th>Interviewer</th>
                    <th>Status</th>
                    <th>Scheduled Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="recruitmentTableBody">
                @forelse ($recruitments as $recruitment)
                    <tr>
                        <td>{{ $recruitment->formid }}</td>
                        <td>{{ $recruitment->pin }}</td>
                        <td>{{ $recruitment->created_at }}</td>
                        <td>{{ $recruitment->name }}</td>
                        <td>{{ $recruitment->contact_no }}</td>
                        <td>{{ $recruitment->source }}</td>
                        <td>{{ $recruitment->designation }}</td>
                        <td>{{ $recruitment->work_from }}</td>
                        <td>{{ $recruitment->interview_taken_by }}</td>
                        <td>{{ $recruitment->status }}</td>
                        <td>{{ $recruitment->date }}</td>
                        <td>
                            <a href="{{ route('recruitments.show', $recruitment->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('recruitments.final', $recruitment->id) }}" class="btn btn-warning btn-sm">Interview</a>
                            <form action="{{ route('recruitments.destroy', $recruitment->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links -->
    <div class="d-flex justify-content-center">
        {{ $recruitments->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Include JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('statusFilter');
        const nameFilter = document.getElementById('nameFilter');
        const formFilter = document.getElementById('formFilter');
        const recordsPerPage = document.getElementById('recordsPerPage');

        const fetchData = () => {
            const status = statusFilter.value;
            const name = nameFilter.value;
            const formNo = formFilter.value;
            const perPage = recordsPerPage.value;

            fetch(`/recruitments/filter?status=${status}&name=${name}&formNo=${formNo}&perPage=${perPage}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('recruitmentTableBody');
                    tableBody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(record => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${record.formid}</td>
                                    <td>${record.pin}</td>
                                    <td>${record.created_at}</td>
                                    <td>${record.name}</td>
                                    <td>${record.contact_no}</td>
                                    <td>${record.source}</td>
                                    <td>${record.designation}</td>
                                    <td>${record.work_from}</td>
                                    <td>${record.interview_taken_by}</td>
                                    <td>${record.status}</td>
                                    <td>${record.date}</td>
                                    <td>
                                        <a href="/recruitments/${record.id}" class="btn btn-info btn-sm">View</a>
                                        <a href="/recruitments/${record.id}/final" class="btn btn-warning btn-sm">Interview</a>
                                        <form action="/recruitments/${record.id}" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="10" class="text-center">No records found</td></tr>';
                    }
                });
        };

        statusFilter.addEventListener('change', fetchData);
        nameFilter.addEventListener('input', fetchData);
        formFilter.addEventListener('input', fetchData);
        recordsPerPage.addEventListener('change', fetchData);
    });
</script>
@endsection
