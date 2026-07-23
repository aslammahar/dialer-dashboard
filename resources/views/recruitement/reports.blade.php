@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2>Recruitment Reports</h2>

    <form method="GET" action="{{ route('recruitments.reports') }}" class="mb-3">
    <div class="row">
        <div class="col-md-2">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="recruiter">Recruiter:</label>
            <select name="recruiter" id="recruiter" class="form-control">
                <option value="">All Recruiters</option>
                @foreach($allRecruiters as $recruiter)
                    <option value="{{ $recruiter }}" {{ $selectedRecruiter == $recruiter ? 'selected' : '' }}>
                        {{ $recruiter }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mt-4">

            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('recruitments.reports', ['filter' => 'weekly']) }}" class="btn btn-secondary">Weekly</a>
            <a href="{{ route('recruitments.reports', ['filter' => 'monthly']) }}" class="btn btn-secondary">Monthly</a>
            <a href="{{ route('recruitments.exportExcel', request()->query()) }}" class="btn btn-success">Export to Excel</a>

            @can('export recruitment')

            <a href="{{ route('recruitments.exportAll') }}" class="btn btn-warning">Export All</a>
  

            @endcan

            
        </div>
    </div>
</form>



    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Recruiter</th>
                <th>Total Calls</th>
                @foreach($statuses as $status)
                    <th>{{ $status }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $data)
                <tr>
                    <td>{{ $data['recruiter'] }}</td>
                    <td>{{ $data['total_calls'] }}</td>
                    @foreach($statuses as $status)
                        <td>{{ $data['statuses'][$status] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function setDateRange(type) {
    let startDate = document.getElementById('start_date');
    let endDate = document.getElementById('end_date');
    let today = new Date();
    
    if (type === 'weekly') {
        let lastWeek = new Date();
        lastWeek.setDate(today.getDate() - 7);
        startDate.value = lastWeek.toISOString().split('T')[0];
    } else if (type === 'monthly') {
        let lastMonth = new Date();
        lastMonth.setDate(today.getDate() - 30);
        startDate.value = lastMonth.toISOString().split('T')[0];
    }

    endDate.value = today.toISOString().split('T')[0];
}
</script>

@endsection
