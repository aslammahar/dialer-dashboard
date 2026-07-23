@extends('layouts.admin')

@section('page-title')
    {{__('Salary Payments')}}
@endsection

@push('script-page')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Store customer references globally
    window.customerReferences = {};

    // Send Payment Modal - Load Full Details
    $('.send-payment-btn').click(function() {
        var salaryId = $(this).data('salary-id');
        
        $('#sendPaymentModal').modal('show');
        $('#modalContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 text-muted">Loading details...</p></div>');
        
        $.ajax({
            url: '/salary-payments/get-salary-details/' + salaryId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayPaymentDetails(response.data);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error loading details:', xhr);
                alert('Failed to load salary details. Please try again.');
                $('#sendPaymentModal').modal('hide');
            }
        });
    });

    function displayPaymentDetails(data) {
        var html = `
            <div class="row g-3">
                <!-- Employee Information -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-user me-2"></i>Employee Details</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="45%" class="text-muted">Name:</td>
                                    <td><strong>${data.user_name}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Employee ID:</td>
                                    <td><strong>${data.employee_id}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Department:</td>
                                    <td><span class="badge bg-primary">${data.department}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Designation:</td>
                                    <td><strong>${data.designation}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-gradient-info text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-calendar me-2"></i>Attendance Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <h5 class="mb-1">${data.working_days}</h5>
                                        <small class="text-muted">Working Days</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <h5 class="mb-1 text-success">${data.present_days}</h5>
                                        <small class="text-muted">Present</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <h5 class="mb-1 text-danger">${data.absent_days}</h5>
                                        <small class="text-muted">Absent</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <h5 class="mb-1 text-warning">${data.leave_days}</h5>
                                        <small class="text-muted">Leave</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Salary Breakdown -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-success text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-calculator me-2"></i>Complete Salary Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Earnings -->
                                <div class="col-md-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded mb-3">
                                        <h6 class="text-success mb-3"><i class="ti ti-trending-up me-1"></i>Earnings</h6>
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td>Basic Salary</td>
                                                <td class="text-end"><strong>Rs. ${data.basic_salary}</strong></td>
                                            </tr>
                                            ${data.punctuality > 0 ? `
                                            <tr>
                                                <td>Punctuality</td>
                                                <td class="text-end text-success">+ Rs. ${data.punctuality}</td>
                                            </tr>` : ''}
                                            ${data.total_allowances > 0 ? `
                                            <tr>
                                                <td>Allowances</td>
                                                <td class="text-end text-success">+ Rs. ${data.total_allowances}</td>
                                            </tr>` : ''}
                                            ${data.bonus > 0 ? `
                                            <tr>
                                                <td>Bonus</td>
                                                <td class="text-end text-success">+ Rs. ${data.bonus}</td>
                                            </tr>` : ''}
                                            <tr class="border-top">
                                                <td><strong>Gross Salary</strong></td>
                                                <td class="text-end"><h5 class="mb-0 text-success">Rs. ${data.gross_salary}</h5></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Deductions -->
                                <div class="col-md-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded mb-3">
                                        <h6 class="text-danger mb-3"><i class="ti ti-trending-down me-1"></i>Deductions</h6>
                                        <table class="table table-sm mb-0">
                                            ${data.total_deductions > 0 ? `
                                            <tr>
                                                <td>Other Deductions</td>
                                                <td class="text-end text-danger">- Rs. ${data.total_deductions}</td>
                                            </tr>` : ''}
                                            ${data.tax_amount > 0 ? `
                                            <tr>
                                                <td>
                                                    Income Tax (${data.tax_percentage}%)
                                                    ${data.tax_slab ? `<br><small class="text-muted">${data.tax_slab}</small>` : ''}
                                                </td>
                                                <td class="text-end text-primary">- Rs. ${data.tax_amount}</td>
                                            </tr>` : ''}
                                            <tr class="border-top">
                                                <td><strong>Total Deductions</strong></td>
                                                <td class="text-end"><h5 class="mb-0 text-danger">Rs. ${data.total_all_deductions}</h5></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Net Salary -->
                            <div class="alert alert-primary border-0 shadow-sm text-center mb-0">
                                <h6 class="mb-2 text-uppercase">Final Net Salary</h6>
                                <h2 class="mb-0 text-primary fw-bold">Rs. ${data.net_salary}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Selection Form -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-dark text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-building-bank me-2"></i>Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <form id="sendPaymentForm" enctype="multipart/form-data">
                                <input type="hidden" name="monthly_salary_id" value="${data.salary_id}">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Select Bank Account <span class="text-danger">*</span></label>
                                    <select name="user_bank_detail_id" class="form-select form-select-lg" required>
                                        <option value="">Choose Bank Account</option>
                                        ${data.banks.map(bank => `
                                            <option value="${bank.id}">
                                                ${bank.bank_name} - ${bank.account_number} (Priority ${bank.priority})
                                            </option>
                                        `).join('')}
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="payment_amount" class="form-control form-control-lg" 
                                           value="${data.net_salary_raw}" step="0.01" required readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Screenshot <span class="text-danger">*</span></label>
                                    <input type="file" name="payment_screenshot" class="form-control" 
                                           accept="image/*" required>
                                    <small class="text-muted">Upload payment confirmation (Max: 5MB, PNG/JPG)</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3" 
                                              placeholder="Add any notes..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="ti ti-send me-2"></i>Send Payment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#modalContent').html(html);
        
        $('#sendPaymentForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            submitPayment(this);
        });
    }

    function submitPayment(form) {
        var formData = new FormData(form);
        var submitBtn = $(form).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Processing...');

        $.ajax({
            url: '{{ route("salary.payments.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    }

    $('.view-details-btn').click(function() {
        var salaryId = $(this).data('salary-id');
        
        $('#viewDetailsModal').modal('show');
        $('#viewModalContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 text-muted">Loading details...</p></div>');
        
        $.ajax({
            url: '/salary-payments/get-salary-details/' + salaryId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayViewDetails(response.data);
                }
            },
            error: function(xhr) {
                alert('Failed to load details');
                $('#viewDetailsModal').modal('hide');
            }
        });
    });

    function displayViewDetails(data) {
        var html = `
            <div class="row g-3">
                <!-- Employee Information -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-user me-2"></i>Employee Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="45%" class="text-muted">Name:</td>
                                    <td><strong>${data.user_name}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Employee ID:</td>
                                    <td><strong>${data.employee_id}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Department:</td>
                                    <td><span class="badge bg-primary">${data.department}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Designation:</td>
                                    <td><strong>${data.designation}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-gradient-info text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-calendar me-2"></i>Attendance Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center g-2">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <h5 class="mb-1">${data.working_days}</h5>
                                        <small class="text-muted">Working Days</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <h5 class="mb-1 text-success">${data.present_days}</h5>
                                        <small class="text-muted">Present</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <h5 class="mb-1 text-danger">${data.absent_days}</h5>
                                        <small class="text-muted">Absent</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <h5 class="mb-1 text-warning">${data.leave_days}</h5>
                                        <small class="text-muted">Leave</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-success text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-building-bank me-2"></i>Bank Account Details (Priority Wise)</h6>
                        </div>
                        <div class="card-body">
                            ${data.banks.length > 0 ? `
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" width="10%">Priority</th>
                                                <th width="25%">Bank Name</th>
                                                <th width="25%">Account Title</th>
                                                <th width="30%">Account Number</th>
                                                <th class="text-center" width="10%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.banks.map(bank => `
                                                <tr class="${bank.priority === 1 ? 'table-success' : ''}">
                                                    <td class="text-center">
                                                        <span class="badge ${bank.priority === 1 ? 'bg-success' : 'bg-secondary'} rounded-pill">
                                                            #${bank.priority}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>${bank.bank_name}</strong>
                                                        ${bank.priority === 1 ? '<br><small class="text-success"><i class="ti ti-star-filled"></i> Primary</small>' : ''}
                                                    </td>
                                                    <td>${bank.account_title}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <code class="me-2">${bank.account_number}</code>
                                                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('${bank.account_number}')" title="Copy">
                                                                <i class="ti ti-copy"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success rounded-pill">Verified</span>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-info border-0 mb-0 mt-3">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Priority 1 account is the primary bank account for salary payments.
                                </div>
                            ` : `
                                <div class="alert alert-warning border-0 mb-0">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    No verified bank accounts found.
                                </div>
                            `}
                        </div>
                    </div>
                </div>

                <!-- Salary Breakdown -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-dark text-white border-0">
                            <h6 class="mb-0"><i class="ti ti-calculator me-2"></i>Complete Salary Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <h6 class="text-success mb-3"><i class="ti ti-trending-up me-1"></i>Earnings</h6>
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td>Basic Salary</td>
                                                <td class="text-end"><strong>Rs. ${data.basic_salary}</strong></td>
                                            </tr>
                                            ${data.punctuality > 0 ? `
                                            <tr>
                                                <td>Punctuality</td>
                                                <td class="text-end text-success">+ Rs. ${data.punctuality}</td>
                                            </tr>` : ''}
                                            ${data.total_allowances > 0 ? `
                                            <tr>
                                                <td>Allowances</td>
                                                <td class="text-end text-success">+ Rs. ${data.total_allowances}</td>
                                            </tr>` : ''}
                                            ${data.bonus > 0 ? `
                                            <tr>
                                                <td>Bonus</td>
                                                <td class="text-end text-success">+ Rs. ${data.bonus}</td>
                                            </tr>` : ''}
                                            <tr class="border-top">
                                                <td><strong>Gross Salary</strong></td>
                                                <td class="text-end"><h5 class="mb-0 text-success">Rs. ${data.gross_salary}</h5></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <h6 class="text-danger mb-3"><i class="ti ti-trending-down me-1"></i>Deductions</h6>
                                        <table class="table table-sm mb-0">
                                            ${data.total_deductions > 0 ? `
                                            <tr>
                                                <td>Other Deductions</td>
                                                <td class="text-end text-danger">- Rs. ${data.total_deductions}</td>
                                            </tr>` : ''}
                                            ${data.tax_amount > 0 ? `
                                            <tr>
                                                <td>Income Tax (${data.tax_percentage}%)<br><small class="text-muted">${data.tax_slab || ''}</small></td>
                                                <td class="text-end text-primary">- Rs. ${data.tax_amount}</td>
                                            </tr>` : ''}
                                            ${!data.total_deductions && !data.tax_amount ? `
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No Deductions</td>
                                            </tr>` : ''}
                                            <tr class="border-top">
                                                <td><strong>Total Deductions</strong></td>
                                                <td class="text-end"><h5 class="mb-0 text-danger">Rs. ${data.total_all_deductions}</h5></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-primary border-0 shadow-sm text-center mt-3 mb-0">
                                <h6 class="mb-2 text-uppercase">Final Net Salary</h6>
                                <h2 class="mb-1 text-primary fw-bold">Rs. ${data.net_salary}</h2>
                                <small class="text-muted">Amount to be transferred</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#viewModalContent').html(html);
    }

    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Copied: ' + text);
        });
    }

    // ==================== EXPORT FUNCTIONS ====================

    // Generate initial customer references on page load
    function generateCustomerReferences() {
        $('.customer-ref-display').each(function() {
            var userId = $(this).data('user-id');
            var reference = date('Ymd') + String(userId).padStart(5, '0');
            window.customerReferences[userId] = reference;
            $(this).text(reference);
        });
    }

    // Helper function to get current date in YYYYMMDD format
    function date(format) {
        var d = new Date();
        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return year + month + day;
    }

    // Initialize customer references
    generateCustomerReferences();

    // Regenerate single customer reference
    window.regenerateSingleReference = function(userId) {
        var reference = date('Ymd') + String(userId).padStart(5, '0') + Math.floor(Math.random() * 900 + 100);
        window.customerReferences[userId] = reference;
        $('.customer-ref-' + userId).text(reference);
        
        // Show success message
        var btn = $('#regen-btn-' + userId);
        var originalIcon = btn.html();
        btn.html('<i class="ti ti-check text-success"></i>');
        setTimeout(function() {
            btn.html(originalIcon);
        }, 1000);
    };

    // Export by Department
    $('#exportDepartmentBtn').click(function() {
        var departmentId = $('#exportDepartmentSelect').val();
        
        if (!departmentId) {
            alert('Please select a department');
            return;
        }
        
        // Store references before export
        storeCustomerReferences();
        
        var url = '{{ route("salary.payments.export.department", ":id") }}'.replace(':id', departmentId);
        url += '?year={{ $year }}&month={{ $month }}&use_stored_reference=1';
        
        window.location.href = url;
    });

    // Export All
    $('#exportAllBtn').click(function() {
        // Store references before export
        storeCustomerReferences();
        
        var url = '{{ route("salary.payments.export.all") }}';
        url += '?year={{ $year }}&month={{ $month }}&use_stored_reference=1';
        
        window.location.href = url;
    });

    // Store customer references in cache
    function storeCustomerReferences() {
        $.ajax({
            url: '{{ route("salary.payments.store.references") }}',
            type: 'POST',
            data: {
                references: window.customerReferences
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async: false // Make it synchronous to ensure storage before export
        });
    }

    // Preview Export Data
    $('#previewExportBtn').click(function() {
        var departmentId = $('#exportDepartmentSelect').val() || null;
        
        $.ajax({
            url: '{{ route("salary.payments.preview.export") }}',
            type: 'POST',
            data: {
                department_id: departmentId,
                year: '{{ $year }}',
                month: '{{ $month }}'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    displayPreviewModal(response.data, response.total_amount, response.total_records);
                }
            },
            error: function(xhr) {
                alert('Error loading preview');
            }
        });
    });

    function displayPreviewModal(data, totalAmount, totalRecords) {
        var html = `
            <div class="modal fade" id="previewModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="ti ti-eye me-2"></i>Export Preview</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Total Records:</strong> ${totalRecords} | 
                                <strong>Total Amount:</strong> Rs. ${parseFloat(totalAmount).toLocaleString()}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Account Number</th>
                                            <th>Beneficiary Name</th>
                                            <th>Customer Ref</th>
                                            <th>Amount</th>
                                            <th>Bank Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.map(row => `
                                            <tr>
                                                <td>
                                                    <strong>${row.user_name}</strong><br>
                                                    <small class="text-muted">${row.employee_id}</small>
                                                </td>
                                                <td><code>${row.account_number}</code></td>
                                                <td>${row.account_title}</td>
                                                <td><code>${row.customer_reference}</code></td>
                                                <td><strong>Rs. ${parseFloat(row.net_salary).toLocaleString()}</strong></td>
                                                <td><span class="badge bg-secondary">${row.bank_code || 'N/A'}</span></td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(html);
        $('#previewModal').modal('show');
        
        $('#previewModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // Regenerate All Customer References
    $('#regenerateAllReferencesBtn').click(function() {
        if (!confirm('This will generate new customer reference numbers for all employees. Continue?')) {
            return;
        }
        
        var btn = $(this);
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Regenerating...');
        
        // Regenerate for all visible employees
        $('.customer-ref-display').each(function() {
            var userId = $(this).data('user-id');
            window.regenerateSingleReference(userId);
        });
        
        setTimeout(function() {
            btn.prop('disabled', false).html(originalText);
            alert('All customer references have been regenerated successfully!');
        }, 1000);
    });
});
</script>

<style>
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.bg-gradient-dark { background: linear-gradient(135deg, #434343 0%, #000000 100%); }

.stats-card {
    border-radius: 15px;
    padding: 25px;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.department-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.department-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.bank-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #667eea;
}

.bank-section-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.meezan-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.card {
    border-radius: 12px;
}

.table {
    margin-bottom: 0;
}

.btn-group-sm .btn {
    padding: 0.35rem 0.6rem;
}

.payment-status-badge {
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: 500;
}

.export-card {
    transition: all 0.3s ease;
}

.export-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.customer-ref-badge {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    padding: 4px 8px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 4px;
}

.regen-btn {
    padding: 2px 6px;
    font-size: 0.75rem;
    border: none;
    background: transparent;
    color: #ff9800;
    cursor: pointer;
    transition: all 0.2s;
}

.regen-btn:hover {
    color: #f57c00;
    transform: rotate(180deg);
}
</style>
@endpush

@section('content')
<div class="row">
    
    <!-- Active Filters -->
    @if($department || $year != date('Y') || $month != date('m'))
    <div class="col-12 mb-4">
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between">
            <div>
                <strong><i class="ti ti-filter me-2"></i>Active Filters:</strong>
                @if($department)
                    <span class="badge bg-white text-primary ms-2">
                        Department: {{ $departments->find($department)->name ?? 'N/A' }}
                    </span>
                @endif
                <span class="badge bg-white text-primary ms-2">
                    Period: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
                </span>
            </div>
            <a href="{{ route('salary.payments.index') }}" class="btn btn-sm btn-light">
                <i class="ti ti-x me-1"></i>Clear All
            </a>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Total Salary Amount</h6>
                <i class="ti ti-wallet" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
            <h2 class="mb-0 fw-bold">Rs. {{ number_format($grandTotal, 2) }}</h2>
            <small class="opacity-75">For selected period</small>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Pending Payments</h6>
                <i class="ti ti-clock" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
            <h2 class="mb-0 fw-bold">Rs. {{ number_format($totalPending, 2) }}</h2>
            <small class="opacity-75">Awaiting processing</small>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Sent Payments</h6>
                <i class="ti ti-check" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
            <h2 class="mb-0 fw-bold">Rs. {{ number_format($totalSent, 2) }}</h2>
            <small class="opacity-75">Completed transfers</small>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('salary.payments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $department == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year</label>
                            <select name="year" class="form-select">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Month</label>
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-1"></i>Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0"><i class="ti ti-download me-2"></i>Export to Excel</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-sm btn-outline-warning me-2" id="regenerateAllReferencesBtn">
                            <i class="ti ti-refresh me-1"></i>Regenerate All References
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="previewExportBtn">
                            <i class="ti ti-eye me-1"></i>Preview Data
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100 export-card border">
                            <div class="card-body text-center">
                                <i class="ti ti-file-spreadsheet text-success" style="font-size: 3rem;"></i>
                                <h6 class="mt-3">Export All Departments</h6>
                                <p class="text-muted small">Export salary payments for all departments</p>
                                <button type="button" class="btn btn-success w-100" id="exportAllBtn">
                                    <i class="ti ti-download me-1"></i>Export All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 export-card border">
                            <div class="card-body">
                                <i class="ti ti-building text-primary" style="font-size: 3rem;"></i>
                                <h6 class="mt-3">Export by Department</h6>
                                <p class="text-muted small">Select a department to export</p>
                                <select class="form-select mb-3" id="exportDepartmentSelect">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary w-100" id="exportDepartmentBtn">
                                    <i class="ti ti-download me-1"></i>Export Department
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-info border-0 mb-0">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <strong><i class="ti ti-info-circle me-2"></i>Customer Reference Number Format</strong>
                                <p class="mb-0 small mt-1">
                                    <code>YYYYMMDD + UserID (5 digits) + Random (3 digits)</code>
                                    <br>Customer references are shown in the table below. Click the refresh icon to regenerate individual references, or use "Regenerate All References" button above.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department-wise Payment List -->
    <div class="col-12">
        @forelse($departmentWiseData as $deptData)
            <div class="card department-card mb-4">
                <div class="card-header bg-light border-bottom-0">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0">
                                <i class="ti ti-building me-2 text-primary"></i>{{ $deptData['department']->name ?? 'N/A' }}
                            </h5>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary fs-6 py-2 px-3">
                                Total: Rs. {{ number_format($deptData['total_department'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Meezan Bank Section -->
                    @if(count($deptData['meezan_banks']) > 0)
                        <div class="bank-section">
                            <div class="bank-section-header meezan-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="ti ti-building-bank me-2"></i>Meezan Bank Accounts</h6>
                                    <strong>Total: Rs. {{ number_format($deptData['total_meezan'], 2) }}</strong>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Bank Account</th>
                                            <th>IBAN</th>
                                            <th>Customer Ref</th>
                                            <th>Salary Amount</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deptData['meezan_banks'] as $item)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $item['user']->name }}</strong>
                                                        <br><small class="text-muted">{{ $item['user']->userDetail->employee_id ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        {{ $item['bank']->bank_name }}
                                                        <br><small class="text-muted">{{ $item['bank']->account_title }}</small>
                                                    </div>
                                                </td>
                                                <td><code>{{ $item['bank']->account_number }}</code></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="customer-ref-badge customer-ref-{{ $item['user']->id }} customer-ref-display" 
                                                              data-user-id="{{ $item['user']->id }}">
                                                            {{ date('Ymd') . str_pad($item['user']->id, 5, '0', STR_PAD_LEFT) }}
                                                        </span>
                                                        <button type="button" 
                                                                class="regen-btn" 
                                                                id="regen-btn-{{ $item['user']->id }}"
                                                                onclick="regenerateSingleReference({{ $item['user']->id }})"
                                                                title="Regenerate Reference">
                                                            <i class="ti ti-refresh"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td><strong class="text-primary">Rs. {{ number_format($item['salary']->net_salary, 2) }}</strong></td>
                                                <td>
                                                    @if($item['payment'])
                                                        <span class="badge payment-status-badge {{ $item['payment']->status_badge_class }}">
                                                            {{ ucfirst($item['payment']->payment_status) }}
                                                        </span>
                                                    @else
                                                        <span class="badge payment-status-badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-info view-details-btn" 
                                                                data-salary-id="{{ $item['salary']->id }}"
                                                                title="View Details">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                        @if($item['payment'] && $item['payment']->isSent())
                                                            <a href="{{ route('salary.payments.show', $item['payment']->id) }}" 
                                                               class="btn btn-success"
                                                               title="View Payment">
                                                                <i class="ti ti-file-invoice"></i>
                                                            </a>
                                                        @else
                                                            <button class="btn btn-primary send-payment-btn"
                                                                    data-salary-id="{{ $item['salary']->id }}"
                                                                    title="Send Payment">
                                                                <i class="ti ti-send"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Other Banks Section -->
                    @if(count($deptData['other_banks']) > 0)
                        <div class="bank-section">
                            <div class="bank-section-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="ti ti-building-bank me-2"></i>Other Bank Accounts</h6>
                                    <strong>Total: Rs. {{ number_format($deptData['total_others'], 2) }}</strong>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Bank Account</th>
                                            <th>IBAN</th>
                                            <th>Customer Ref</th>
                                            <th>Salary Amount</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deptData['other_banks'] as $item)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $item['user']->name }}</strong>
                                                        <br><small class="text-muted">{{ $item['user']->userDetail->employee_id ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        {{ $item['bank']->bank_name }}
                                                        <br><small class="text-muted">{{ $item['bank']->account_title }}</small>
                                                    </div>
                                                </td>
                                                <td><code>{{ $item['bank']->account_number }}</code></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="customer-ref-badge customer-ref-{{ $item['user']->id }} customer-ref-display" 
                                                              data-user-id="{{ $item['user']->id }}">
                                                            {{ date('Ymd') . str_pad($item['user']->id, 5, '0', STR_PAD_LEFT) }}
                                                        </span>
                                                        <button type="button" 
                                                                class="regen-btn" 
                                                                id="regen-btn-{{ $item['user']->id }}"
                                                                onclick="regenerateSingleReference({{ $item['user']->id }})"
                                                                title="Regenerate Reference">
                                                            <i class="ti ti-refresh"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td><strong class="text-primary">Rs. {{ number_format($item['salary']->net_salary, 2) }}</strong></td>
                                                <td>
                                                    @if($item['payment'])
                                                        <span class="badge payment-status-badge {{ $item['payment']->status_badge_class }}">
                                                            {{ ucfirst($item['payment']->payment_status) }}
                                                        </span>
                                                    @else
                                                        <span class="badge payment-status-badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-info view-details-btn" 
                                                                data-salary-id="{{ $item['salary']->id }}"
                                                                title="View Details">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                        @if($item['payment'] && $item['payment']->isSent())
                                                            <a href="{{ route('salary.payments.show', $item['payment']->id) }}" 
                                                               class="btn btn-success"
                                                               title="View Payment">
                                                                <i class="ti ti-file-invoice"></i>
                                                            </a>
                                                        @else
                                                            <button class="btn btn-primary send-payment-btn"
                                                                    data-salary-id="{{ $item['salary']->id }}"
                                                                    title="Send Payment">
                                                                <i class="ti ti-send"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ti ti-wallet-off text-muted" style="font-size: 64px;"></i>
                    <h5 class="mt-3 text-muted">No Salary Data Found</h5>
                    <p class="text-muted">No approved salaries for the selected period.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Send Payment Modal -->
<div class="modal fade" id="sendPaymentModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h5 class="modal-title"><i class="ti ti-send me-2"></i>Send Salary Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalContent"></div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title"><i class="ti ti-info-circle me-2"></i>Complete Salary & Bank Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewModalContent"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection