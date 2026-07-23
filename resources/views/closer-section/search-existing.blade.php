@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-search me-2"></i>Search Existing Records
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="{{ route('closer.search.existing') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Enter phone number or alternate phone number..." 
                                        value="{{ $search ?? '' }}"
                                        required
                                    >
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                                <small class="text-muted">Search by phone number or alternate phone number (minimum 4 characters required)</small>
                            </div>
                        </div>
                    </form>

                    <!-- Error Messages -->
                    @if(isset($searchError) && $searchError)
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> {{ $searchError }}
                        </div>
                    @endif

                    <!-- Results Section -->
                    @if($searched)
                        @if($existingRecords->count() > 0)
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Record(s) Found!</strong> {{ $existingRecords->count() }} existing record(s) found with this phone number.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer Name</th>
                                            <th>Phone Number / Lead Id</th>
                                            <th>Alt. Phone</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Gender</th>
                                            <th>Carrier</th>
                                            <th>Coverage Plan</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($existingRecords as $record)
                                        <tr>
                                            <td>{{ $record->id }}</td>
                                            <td>
                                                <strong>{{ $record->customer_full_name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $record->phone_number ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $record->alternate_phone_number ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $record->address ?? 'N/A' }}</td>
                                            <td>{{ $record->city ?? 'N/A' }}</td>
                                            <td>{{ $record->state ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $record->gender == 'male' ? 'primary' : 'pink' }}">
                                                    {{ ucfirst($record->gender ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td>{{ $record->carrier ?? 'N/A' }}</td>
                                            <td>
                                                @if($record->coverage_plan)
                                                    ${{ number_format($record->coverage_plan) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $record->status == 'approved' ? 'success' : 
                                                    ($record->status == 'pending' ? 'warning' : 
                                                    ($record->status == 'rejected' ? 'danger' : 'secondary')) 
                                                }}">
                                                    {{ ucfirst($record->status ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td>{{ $record->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('closed-calls.show', $record->id) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>No Records Found!</strong> No existing records found with this phone number. You can proceed with creating a new record.
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <p>Enter a phone number to search for existing records</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-pink {
    background-color: #e91e63 !important;
}
</style>
@endsection