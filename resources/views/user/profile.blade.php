@extends('layouts.admin')

@section('page-title')
    {{__('User Details')}}
@endsection

@push('script-page')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Store banks data in JavaScript
            var banksData = @json($banks);

            // Open modal
            $('#openUserDetailsModal, #openUserDetailsModalEmpty').click(function() {
                $('#userDetailsModal').modal('show');
            });

            // Add bank row
            $('#addBankRow').click(function() {
                var bankRows = $('.bank-row').length;
                if (bankRows < 5) {
                    // Generate bank options from database
                    var bankOptions = '<option value="">Select Bank/Account</option>';
                    
                    Object.keys(banksData).forEach(function(category) {
                        bankOptions += '<optgroup label="' + category + '">';
                        banksData[category].forEach(function(bank) {
                            bankOptions += '<option value="' + bank.name + '">' + bank.name + '</option>';
                        });
                        bankOptions += '</optgroup>';
                    });

                    var newRow = `
                        <div class="bank-row border p-3 mb-2 rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="bank-priority mb-0 fw-semibold text-primary">Priority ${bankRows + 1}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-bank-row">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label small">Bank Name <span class="text-danger">*</span></label>
                                        <select name="bank_name[]" class="form-select bank-select" required>
                                            ${bankOptions}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label small">Account Title <span class="text-danger">*</span></label>
                                        <input type="text" name="account_title[]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label small">Account Number / IBAN <span class="text-danger">*</span></label>
                                        <input type="text" name="account_number[]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label small">CNIC Number <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_cnic_number[]" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#bankDetailsContainer').append(newRow);
                } else {
                    alert('You can add maximum 5 bank accounts.');
                }
            });

            // Remove bank row
            $(document).on('click', '.remove-bank-row', function() {
                if ($('.bank-row').length > 1) {
                    $(this).closest('.bank-row').remove();
                    updateBankPriorities();
                }
            });

            // Update bank priorities
            function updateBankPriorities() {
                $('.bank-row').each(function(index) {
                    $(this).find('.bank-priority').text('Priority ' + (index + 1));
                });
            }

            // Submit form
            $('#userDetailsForm').submit(function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var url = "{{ route('user.details.save') }}";
                var method = "POST";
                
                // Show loading state
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Saving...');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (response.success) {
                            alert(response.success);
                            $('#userDetailsModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '\n';
                            });
                            alert('Validation Error:\n' + errorMessage);
                        } else {
                            alert('An error occurred. Please try again.');
                            console.error('Error:', xhr.responseText);
                        }
                    }
                });
            });

            // File input change handler
            $('input[type="file"]').change(function() {
                var file = this.files[0];
                if (file) {
                    var fileName = file.name;
                    var fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                        $(this).val('');
                        return;
                    }
                    
                    if (fileSize > 5) {
                        alert('File size should not exceed 5MB');
                        $(this).val('');
                        return;
                    }
                    
                    console.log('File selected:', fileName, 'Size:', fileSize + 'MB');
                }
            });
        });
    </script>
    <style>
        .professional-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #dee2e6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }
        .professional-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .image-card img {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .image-card img:hover {
            transform: scale(1.05);
        }
        .placeholder-image {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            color: #6c757d;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }
        .section-header {
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .bank-row {
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .form-label small {
            font-size: 12px;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
<div class="row">

    <!-- Change Password Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white"><i class="ti ti-lock me-2 text-white"></i>{{__('Change Password')}}</h5>
        </div>
        <div class="card-body p-4">
            <form method="post" action="{{route('update.password')}}">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-sm-12 form-group mb-3">
                        <label for="old_password" class="form-label">{{ __('Old Password') }} <span class="text-danger">*</span></label>
                        <input class="form-control @error('old_password') is-invalid @enderror" 
                            name="old_password" 
                            type="password" 
                            id="old_password" 
                            required 
                            autocomplete="old_password" 
                            placeholder="{{ __('Enter Old Password') }}">
                        @error('old_password')
                        <span class="invalid-feedback text-danger" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-4 col-sm-12 form-group mb-3">
                        <label for="password" class="form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
                        <input class="form-control @error('password') is-invalid @enderror" 
                            name="password" 
                            type="password" 
                            required 
                            autocomplete="new-password" 
                            id="password" 
                            placeholder="{{ __('Enter New Password') }}">
                        @error('password')
                        <span class="invalid-feedback text-danger" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-4 col-sm-12 form-group mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                        <input class="form-control @error('password_confirmation') is-invalid @enderror" 
                            name="password_confirmation" 
                            type="password" 
                            required 
                            autocomplete="new-password" 
                            id="password_confirmation" 
                            placeholder="{{ __('Confirm New Password') }}">
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-lock me-1"></i>{{__('Change Password')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="row">
                    <div class="col-6">
                        <h5 class="mb-0 text-white"><i class="ti ti-user me-2 text-white"></i>{{__('User Profile Details')}}</h5>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-light" id="openUserDetailsModal">
                            <i class="ti ti-edit me-1"></i>{{__('Update Details')}}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if(isset($userDetail) && $userDetail)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="section-header">Personal Information</h6>
                            <table class="table professional-table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Full Name:</strong></td>
                                    <td>{{ $userDetail->full_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Father Name:</strong></td>
                                    <td>{{ $userDetail->father_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pseudo Name:</strong></td>
                                    <td>{{ $userDetail->pseudo_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $userDetail->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $userDetail->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>CNIC Number:</strong></td>
                                    <td>{{ $userDetail->cnic_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td>{{ $userDetail->date_of_birth ? $userDetail->date_of_birth->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Emergency Phone:</strong></td>
                                    <td>{{ $userDetail->emergency_phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="section-header">Work Information</h6>
                            <table class="table professional-table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Employee ID:</strong></td>
                                    <td>{{ $userDetail->employee_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Team Leader:</strong></td>
                                    <td>{{ $userDetail->team_leader ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Designation:</strong></td>
                                    <td>{{ $userDetail->designation ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Work From:</strong></td>
                                    <td>{{ $userDetail->work_from ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Joining:</strong></td>
                                    <td>{{ $userDetail->date_of_joining ? $userDetail->date_of_joining->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Source of Joining:</strong></td>
                                    <td>{{ $userDetail->source_of_joining ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $userDetail->city ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $userDetail->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- CNIC Documents Section --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="section-header">CNIC Documents</h6>
                            <div class="row">
                                {{-- CNIC Front --}}
                                <div class="col-md-6">
                                    <div class="card image-card shadow-sm">
                                        <div class="card-body text-center p-3">
                                            <p class="fw-semibold mb-3">CNIC Front</p>
                                            @if($userDetail && $userDetail->cnicFront)
                                                <img src="{{ $userDetail->cnicFront->url }}" 
                                                    alt="CNIC Front" class="img-fluid rounded" 
                                                    style="max-height: 300px; width: 100%; object-fit: cover;">
                                                <small class="text-muted mt-2 d-block">
                                                    Uploaded: {{ $userDetail->cnicFront->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            @else
                                                <div class="placeholder-image d-flex align-items-center justify-content-center" style="height: 250px;">
                                                    <i class="ti ti-image-off fs-1 me-2 text-muted"></i>
                                                    <span>No Image Available</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- CNIC Back --}}
                                <div class="col-md-6">
                                    <div class="card image-card shadow-sm">
                                        <div class="card-body text-center p-3">
                                            <p class="fw-semibold mb-3">CNIC Back</p>
                                            @if($userDetail && $userDetail->cnicBack)
                                                <img src="{{ $userDetail->cnicBack->url }}" 
                                                    alt="CNIC Back" class="img-fluid rounded" 
                                                    style="max-height: 300px; width: 100%; object-fit: cover;">
                                                <small class="text-muted mt-2 d-block">
                                                    Uploaded: {{ $userDetail->cnicBack->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            @else
                                                <div class="placeholder-image d-flex align-items-center justify-content-center" style="height: 250px;">
                                                    <i class="ti ti-image-off fs-1 me-2 text-muted"></i>
                                                    <span>No Image Available</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="section-header">Bank Details</h6>
                            <div class="table-responsive">
                                <table class="table professional-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Priority</th>
                                            <th>Bank Name</th>
                                            <th>Account Title</th>
                                            <th>Account Number / IBAN</th>
                                            <th>CNIC Number / Form B No</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($bankDetails) && $bankDetails->count() > 0)
                                            @foreach($bankDetails as $bankIndex => $bank)
                                                <tr>
                                                    <td><span class="badge bg-info">{{ $bank->priority ?? ($bankIndex + 1) }}</span></td>
                                                    <td>{{ $bank->bank_name ?? 'N/A' }}</td>
                                                    <td>{{ $bank->account_title ?? 'N/A' }}</td>
                                                    <td>{{ $bank->account_number ?? 'N/A' }}</td>
                                                    <td>{{ $bank->cnic_number ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($bank->status == 'verified')
                                                            <span class="badge bg-success">Verified</span>
                                                        @elseif($bank->status == 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @else
                                                            <span class="badge bg-warning">Unverified</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="ti ti-building-bank fs-1 mb-2 d-block"></i>
                                                    No bank details found
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-user-x fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-muted mb-3">No details found</h5>
                        <p class="text-muted">Please update your details to view profile information.</p>
                        <button type="button" class="btn btn-primary" id="openUserDetailsModalEmpty">
                            <i class="ti ti-plus me-1"></i>{{__('Add Details')}}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0" id="userDetailsModalLabel">
                    <i class="ti ti-edit me-2"></i>{{ isset($userDetail) && $userDetail ? __('Update User Details') : __('Add User Details') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userDetailsForm" enctype="multipart/form-data">
                @csrf
                
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h6 class="section-header mb-4">Personal Information</h6>
                            <div class="form-group mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="{{ isset($userDetail) ? $userDetail->full_name : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control" value="{{ isset($userDetail) ? $userDetail->father_name : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Pseudo Name</label>
                                <input type="text" name="pseudo_name" class="form-control" value="{{ isset($userDetail) ? $userDetail->pseudo_name : '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ isset($userDetail) ? $userDetail->phone : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ isset($userDetail) ? $userDetail->email : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">CNIC Number <span class="text-danger">*</span></label>
                                <input type="text" name="cnic_number" class="form-control" value="{{ isset($userDetail) ? $userDetail->cnic_number : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ isset($userDetail) && $userDetail->date_of_birth ? $userDetail->date_of_birth->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Emergency Phone <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_phone" class="form-control" value="{{ isset($userDetail) ? $userDetail->emergency_phone : '' }}" required>
                            </div>
                        </div>

                        <!-- Work Information -->
                        <div class="col-md-6">
                            <h6 class="section-header mb-4">Work Information</h6>
                            <div class="form-group mb-3">
                                <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" name="employee_id" class="form-control" value="{{ isset($userDetail) ? $userDetail->employee_id : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Team Leader</label>
                                <input type="text" name="team_leader" class="form-control" value="{{ isset($userDetail) ? $userDetail->team_leader : '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control" value="{{ isset($userDetail) ? $userDetail->designation : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Work From <span class="text-danger">*</span></label>
                                <select name="work_from" class="form-select" required>
                                    <option value="">Select Option</option>
                                    <option value="Office" {{ (isset($userDetail) && ($userDetail->work_from ?? '') == 'Office') ? 'selected' : '' }}>Office</option>
                                    <option value="Home" {{ (isset($userDetail) && ($userDetail->work_from ?? '') == 'Home') ? 'selected' : '' }}>Home</option>
                                    <option value="Hybrid" {{ (isset($userDetail) && ($userDetail->work_from ?? '') == 'Hybrid') ? 'selected' : '' }}>Hybrid</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_joining" class="form-control" value="{{ isset($userDetail) && $userDetail->date_of_joining ? $userDetail->date_of_joining->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Source of Joining</label>
                                <input type="text" name="source_of_joining" class="form-control" value="{{ isset($userDetail) ? $userDetail->source_of_joining : '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="{{ isset($userDetail) ? $userDetail->city : '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="3" required>{{ isset($userDetail) ? $userDetail->address : '' }}</textarea>
                            </div>
                        </div>

                        <!-- CNIC Documents -->
                        <div class="col-12 mt-4">
                            <h6 class="section-header mb-4">CNIC Documents</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">CNIC Front 
                                            <span class="text-danger">{{ !isset($userDetail) || !$userDetail ? '*' : '' }}</span>
                                        </label>
                                        <input type="file" name="cnic_front" class="form-control" accept="image/*" {{ !isset($userDetail) || !$userDetail ? 'required' : '' }}>
                                        <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF | Max size: 5MB</small>
                                        @if(isset($userDetail) && $userDetail->cnicFront)
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="ti ti-check me-1"></i>File already uploaded
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">CNIC Back 
                                            <span class="text-danger">{{ !isset($userDetail) || !$userDetail ? '*' : '' }}</span>
                                        </label>
                                        <input type="file" name="cnic_back" class="form-control" accept="image/*" {{ !isset($userDetail) || !$userDetail ? 'required' : '' }}>
                                        <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF | Max size: 5MB</small>
                                        @if(isset($userDetail) && $userDetail->cnicBack)
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="ti ti-check me-1"></i>File already uploaded
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="section-header mb-0">Bank Details</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="addBankRow">
                                    <i class="ti ti-plus me-1"></i>Add Bank
                                </button>
                            </div>
                            <div id="bankDetailsContainer">
                                @if(isset($bankDetails) && $bankDetails->count() > 0)
                                    @foreach($bankDetails as $bankIndex => $bank)
                                        <div class="bank-row border p-3 mb-3 rounded shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="bank-priority mb-0 fw-semibold text-primary">Priority {{ $bankIndex + 1 }}</h6>
                                                @if($bankIndex > 0)
                                                    <button type="button" class="btn btn-sm btn-danger remove-bank-row">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group mb-2">
                                                        <label class="form-label small">Bank Name <span class="text-danger">{{ $bankIndex === 0 ? '*' : '' }}</span></label>
                                                        <select name="bank_name[]" class="form-select bank-select" {{ $bankIndex === 0 ? 'required' : '' }}>
                                                            <option value="">Select Bank/Account</option>
                                                            @foreach($banks as $category => $categoryBanks)
                                                                <optgroup label="{{ $category }}">
                                                                    @foreach($categoryBanks as $bankItem)
                                                                        <option value="{{ $bankItem->name }}" 
                                                                            {{ $bank->bank_name == $bankItem->name ? 'selected' : '' }}>
                                                                            {{ $bankItem->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-2">
                                                        <label class="form-label small">Account Title <span class="text-danger">{{ $bankIndex === 0 ? '*' : '' }}</span></label>
                                                        <input type="text" name="account_title[]" class="form-control" value="{{ $bank->account_title ?? '' }}" {{ $bankIndex === 0 ? 'required' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-2">
                                                        <label class="form-label small">Account Number / IBAN No <span class="text-danger">{{ $bankIndex === 0 ? '*' : '' }}</span></label>
                                                        <input type="text" name="account_number[]" class="form-control" value="{{ $bank->account_number ?? '' }}" {{ $bankIndex === 0 ? 'required' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-2">
                                                        <label class="form-label small">CNIC Number <span class="text-danger">{{ $bankIndex === 0 ? '*' : '' }}</span></label>
                                                        <input type="text" name="bank_cnic_number[]" class="form-control" value="{{ $bank->cnic_number ?? '' }}" {{ $bankIndex === 0 ? 'required' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="bank-row border p-3 mb-3 rounded shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="bank-priority mb-0 fw-semibold text-primary">Priority 1</h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="form-label small">Bank Name <span class="text-danger">*</span></label>
                                                    <select name="bank_name[]" class="form-select bank-select" required>
                                                        <option value="">Select Bank/Account</option>
                                                        @foreach($banks as $category => $categoryBanks)
                                                            <optgroup label="{{ $category }}">
                                                                @foreach($categoryBanks as $bankItem)
                                                                    <option value="{{ $bankItem->name }}">
                                                                        {{ $bankItem->name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="form-label small">Account Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="account_title[]" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="form-label small">Account Number / IBAN No <span class="text-danger">*</span></label>
                                                    <input type="text" name="account_number[]" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="form-label small">CNIC Number <span class="text-danger">*</span></label>
                                                    <input type="text" name="bank_cnic_number[]" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>
                        {{ isset($userDetail) && $userDetail ? 'Update Details' : 'Save Details' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection