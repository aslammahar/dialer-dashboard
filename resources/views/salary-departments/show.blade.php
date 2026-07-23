@extends('layouts.admin')

@section('title', 'Department Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Department Details</h2>
    <div>
        <a href="{{ route('salary-departments.edit', $salaryDepartment) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('salary-departments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Department Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Name:</th>
                        <td>{{ $salaryDepartment->name }}</td>
                    </tr>
                    <tr>
                        <th>Role Type:</th>
                        <td><span class="badge bg-info">{{ $salaryDepartment->role_type }}</span></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($salaryDepartment->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Total Users:</th>
                        <td>{{ $salaryDepartment->users->count() }}</td>
                    </tr>
                    <tr>
                        <th>Created By:</th>
                        <td>{{ $salaryDepartment->creator->name }}</td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td>{{ $salaryDepartment->created_at->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $salaryDepartment->description ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Department Users ({{ $salaryDepartment->users->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Salary Setup</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salaryDepartment->users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->userDetail->full_name ?? $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                  
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No users assigned</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection