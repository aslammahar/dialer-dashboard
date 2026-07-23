@extends('layouts.admin')

@section('title', 'Closed Calls Export')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">Closed Calls Export</h3>
                        <small class="text-muted">Export closed_calls data to Excel with custom filters.</small>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reporting.closed-calls.export.run') }}">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Statuses to include</label>
                                <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                    @foreach($availableStatuses as $status)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="statuses[]"
                                                id="status_{{ $status }}"
                                                value="{{ $status }}"
                                                {{ in_array($status, old('statuses', $statusesCheckedByDefault)) ? 'checked' : '' }}>
                                            <label class="form-check-label text-capitalize" for="status_{{ $status }}">
                                                {{ str_replace('_', ' ', $status) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted d-block mt-1">Policy statuses filter <code>status</code>; <strong>Scheduled Call Back</strong> filters <code>agent_status</code>. If you mix them, rows matching <em>any</em> checked option are included (union).</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Centers to include</label>
                                <div class="border rounded p-2 mb-2">
                                    @foreach($availableCenters as $center)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="centers[]"
                                                   id="center_{{ $center }}"
                                                   value="{{ $center }}"
                                                   {{ in_array($center, old('centers', $availableCenters)) ? 'checked' : '' }}>
                                            <label class="form-check-label text-uppercase" for="center_{{ $center }}">
                                                {{ $center }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted d-block">Uncheck a center to exclude its rows from the export.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Columns</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="include_center" id="include_center"
                                        value="1" {{ old('include_center', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="include_center">
                                        Include <strong>Center Name</strong> column
                                    </label>
                                </div>
                                <small class="text-muted d-block">Other columns (lead, customer, banking, etc.) are always included; Center Name follows the checkbox above.</small>
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label fw-semibold">Start date</label>
                                <input type="date" name="start_date" id="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror" required
                                       value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label fw-semibold">End date</label>
                                <input type="date" name="end_date" id="end_date"
                                       class="form-control @error('end_date') is-invalid @enderror" required
                                       value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Export records by <code>created_at</code> in this range.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('reporting.index') }}" class="btn btn-light">
                                <i class="ti ti-arrow-left"></i> Back to Reporting
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-file-spreadsheet"></i> Export Closed Calls Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

