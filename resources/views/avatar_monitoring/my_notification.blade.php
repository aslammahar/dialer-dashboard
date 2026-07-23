@extends('layouts.admin')

@section('head')
    <!-- Explicitly link Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')

<style>
    .btn {
        margin: 5px;
    }
</style>

<div class="container mt-5">
    <h1 class="text-center mb-4">{{ __('My Avatar Monitoring Records') }}</h1>

    <!-- Check if there are any notifications -->
    <table class="table table-bordered table-hover">
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
            @forelse ($notifications as $notification)
                <tr>
                    <td>{{ $notification->id }}</td>
                    <td>{{ $notification->employee->name ?? __('N/A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($notification->monitor_date)->format('F d, Y') }}</td>
                    <td>
                        @php
                            $scoreDetails = [
                                'good' => ['icon' => 'check-circle-fill', 'class' => 'text-success'],
                                'avg' => ['icon' => 'dash-circle-fill', 'class' => 'text-primary'],
                                'bad' => ['icon' => 'x-circle-fill', 'class' => 'text-warning'],
                                'worst' => ['icon' => 'exclamation-circle-fill', 'class' => 'text-danger']
                            ];
                            $score = strtolower($notification->score);
                            $details = $scoreDetails[$score] ?? ['icon' => 'question-circle-fill', 'class' => 'text-secondary'];
                        @endphp
                        <span class="{{ $details['class'] }}">
                            <i class="bi bi-{{ $details['icon'] }} me-2"></i>
                            {{ ucfirst($score) }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('avatar_monitoring.show', $notification->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> {{ __('View') }}
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('No new monitoring records for you.') }}
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $notifications->links('pagination::bootstrap-4') }}
    </div>
</div>

@endsection
