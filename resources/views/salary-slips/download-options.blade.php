@extends('layouts.admin')

@section('title', 'Download Salary Slips')

@section('content')
<div class="mb-4">
    <h2>Download Salary Slips</h2>
</div>

<div class="row">
    <!-- Download All -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-download fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Download All</h5>
                <p class="card-text">Download all salary slips for selected month in a ZIP file</p>
                <form action="{{ route('salary-slips.download-bulk') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="year" class="form-select" required>
                            @foreach(range(date('Y'), date('Y') - 5) as $y)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="month" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-archive"></i> Download ZIP
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Download by Department -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-building fa-3x text-success mb-3"></i>
                <h5 class="card-title">Download by Department</h5>
                <p class="card-text">Download salary slips for a specific department</p>
                <form action="{{ route('salary-slips.download-department', 0) }}" method="GET" id="deptDownloadForm">
                    <div class="mb-3">
                        <select name="year" class="form-select" required>
                            @foreach(range(date('Y'), date('Y') - 5) as $y)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="month" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="department_id" id="department_select" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach(\App\Models\Department::all() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-archive"></i> Download
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Download Individual -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user fa-3x text-info mb-3"></i>
                <h5 class="card-title">Individual Slips</h5>
                <p class="card-text">View and download individual employee salary slips</p>
                <a href="{{ route('monthly-salaries.index') }}" class="btn btn-info">
                    <i class="fas fa-list"></i> View List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#deptDownloadForm').submit(function(e) {
        e.preventDefault();
        let deptId = $('#department_select').val();
        if (!deptId) {
            alert('Please select a department');
            return;
        }
        
        let action = $(this).attr('action').replace('/0', '/' + deptId);
        $(this).attr('action', action);
        this.submit();
    });
});
</script>
@endpush