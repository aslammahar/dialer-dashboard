@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Departments</h2>
    <a href="{{ route('salary-departments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Department
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        <th>Role Type</th>
                        <th>Total Users</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $department->name }}</td>
                            <td><span class="badge bg-info">{{ $department->role_type }}</span></td>
                            <td>{{ $department->users->count() }}</td>
                            <td>
                                @if($department->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $department->creator->name }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('salary-departments.show', $department) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('salary-departments.edit', $department) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('salary-departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No departments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $departments->links() }}
        </div>
    </div>
</div>
@endsection