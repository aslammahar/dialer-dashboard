@extends('layouts.admin')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
@endsection

@section('content')

<style>
    table.table th, table.table td {
        word-wrap: break-word;
        white-space: normal;
        overflow-wrap: break-word;
        max-width: 200px;
        text-overflow: ellipsis;
    }

    table.table {
        table-layout: fixed;
        width: 100%;
    }

    .export-btn {
        margin-left: 10px;
    }

    .navigation-buttons {
        padding: 10px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>

<div class="container-fluid mt-4">
    <div class="card shadow" id="avatarMonitoringCard">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h2 class="mb-0 me-3">
                    <i class="bi bi-clipboard-data me-2"></i>
                    {{ __('Avatar Monitoring Record Details') }}
                </h2>
                <button id="exportPngBtn" class="btn btn-success btn-sm export-btn">
                    <i class="bi bi-file-earmark-image me-1"></i>{{ __('Export to PNG') }}
                </button>
            </div>
            <a href="{{ route('avatar_monitoring.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to List') }}
            </a>
        </div>

        <!-- Navigation Buttons -->
        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
    @if ($previous)
        <a href="{{ route('avatar_monitoring.show', $previous->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-chevron-left me-1"></i> {{ __('Previous') }}
        </a>
    @else
        <button class="btn btn-sm btn-secondary" disabled>
            <i class="bi bi-chevron-left me-1"></i> {{ __('Previous') }}
        </button>
    @endif

    @if ($next)
        <a href="{{ route('avatar_monitoring.show', $next->id) }}" class="btn btn-sm btn-secondary">
            {{ __('Next') }}<i class="bi bi-chevron-right ms-1"></i>
        </a>
    @else
        <button class="btn btn-sm btn-secondary" disabled>
            {{ __('Next') }}<i class="bi bi-chevron-right ms-1"></i>
        </button>
    @endif
</div>



        <!-- Monitoring Details Table -->
        <div class="p-4">
            <table class="table table-bordered">
                <tr>
                    <th><i class="bi bi-person-badge me-2"></i>{{ __('Employee Name') }}</th>
                    <td>
                        {{ $avatarMonitoring->employee->name ?? __('N/A') }}
                    </td>
                    <th><i class="bi bi-person-fill me-2"></i>{{ __('Filled By') }}</th>
                    <td>
                        {{ $avatarMonitoring->filledBy->name ?? __('N/A') }}
                    </td>
                </tr>
                <tr>
                    <th><i class="bi bi-clock me-2"></i>{{ __('Monitoring Time') }}</th>
                    <td>
                        {{ $avatarMonitoring->monitor_from ?? __('N/A') }} - {{ $avatarMonitoring->monitor_to ?? __('N/A') }}
                    </td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Monitoring Date') }}</th>
                    <td>
                        {{ \Carbon\Carbon::parse($avatarMonitoring->monitor_date ?? now())->format('F d, Y') }}
                    </td>
                </tr>
                <tr>
                    <th><i class="bi bi-chat-left-dots me-2"></i>{{ __('Greeting') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->greeting ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-headset me-2"></i>{{ __('Response on Answering Machine') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->response_on_answering_machine ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-speedometer2 me-2"></i>{{ __('Response Time') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->response_time ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-people me-2"></i>{{ __('Customer Response') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->customer_response ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-person-badge-fill me-2"></i>{{ __('Leave 3 Way') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->leave_3_way ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-question-circle me-2"></i>{{ __('Questions') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->questions ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-info-circle me-2"></i>{{ __('Dispositions') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->dispositions ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-chat-right-dots me-2"></i>{{ __('Comments & Suggestions') }}</th>
                    <td colspan="3">{{ $avatarMonitoring->comments ?? __('N/A') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small>
                <i class="bi bi-clock-history me-1"></i>
                {{ __('Last Updated:') }} {{ $avatarMonitoring->updated_at->diffForHumans() }}
            </small>
            <div class="action-buttons">
                @can('update avatar_monitoring')
                    <a href="{{ route('avatar_monitoring.edit', $avatarMonitoring->id) }}" class="btn btn-warning btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i> {{ __('Edit') }}
                    </a>
                @endcan

                @can('delete avatar_monitoring')
                    <form action="{{ route('avatar_monitoring.destroy', $avatarMonitoring->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure?') }}')">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportPngBtn');

    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const cardElement = document.getElementById('avatarMonitoringCard');

            // Disable button during export
            exportBtn.disabled = true;
            exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';

            domtoimage.toPng(cardElement, { 
                quality: 1,
                width: cardElement.scrollWidth,
                height: cardElement.scrollHeight
            })
            .then(function (dataUrl) {
                const link = document.createElement('a');
                link.download = 'avatar-monitoring-record-' + new Date().toISOString().slice(0,10) + '.png';
                link.href = dataUrl;
                link.click();

                exportBtn.disabled = false;
                exportBtn.innerHTML = '<i class="bi bi-file-earmark-image me-1"></i>{{ __('Export to PNG') }}';
            })
            .catch(function (error) {
                console.error('Export failed:', error);
                alert('{{ __('Failed to export image. Please try again.') }}');
                
                exportBtn.disabled = false;
                exportBtn.innerHTML = '<i class="bi bi-file-earmark-image me-1"></i>{{ __('Export to PNG') }}';
            });
        });
    }
});
</script>
@endsection
