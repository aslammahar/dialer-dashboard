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
    <div class="card shadow" id="monitoringCard">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h2 class="mb-0 me-3">
                    <i class="bi bi-clipboard-data me-2"></i>
                    {{ __('Monitoring Record Details') }}
                </h2>
                <button id="exportPngBtn" class="btn btn-success btn-sm export-btn">
                    <i class="bi bi-file-earmark-image me-1"></i>{{ __('Export to PNG') }}
                </button>
            </div>
            <a href="{{ route('monitoring.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to List') }}
            </a>
        </div>

        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
            <!-- Previous Button -->
            @if ($previous)
                <a href="{{ route('monitoring.show', $previous->id) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-chevron-left me-1"></i>{{ __('Previous') }}
                </a>
            @else
                <button class="btn btn-sm btn-secondary" disabled>
                    <i class="bi bi-chevron-left me-1"></i>{{ __('Previous') }}
                </button>
            @endif

            <!-- Next Button -->
            @if ($next)
                <a href="{{ route('monitoring.show', $next->id) }}" class="btn btn-sm btn-secondary">
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
                    <th><i class="bi bi-person-badge me-2"></i>{{ __('Monitered By') }}</th>
                    <td>
                        <div class="d-flex justify-content-between align-items-center">
                            {{ $monitoring->filledBy->name ?? __('N/A') }}
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <th><i class="bi bi-person-badge me-2"></i>{{ __('Employee Name') }}</th>
                    <td>
                        <div class="d-flex justify-content-between align-items-center">
                            {{ $monitoring->employee->name ?? __('N/A') }}
                        </div>
                    </td>
                    <th><i class="bi bi-clock me-2"></i>{{ __('Monitoring Time') }}</th>
                    <td>{{ $monitoring->monitor_from ?? __('N/A') }} - {{ $monitoring->monitor_to ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Monitoring Date') }}</th>
                    <td>{{ \Carbon\Carbon::parse($monitoring->monitor_date ?? now())->format('F d, Y') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Agent\'s Efforts') }}</th>
                    <td>{{ $monitoring->agents_efforts ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Call Rapport Building') }}</th>
                    <td>{{ $monitoring->call_rapport_building ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Qualifying Part') }}</th>
                    <td>{{ $monitoring->qualifying_part ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Rebuttals') }}</th>
                    <td>{{ $monitoring->rebuttals ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Overall Call Details') }}</th>
                    <td>{{ $monitoring->overall_call_details ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Vocabulary') }}</th>
                    <td>{{ $monitoring->vocabulary ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Customer Response') }}</th>
                    <td>{{ $monitoring->customer_response ?? __('N/A') }}</td>
                </tr>

                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Greeting') }}</th>
                    <td>{{ $monitoring->greeting ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Energy') }}</th>
                    <td>{{ $monitoring->energy ?? __('N/A') }}</td>
                </tr>


                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Suggestions') }}</th>
                    <td >{{ $monitoring->suggestions ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('QA') }}</th>
                    <td>{{ $monitoring->qa ?? __('N/A') }}</td>
                </tr>

                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Confidence') }}</th>
                    <td >{{ $monitoring->confidence ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Motivation') }}</th>
                    <td>{{ $monitoring->motivation ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Energy Level') }}</th>
                    <td >{{ $monitoring->energy_level ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Smile') }}</th>
                    <td>{{ $monitoring->smile ?? __('N/A') }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Focus') }}</th>
                    <td >{{ $monitoring->focus ?? __('N/A') }}</td>
                    <th><i class="bi bi-calendar-date me-2"></i>{{ __('Positivity') }}</th>
                    <td>{{ $monitoring->positivity ?? __('N/A') }}</td>
                </tr>
                @php
                    $scoreDetails = [
                        'good' => ['icon' => 'check-circle-fill', 'class' => 'bg-success'],
                        'avg' => ['icon' => 'dash-circle-fill', 'class' => 'bg-info'],
                        'bad' => ['icon' => 'x-circle-fill', 'class' => 'bg-warning'],
                        'worst' => ['icon' => 'exclamation-circle-fill', 'class' => 'bg-danger']
                    ];
                    $score = strtolower($monitoring->score ?? 'unknown');
                    $details = $scoreDetails[$score] ?? ['icon' => 'question-circle-fill', 'class' => 'bg-secondary'];
                @endphp
                <tr>
                    <th colspan="4" class="text-center">
                        <span class="badge {{ $details['class'] }} fs-6">
                            <i class="bi bi-{{ $details['icon'] }} me-2"></i>
                            {{ ucfirst($score) }} {{ __('Score') }}
                        </span>
                    </th>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small>
                <i class="bi bi-clock-history me-1"></i>
                {{ __('Last Updated:') }} {{ $monitoring->updated_at->diffForHumans() }}
            </small>
            <div class="action-buttons">
                @can('update monitoring')
                    <a href="{{ route('monitoring.edit', $monitoring->id) }}" class="btn btn-warning btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i> {{ __('Edit') }}
                    </a>
                @endcan
               
                @can('delete monitoring')
                    <form action="{{ route('monitoring.destroy', $monitoring->id) }}" method="POST" class="d-inline">
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
            const cardElement = document.getElementById('monitoringCard');

            // Disable button during export
            exportBtn.disabled = true;
            exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';

            domtoimage.toPng(cardElement, { 
                quality: 1,
                width: cardElement.scrollWidth,
                height: cardElement.scrollHeight
            })
            .then(function (dataUrl) {
                // Create download link
                const link = document.createElement('a');
                link.download = 'monitoring-record-' + new Date().toISOString().slice(0,10) + '.png';
                link.href = dataUrl;
                link.click();

                // Reset button
                exportBtn.disabled = false;
                exportBtn.innerHTML = '<i class="bi bi-file-earmark-image me-1"></i>{{ __('Export to PNG') }}';
            })
            .catch(function (error) {
                console.error('Export failed:', error);
                alert('{{ __('Failed to export image. Please try again.') }}');
                
                // Reset button
                exportBtn.disabled = false;
                exportBtn.innerHTML = '<i class="bi bi-file-earmark-image me-1"></i>{{ __('Export to PNG') }}';
            });
        });
    }
});
</script>
@endsection