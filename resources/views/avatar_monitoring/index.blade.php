@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">{{ __('Avatar Monitoring Records') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>{{ __('Records') }}</h2>
            <a href="{{ route('avatar_monitoring.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Add New Record') }}
            </a>
        </div>

            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Monitoring Date</th>
                        <th>Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monitorings as $monitoring)
                        <tr>
                            <td>{{ $monitoring->id }}</td>
                            <td>{{ $monitoring->employee->name ?? __('N/A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($monitoring->monitor_date)->format('F d, Y') }}</td>
                            <td>
                                @php
                                    $scoreClasses = ['Good' => 'text-success', 'Avg' => 'text-info', 'Bad' => 'text-warning', 'Worst' => 'text-danger'];
                                    $scoreClass = $scoreClasses[$monitoring->score] ?? 'text-muted';
                                @endphp
                                <span class="{{ $scoreClass }}">{{ ucfirst($monitoring->score) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('avatar_monitoring.show', $monitoring->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> {{ __('View') }}
                                </a>
                                <a href="{{ route('avatar_monitoring.edit', $monitoring->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('avatar_monitoring.destroy', $monitoring->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
        <i class="bi bi-trash"></i> Delete
    </button>
</form>


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ __('No records found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        <div class="card-footer">
            {{ $monitorings->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
