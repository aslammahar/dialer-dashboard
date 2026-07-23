@extends('layouts.admin')

@section('page-title')
    {{__('Employee Profile - ') . ($user->userDetail->full_name ?? $user->name)}}
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            $('.update-bank-status').click(function() {
                var bankId = $(this).data('bank-id');
                var status = $(this).data('status');
                var bankName = $(this).data('bank-name');
                var button = $(this);
                
                // Show confirmation for reject action
                if (status === 'rejected') {
                    if (!confirm(`Are you sure you want to reject ${bankName}?`)) {
                        return;
                    }
                }
                
                button.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Processing...');
                
                $.ajax({
                    url: '/user-details/bank/' + bankId + '/update-status',
                    type: 'POST',
                    data: {
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.success);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred. Please try again.');
                        button.prop('disabled', false).html('<i class="ti ti-check"></i> Verify');
                    }
                });
            });
            
            function showToast(type, message) {
                const toast = document.createElement('div');
                toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }
            
            // Image modal functionality
            $('.document-image').click(function() {
                const imgSrc = $(this).attr('src');
                const imgAlt = $(this).attr('alt');
                $('#imageModal img').attr('src', imgSrc).attr('alt', imgAlt);
                $('#imageModalLabel').text(imgAlt);
                $('#imageModal').modal('show');
            });
        });
    </script>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .info-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .info-card .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem 1.5rem;
        }
        .info-table td {
            border: none;
            padding: 12px 15px;
            font-size: 14px;
        }
        .info-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .info-table tr:hover {
            background-color: #e9ecef;
        }
        .document-preview {
            cursor: pointer;
            transition: transform 0.3s ease;
            border: 3px solid #e9ecef;
            border-radius: 10px;
        }
        .document-preview:hover {
            transform: scale(1.05);
            border-color: #667eea;
        }
        .status-badge {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .action-buttons .btn {
            border-radius: 20px;
            font-size: 12px;
            padding: 6px 12px;
            margin: 2px;
        }
        .user-avatar {
            width: 120px;
            height: 120px;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .section-divider {
            border-left: 3px solid #667eea;
            padding-left: 20px;
            margin-left: 10px;
        }
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('hr.user.details.index')}}">{{__('Employee Directory')}}</a></li>
    <li class="breadcrumb-item active">{{__('Profile View')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <div class="btn-group">
            <a href="{{route('hr.user.details.index')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-arrow-left me-1"></i> {{__('Back to List')}}
            </a>
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <i class="ti ti-chevron-down"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Export Profile</a>
                <a class="dropdown-item" href="#"><i class="ti ti-printer me-2"></i>Print Profile</a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        @if($user->userDetail)
            <!-- Profile Header -->
            <div class="card info-card mb-4">
                <div class="profile-header">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="user-avatar bg-white rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-user" style="font-size: 3rem; color: #667eea;"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h2 class="mb-1">{{ $user->userDetail->full_name }}</h2>
                                <p class="mb-1 opacity-75">
                                    <i class="ti ti-briefcase me-1"></i>{{ $user->userDetail->designation }}
                                </p>
                                <p class="mb-0 opacity-75">
                                    <i class="ti ti-id me-1"></i>Employee ID: {{ $user->userDetail->employee_id }}
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="badge bg-success fs-6">
                                    <i class="ti ti-circle-check me-1"></i>Profile Complete
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Personal Information -->
                <div class="col-xl-6">
                    <div class="card info-card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="ti ti-user-circle me-2"></i>
                            <h5 class="mb-0">{{__('Personal Information')}}</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table info-table mb-0">
                                <tr>
                                    <td width="35%"><strong><i class="ti ti-user me-1 text-primary"></i>Full Name</strong></td>
                                    <td>{{ $user->userDetail->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-user-plus me-1 text-primary"></i>Father Name</strong></td>
                                    <td>{{ $user->userDetail->father_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-at me-1 text-primary"></i>Pseudo Name</strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $user->userDetail->pseudo_name ?? 'Not Set' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-phone me-1 text-primary"></i>Phone</strong></td>
                                    <td>
                                        <a href="tel:{{ $user->userDetail->phone }}" class="text-decoration-none">
                                            {{ $user->userDetail->phone }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-mail me-1 text-primary"></i>Email</strong></td>
                                    <td>
                                        <a href="mailto:{{ $user->userDetail->email }}" class="text-decoration-none">
                                            {{ $user->userDetail->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-id-badge me-1 text-primary"></i>CNIC Number</strong></td>
                                    <td>{{ $user->userDetail->cnic_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-xl-6">
                    <div class="card info-card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="ti ti-info-circle me-2"></i>
                            <h5 class="mb-0">{{__('Additional Information')}}</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table info-table mb-0">
                                <tr>
                                    <td width="35%"><strong><i class="ti ti-cake me-1 text-success"></i>Date of Birth</strong></td>
                                    <td>{{ $user->userDetail->date_of_birth->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-phone-call me-1 text-success"></i>Emergency Phone</strong></td>
                                    <td>
                                        <a href="tel:{{ $user->userDetail->emergency_phone }}" class="text-decoration-none">
                                            {{ $user->userDetail->emergency_phone }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-map-pin me-1 text-success"></i>City</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->userDetail->city }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="ti ti-home me-1 text-success"></i>Address</strong></td>
                                    <td>{{ $user->userDetail->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Information -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card info-card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="ti ti-briefcase me-2"></i>
                            <h5 class="mb-0">{{__('Employment Information')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 section-divider">
                                    <table class="table info-table mb-0">
                                        <tr>
                                            <td width="40%"><strong><i class="ti ti-id me-1 text-warning"></i>Employee ID</strong></td>
                                            <td>
                                                <span class="badge bg-warning text-dark fs-6">
                                                    {{ $user->userDetail->employee_id }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="ti ti-users me-1 text-warning"></i>Team Leader</strong></td>
                                            <td>{{ $user->userDetail->team_leader ?? 'Not Assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="ti ti-star me-1 text-warning"></i>Designation</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ $user->userDetail->designation }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table info-table mb-0">
                                        <tr>
                                            <td width="40%"><strong><i class="ti ti-building me-1 text-warning"></i>Work From</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $user->userDetail->work_from == 'Office' ? 'primary' : ($user->userDetail->work_from == 'Home' ? 'success' : 'info') }}">
                                                    {{ $user->userDetail->work_from }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="ti ti-calendar-event me-1 text-warning"></i>Date of Joining</strong></td>
                                            <td>{{ $user->userDetail->date_of_joining->format('d M, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="ti ti-source-code me-1 text-warning"></i>Source of Joining</strong></td>
                                            <td>{{ $user->userDetail->source_of_joining ?? 'Not Specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CNIC Documents -->
            <div class="card info-card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="ti ti-id-badge me-2"></i>
                    <h5 class="mb-0">{{__('Identity Documents')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center p-3">
                                <h6 class="mb-3 text-primary">
                                    <i class="ti ti-id me-1"></i>CNIC Front
                                </h6>
                                @if($user->userDetail && $user->userDetail->cnicFront)
                                    <img src="{{ route('attachments.show', $user->userDetail->cnicFront->id) }}" 
                                        alt="CNIC Front" 
                                        class="img-fluid rounded shadow document-preview document-image"
                                        style="max-height: 250px; width: 100%; object-fit: cover;">
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="ti ti-calendar me-1"></i>
                                            Uploaded: {{ $user->userDetail->cnicFront->created_at->format('d M Y, H:i') }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="ti ti-file me-1"></i>
                                            Size: {{ $user->userDetail->cnicFront->file_size_human ?? 'N/A' }}
                                        </small>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="ti ti-image-off"></i>
                                        <p class="mb-2">Document Not Uploaded</p>
                                        <small class="text-muted">Employee has not uploaded CNIC front image</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center p-3">
                                <h6 class="mb-3 text-primary">
                                    <i class="ti ti-id me-1"></i>CNIC Back
                                </h6>
                                @if($user->userDetail && $user->userDetail->cnicBack)
                                    <img src="{{ route('attachments.show', $user->userDetail->cnicBack->id) }}" 
                                        alt="CNIC Back" 
                                        class="img-fluid rounded shadow document-preview document-image"
                                        style="max-height: 250px; width: 100%; object-fit: cover;">
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="ti ti-calendar me-1"></i>
                                            Uploaded: {{ $user->userDetail->cnicBack->created_at->format('d M Y, H:i') }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="ti ti-file me-1"></i>
                                            Size: {{ $user->userDetail->cnicBack->file_size_human ?? 'N/A' }}
                                        </small>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="ti ti-image-off"></i>
                                        <p class="mb-2">Document Not Uploaded</p>
                                        <small class="text-muted">Employee has not uploaded CNIC back image</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="card info-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="ti ti-building-bank me-2"></i>
                        <h5 class="mb-0">{{__('Bank Account Details')}}</h5>
                    </div>
                    <span class="badge bg-primary">
                        {{ $user->bankDetails->count() }} Account(s)
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Priority</th>
                                    <th>Bank Name</th>
                                    <th>Account Title</th>
                                    <th>Account Number</th>
                                    <th>CNIC Number</th>
                                    <th width="120">Status</th>
                                    <th width="200" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->bankDetails as $bank)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary rounded-circle p-2">
                                                {{ $bank->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $bank->bank_name }}</strong>
                                        </td>
                                        <td>{{ $bank->account_title }}</td>
                                        <td>
                                            <code>{{ $bank->account_number }}</code>
                                        </td>
                                        <td>{{ $bank->cnic_number }}</td>
                                        <td>
                                            @if($bank->status == 'verified')
                                                <span class="status-badge bg-success">
                                                    <i class="ti ti-check me-1"></i>Verified
                                                </span>
                                            @elseif($bank->status == 'rejected')
                                                <span class="status-badge bg-danger">
                                                    <i class="ti ti-x me-1"></i>Rejected
                                                </span>
                                            @else
                                                <span class="status-badge bg-warning text-dark">
                                                    <i class="ti ti-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="action-buttons text-center">
                                            @if($bank->status != 'verified')
                                                <button class="btn btn-sm btn-success update-bank-status" 
                                                        data-bank-id="{{ $bank->id }}" 
                                                        data-status="verified"
                                                        data-bank-name="{{ $bank->bank_name }}">
                                                    <i class="ti ti-check me-1"></i>Verify
                                                </button>
                                            @endif
                                            @if($bank->status != 'rejected')
                                                <button class="btn btn-sm btn-danger update-bank-status" 
                                                        data-bank-id="{{ $bank->id }}" 
                                                        data-status="rejected"
                                                        data-bank-name="{{ $bank->bank_name }}">
                                                    <i class="ti ti-x me-1"></i>Reject
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="ti ti-building-bank"></i>
                                                <p class="mb-2">No Bank Accounts Found</p>
                                                <small class="text-muted">Employee has not added any bank accounts</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="card info-card">
                <div class="card-body">
                    <div class="empty-state py-5">
                        <i class="ti ti-user-x" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Profile Incomplete</h3>
                        <p class="text-muted mb-4">This employee has not completed their profile details yet.</p>
                        <button class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Send Reminder
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="" class="img-fluid rounded" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>
@endsection