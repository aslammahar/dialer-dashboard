{{-- resources/views/vendor-lists/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Vendor Lists Management')

@push('styles')
<style>
    .table-responsive {
        max-height: 80vh;
        overflow-y: auto;
    }
    .table th {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .conversion-cell {
        background-color: #e3f2fd;
    }
    .edit-mode {
        background-color: #fff3cd;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-list"></i> Vendor Lists Management</h2>
                <button class="btn btn-primary" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>List ID</th>
                                <th>Sales</th>
                                <th>Dialer Name</th>
                                <th>Vendor Name</th>
                                <th>File Name</th>
                                <th>Total Numbers</th>
                                <th>DNC</th>
                                <th>Duplicate</th>
                                <th>Clean</th>
                                <th class="conversion-cell">Sales Conversion</th>
                                <th>Xfers</th>
                                <th class="conversion-cell">Xfers Sales Conv</th>
                                <th class="conversion-cell">Xfers Clean Conv</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendorLists as $list)
                            <tr id="row-{{ $list->id }}">
                                <td>{{ $list->list_id }}</td>
                                <td class="text-center">{{ $list->sales }}</td>
                                <td>{{ $list->dialer_name ?? '-' }}</td>
                                <td class="editable-field" data-field="vendor_name" data-id="{{ $list->id }}">
                                    {{ $list->vendor_name ?? '-' }}
                                </td>
                                <td class="editable-field" data-field="file_name" data-id="{{ $list->id }}">
                                    {{ $list->file_name ?? '-' }}
                                </td>
                                <td class="editable-field text-center" data-field="total_numbers" data-id="{{ $list->id }}">
                                    {{ $list->total_numbers ?: '-' }}
                                </td>
                                <td class="editable-field text-center" data-field="dnc" data-id="{{ $list->id }}">
                                    {{ $list->dnc ?: '-' }}
                                </td>
                                <td class="editable-field text-center" data-field="duplicate" data-id="{{ $list->id }}">
                                    {{ $list->duplicate ?: '-' }}
                                </td>
                                <td class="editable-field text-center" data-field="clean" data-id="{{ $list->id }}">
                                    {{ $list->clean ?: '-' }}
                                </td>
                                <td class="conversion-cell text-center">
                                    {{ number_format($list->sales_conversion, 1) }}
                                </td>
                                <td class="text-center">{{ $list->xfers }}</td>
                                <td class="conversion-cell text-center">
                                    {{ number_format($list->xfers_sales_conversion,1) }}
                                </td>
                                <td class="conversion-cell text-center">
                                    {{ number_format($list->xfers_clean_conversion, 1) }}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn" onclick="editRow({{ $list->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success save-btn d-none" onclick="saveRow({{ $list->id }})">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary cancel-btn d-none" onclick="cancelEdit({{ $list->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    No vendor lists found. Click "Refresh Data" to generate from closed calls.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>

{{-- Loading Modal --}}
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Processing...</p>
            </div>
        </div>
    </div>
</div>

{{-- Success/Error Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<script>
    let originalData = {};
    let loadingModal;

    document.addEventListener('DOMContentLoaded', function() {
        loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    });

    function editRow(id) {
        const row = document.getElementById(`row-${id}`);
        const editableFields = row.querySelectorAll('.editable-field');
        
        // Store original data
        originalData[id] = {};
        editableFields.forEach(field => {
            const fieldName = field.dataset.field;
            originalData[id][fieldName] = field.textContent.trim();
            
            // Replace with input
            const currentValue = field.textContent.trim() === '-' ? '' : field.textContent.trim();
            const inputType = ['total_numbers', 'dnc', 'duplicate', 'clean'].includes(fieldName) ? 'number' : 'text';
            field.innerHTML = `<input type="${inputType}" class="form-control form-control-sm" value="${currentValue}" ${inputType === 'number' ? 'min="0" oninput="calculateConversions(' + id + ')"' : ''}${fieldName === 'vendor_name' ? ' placeholder="Enter vendor name"' : ''}${fieldName === 'file_name' ? ' placeholder="Enter file name"' : ''}>`;
        });

        // Toggle buttons
        row.classList.add('edit-mode');
        row.querySelector('.edit-btn').classList.add('d-none');
        row.querySelector('.save-btn').classList.remove('d-none');
        row.querySelector('.cancel-btn').classList.remove('d-none');
    }

    function cancelEdit(id) {
        const row = document.getElementById(`row-${id}`);
        const editableFields = row.querySelectorAll('.editable-field');
        
        // Restore original values for editable fields
        editableFields.forEach(field => {
            const fieldName = field.dataset.field;
            const originalValue = originalData[id][fieldName];
            field.textContent = originalValue;
        });

        // Restore original conversion values (recalculate from original data)
        const originalClean = parseInt(originalData[id]['clean']) || 0;
        const sales = parseInt(row.cells[1].textContent.trim()) || 0;
        const xfers = parseInt(row.cells[10].textContent.trim()) || 0;
        
        const salesConversion = originalClean > 0 ? (sales / originalClean) : 0;
        const xfersSalesConversion = xfers > 0 ? (sales / xfers) : 0;
        const xfersCleanConversion = xfers > 0 && originalClean > 0 ? (originalClean / xfers) : 0;
        
        // Update conversion cells with original calculated values
        row.cells[9].textContent = salesConversion.toFixed(4);
        row.cells[11].textContent = xfersSalesConversion.toFixed(4);
        row.cells[12].textContent = xfersCleanConversion.toFixed(4);

        // Toggle buttons back
        row.classList.remove('edit-mode');
        row.querySelector('.edit-btn').classList.remove('d-none');
        row.querySelector('.save-btn').classList.add('d-none');
        row.querySelector('.cancel-btn').classList.add('d-none');
        
        delete originalData[id];
    }

    // Calculate conversions in real-time
    function calculateConversions(id) {
        const row = document.getElementById(`row-${id}`);
        
        // Get values from the correct cells by their position
        // Column indexes: 0=List ID, 1=Sales, 2=Dialer, 3=Vendor, 4=File, 5=Total, 6=DNC, 7=Duplicate, 8=Clean, 9=Sales Conv, 10=Xfers, 11=Xfers Sales, 12=Xfers Clean, 13=Actions
        const sales = parseInt(row.cells[1].textContent.trim()) || 0; // Sales column (index 1)
        const xfers = parseInt(row.cells[10].textContent.trim()) || 0; // Xfers column (index 10)
        
        // Get clean value from the input field
        const cleanInput = row.querySelector('[data-field="clean"] input');
        const clean = parseInt(cleanInput ? cleanInput.value : 0) || 0;
        
        console.log(`Calculating conversions for row ${id}:`, { sales, xfers, clean }); // Debug
        
        // Calculate conversions
        const salesConversion = clean > 0 ? (sales / clean) : 0;
        const xfersSalesConversion = xfers > 0 ? (sales / xfers) : 0;
        const xfersCleanConversion = xfers > 0 && clean > 0 ? (clean / xfers) : 0;
        
        console.log('Calculated conversions:', { salesConversion, xfersSalesConversion, xfersCleanConversion }); // Debug
        
        // Update conversion cells in real-time (indexes 9, 11, 12)
        row.cells[9].textContent = salesConversion.toFixed(4);   // Sales Conversion
        row.cells[11].textContent = xfersSalesConversion.toFixed(4); // Xfers Sales Conv  
        row.cells[12].textContent = xfersCleanConversion.toFixed(4); // Xfers Clean Conv
    }

    function saveRow(id) {
        console.log('Saving row:', id);
        
        if (!loadingModal) {
            loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        }
        loadingModal.show();
        
        const row = document.getElementById(`row-${id}`);
        const editableFields = row.querySelectorAll('.editable-field');
        const formData = new FormData();
        
        // Get current values from the correct cells
        const sales = parseInt(row.cells[1].textContent.trim()) || 0; // Sales (index 1)
        const xfers = parseInt(row.cells[10].textContent.trim()) || 0; // Xfers (index 10)
        
        editableFields.forEach(field => {
            const fieldName = field.dataset.field;
            const input = field.querySelector('input');
            const value = input ? input.value || '' : '';
            formData.append(fieldName, value);
            console.log(`Field ${fieldName}:`, value);
        });

        // Calculate and add conversions to form data
        const cleanInput = row.querySelector('[data-field="clean"] input');
        const clean = parseInt(cleanInput ? cleanInput.value : 0) || 0;
        
        const salesConversion = clean > 0 ? (sales / clean) : 0;
        const xfersSalesConversion = xfers > 0 ? (sales / xfers) : 0;
        const xfersCleanConversion = xfers > 0 && clean > 0 ? (clean / xfers) : 0;
        
        console.log('Sending conversions:', { salesConversion, xfersSalesConversion, xfersCleanConversion }); // Debug
        
        formData.append('sales_conversion', salesConversion);
        formData.append('xfers_sales_conversion', xfersSalesConversion);
        formData.append('xfers_clean_conversion', xfersCleanConversion);

        formData.append('_method', 'PUT');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch(`/vendor-lists/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Update row with new data
                updateRowDisplay(id, data.data);
                
                // Reset edit mode
                row.classList.remove('edit-mode');
                row.querySelector('.edit-btn').classList.remove('d-none');
                row.querySelector('.save-btn').classList.add('d-none');
                row.querySelector('.cancel-btn').classList.add('d-none');
                
                delete originalData[id];
                
                // Show success message
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || 'Error updating record');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showAlert('danger', `Network error occurred while updating record: ${error.message}`);
        })
        .finally(() => {
            loadingModal.hide();
        });
    }

    function updateRowDisplay(id, data) {
        const row = document.getElementById(`row-${id}`);
        const fields = {
            'vendor_name': data.vendor_name || '-',
            'file_name': data.file_name || '-',
            'total_numbers': data.total_numbers || '-',
            'dnc': data.dnc || '-',
            'duplicate': data.duplicate || '-',
            'clean': data.clean || '-'
        };
        
        // Update editable fields
        Object.keys(fields).forEach(fieldName => {
            const field = row.querySelector(`[data-field="${fieldName}"]`);
            if (field) {
                field.textContent = fields[fieldName];
            }
        });
        
        // Update conversion fields
        const conversionFields = row.querySelectorAll('.conversion-cell');
        if (conversionFields.length >= 3) {
            conversionFields[0].textContent = parseFloat(data.sales_conversion || 0).toFixed(4);
            conversionFields[1].textContent = parseFloat(data.xfers_sales_conversion || 0).toFixed(4);
            conversionFields[2].textContent = parseFloat(data.xfers_clean_conversion || 0).toFixed(4);
        }
    }

    function refreshData() {
        loadingModal.show();
        
        fetch('/vendor-lists/refresh', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('danger', data.message || 'Error refreshing data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Network error occurred while refreshing data');
        })
        .finally(() => {
            loadingModal.hide();
        });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                const alert = new bootstrap.Alert(alertDiv);
                alert.close();
            }
        }, 5000);
    }

    // Handle browser back/refresh during edit mode
    window.addEventListener('beforeunload', function(e) {
        if (Object.keys(originalData).length > 0) {
            const confirmationMessage = 'You have unsaved changes. Are you sure you want to leave?';
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });
</script>
@endsection

