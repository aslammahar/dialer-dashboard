@extends('layouts.admin')

@section('title', 'Queue Sales')

@section('content')

<style>
    .header {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }
    .date-filter-container {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .date-filter-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }
    .date-filter-input {
        padding: 0.5rem;
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        min-width: 200px;
    }
    .date-filter-input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    .btn-filter {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #ffffff;
        background-color: #4f46e5;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .btn-filter:hover {
        background-color: #4338ca;
    }
    .btn-reset {
        background-color: #6b7280;
    }
    .btn-reset:hover {
        background-color: #4b5563;
    }
    .timezone-info {
        font-size: 0.75rem;
        color: #6b7280;
        margin-left: auto;
    }
    .dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .status-box {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
    }
    .status-box h3 {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .status-box p {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }
    .status-queued {
        border-left: 4px solid #3b82f6;
    }
    .status-assigned {
        border-left: 4px solid #f59e0b;
    }
    .status-approved {
        border-left: 4px solid #10b981;
    }
    .status-rejected {
        border-left: 4px solid #dc2626;
    }
    .table-container {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1400px;
    }
    .table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        position: relative;
    }
    .table th:hover {
        background-color: #f3f4f6;
    }
    .table th.sortable::after {
        content: '⇅';
        position: absolute;
        right: 0.5rem;
        opacity: 0.3;
    }
    .table th.sortable.asc::after {
        content: '↑';
        opacity: 1;
    }
    .table th.sortable.desc::after {
        content: '↓';
        opacity: 1;
    }
    .table td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
    }
    .table tr:hover {
        background-color: #f9fafb;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-primary {
        color: #ffffff;
        background-color: #4f46e5;
    }
    .btn-primary:hover {
        background-color: #4338ca;
    }
    .btn-success {
        color: #ffffff;
        background-color: #10b981;
    }
    .btn-success:hover {
        background-color: #059669;
    }
    .btn-warning {
        color: #ffffff;
        background-color: #f59e0b;
    }
    .btn-warning:hover {
        background-color: #d97706;
    }
    .btn-danger {
        color: #ffffff;
        background-color: #dc2626;
    }
    .btn-danger:hover {
        background-color: #b91c1c;
    }
    .btn-info {
        color: #ffffff;
        background-color: #3b82f6;
    }
    .btn-info:hover {
        background-color: #2563eb;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 9999px;
    }
    .status-queue {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .status-assigned {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .badge-connected {
        background-color: #d1fae5;
        color: #065f46;
    }
    .badge-not-connected {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .badge-disconnected {
        background-color: #fef3c7;
        color: #92400e;
    }
    .edit-mode select {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        width: 100%;
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal.show {
        display: block;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 2% auto;
        padding: 0;
        border-radius: 0.5rem;
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .modal-header {
        padding: 1.5rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h2 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }
    .close {
        color: #6b7280;
        font-size: 2rem;
        font-weight: bold;
        line-height: 1;
        cursor: pointer;
        border: none;
        background: none;
    }
    .close:hover {
        color: #1f2937;
    }
    .modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .detail-item {
        padding: 0.75rem;
        background-color: #f9fafb;
        border-radius: 0.375rem;
    }
    .detail-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    .detail-value {
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 500;
    }
    .comments-section {
        border-top: 2px solid #e5e7eb;
        padding-top: 1.5rem;
    }
    .comments-section h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }
    .comment-form {
        margin-bottom: 1.5rem;
    }
    .comment-form textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        resize: vertical;
        min-height: 80px;
    }
    .comment-form textarea:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    .comment {
        background-color: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .comment-author {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.875rem;
    }
    .comment-date {
        font-size: 0.75rem;
        color: #6b7280;
    }
    .comment-content {
        color: #374151;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    .comment-actions {
        display: flex;
        gap: 0.75rem;
    }
    .comment-action-btn {
        font-size: 0.75rem;
        color: #4f46e5;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .comment-action-btn:hover {
        text-decoration: underline;
    }
    .comment-action-btn.delete {
        color: #dc2626;
    }
    .reply {
        margin-left: 2rem;
        margin-top: 0.75rem;
    }
    .reply-form {
        margin-left: 2rem;
        margin-top: 0.75rem;
    }
    .reply-form textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        min-height: 60px;
    }
    .mt-2 {
        margin-top: 0.5rem;
    }
</style>

<div class="header">Queue Sales Dashboard</div>

<!-- Date Filter -->
<div class="date-filter-container">
    <label class="date-filter-label">Filter by Date (NY Time):</label>
    <input type="date" 
           id="dateFilter" 
           class="date-filter-input" 
           value="{{ $selectedDate }}"
           max="{{ \Carbon\Carbon::now('America/New_York')->format('Y-m-d') }}">
    <button class="btn-filter" onclick="applyDateFilter()">Apply Filter</button>
    <button class="btn-filter btn-reset" onclick="resetDateFilter()">Reset to Today</button>
    <div class="timezone-info">
        📍 Displaying times in <strong>New York Time (NY)</strong><br>
        🗓️ Filtering records by <strong>New York Time (NY)</strong>
    </div>
</div>

<div class="dashboard">
    <div class="status-box status-queued">
        <h3>Queued</h3>
        <p>{{ $statusCounts['queued'] }}</p>
    </div>
    <div class="status-box status-assigned">
        <h3>Assigned</h3>
        <p>{{ $statusCounts['assigned'] }}</p>
    </div>
    <div class="status-box status-approved">
        <h3>Approved</h3>
        <p>{{ $statusCounts['approved'] }}</p>
    </div>
    <div class="status-box status-rejected">
        <h3>Rejected</h3>
        <p>{{ $statusCounts['rejected'] }}</p>
    </div>
</div>

<div class="table-container">
    <table class="table" id="queueSalesTable">
        <thead>
            <tr>
                <th class="sortable" data-column="id">ID</th>
                <th class="sortable" data-column="created_at">Created At (NY)</th>
                <th class="sortable" data-column="customer_full_name">Name</th>
                <th class="sortable" data-column="state">State</th>
                <th class="sortable" data-column="carrier">Carrier</th>
                <th class="sortable" data-column="client_name">Client</th>
                <th class="sortable" data-column="closer">Closer</th>
                <th class="sortable" data-column="validator">Validator</th>
                <th class="sortable" data-column="validator_updated_at">Assigned At (NY)</th>
                <th class="sortable" data-column="status">Status</th>
                <th class="sortable" data-column="status_updated_at">Status Updated (NY)</th>
                <th class="sortable" data-column="is_connected">Connected</th>
                <th class="sortable" data-column="connected_at">Connected At (NY)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($queueSales as $queueSale)
                <tr data-id="{{ $queueSale->id }}">
                    <td>{{ $queueSale->id }}</td>
                    <td>{{ $queueSale->created_at_ny }}</td>
                    <td>{{ $queueSale->customer_full_name ?? 'N/A' }}</td>
                    <td>{{ $queueSale->state ?? 'N/A' }}</td>
                    <td>{{ $queueSale->carrier ?? 'N/A' }}</td>
                    <td>{{ $queueSale->client_name ?? 'N/A' }}</td>
                    <td>{{ $queueSale->closedCall->closername ?? 'N/A' }}</td>
                    <td class="validator-cell">
                        <span class="validator-display">
                            @if($queueSale->validator)
                                {{ $queueSale->validator->code }} - {{ $queueSale->validator->name }}
                            @else
                                N/A
                            @endif
                        </span>
                        <select class="validator-select" style="display: none;">
                            <option value="">Select...</option>
                            @foreach ($validators as $validator)
                                <option value="{{ $validator->id }}" {{ $queueSale->validator_id == $validator->id ? 'selected' : '' }}>
                                    {{ $validator->code }} - {{ $validator->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>{{ $queueSale->validator_updated_at_ny ?? 'N/A' }}</td>
                    <td class="status-cell">
                        @php
                            if ($queueSale->status == 'approved') {
                                $display_status = 'approved';
                                $status_class = 'status-approved';
                            } elseif ($queueSale->status == 'rejected') {
                                $display_status = 'rejected';
                                $status_class = 'status-rejected';
                            } else {
                                $display_status = $queueSale->clients_id ? 'assigned' : 'queue';
                                $status_class = $queueSale->clients_id ? 'status-assigned' : 'status-queue';
                            }
                        @endphp
                        <span class="status-display status-badge {{ $status_class }}">{{ $display_status }}</span>
                        <select class="status-select" style="display: none;">
                            <option value="pending" {{ $queueSale->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $queueSale->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $queueSale->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </td>
                    <td>{{ $queueSale->status_updated_at_ny ?? 'N/A' }}</td>
                    <td class="connected-cell">
                        @php
                            if ($queueSale->is_connected === 1) {
                                $badge_class = 'badge-connected';
                                $badge_text = 'Connected';
                            } elseif ($queueSale->is_connected === 0 && $queueSale->connected_at) {
                                $badge_class = 'badge-disconnected';
                                $badge_text = 'Disconnected';
                            } else {
                                $badge_class = 'badge-not-connected';
                                $badge_text = 'Not Connected';
                            }
                        @endphp
                        <span class="status-badge {{ $badge_class }}">{{ $badge_text }}</span>
                    </td>
                    <td class="connected-at-cell">
                        {{ $queueSale->connected_at_ny ?? 'N/A' }}
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                            <button class="btn btn-success btn-sm save-btn" style="display: none;">Save</button>
                            <button class="btn btn-sm cancel-btn" style="display: none;">Cancel</button>
                            <button class="btn btn-info btn-sm connect-btn" data-connected="{{ $queueSale->is_connected }}">
                                @if($queueSale->is_connected === 1)
                                    Disconnect
                                @else
                                    Connect
                                @endif
                            </button>
                            <button class="btn btn-primary btn-sm view-btn">View</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" style="text-align: center; padding: 2rem; color: #6b7280;">
                        No records found for <strong>{{ $selectedDate }}</strong> (New York Time)
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination mt-3 d-flex justify-content-center">
    {{ $queueSales->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>

<!-- Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Queue Sale Details</h2>
            <button class="close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid" id="modalDetails"></div>
            
            <div class="comments-section">
                <h3>Comments</h3>
                
                <div class="comment-form">
                    <textarea id="newCommentText" placeholder="Add a comment..."></textarea>
                    <button class="btn btn-primary mt-2" id="submitComment">Post Comment</button>
                </div>
                
                <div id="commentsList"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Date filter functions
function applyDateFilter() {
    const selectedDate = document.getElementById('dateFilter').value;
    if (selectedDate) {
        const url = new URL(window.location.href);
        url.searchParams.set('date', selectedDate);
        url.searchParams.delete('page'); // Reset to page 1 when filtering
        window.location.href = url.toString();
    }
}

function resetDateFilter() {
    const url = new URL(window.location.href);
    url.searchParams.delete('date');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Table sorting
let currentSort = {
    column: null,
    direction: 'asc'
};

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('viewModal');
    const closeBtn = modal.querySelector('.close');
    const table = document.getElementById('queueSalesTable');
    let currentQueueSaleId = null;

    // Add sorting functionality
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.column;
            sortTable(column);
        });
    });

    function sortTable(column) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Toggle direction if same column, otherwise start with asc
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }

        // Update header styling
        document.querySelectorAll('.sortable').forEach(th => {
            th.classList.remove('asc', 'desc');
        });
        const activeHeader = document.querySelector(`[data-column="${column}"]`);
        activeHeader.classList.add(currentSort.direction);

        // Get column index
        const columnIndex = Array.from(activeHeader.parentElement.children).indexOf(activeHeader);

        // Sort rows
        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].textContent.trim();
            let bValue = b.cells[columnIndex].textContent.trim();

            // Handle N/A values
            if (aValue === 'N/A') return 1;
            if (bValue === 'N/A') return -1;

            // Try to parse as number
            const aNum = parseFloat(aValue);
            const bNum = parseFloat(bValue);
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return currentSort.direction === 'asc' ? aNum - bNum : bNum - aNum;
            }

            // String comparison
            return currentSort.direction === 'asc' 
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        });

        // Reorder table
        rows.forEach(row => tbody.appendChild(row));
    }

    // Edit functionality
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            enterEditMode(row);
        });
    });

    // Save functionality
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            saveRow(row);
        });
    });

    // Cancel functionality
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            exitEditMode(row);
        });
    });

    // Connect/Disconnect functionality
    document.querySelectorAll('.connect-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.dataset.id;
            toggleConnection(id, row);
        });
    });

    // View functionality
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.dataset.id;
            openModal(id);
        });
    });

    // Close modal
    closeBtn.addEventListener('click', function() {
        modal.classList.remove('show');
    });

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.classList.remove('show');
        }
    });

    // Submit comment
    document.getElementById('submitComment').addEventListener('click', function() {
        submitComment();
    });

    function enterEditMode(row) {
        row.querySelector('.validator-display').style.display = 'none';
        row.querySelector('.validator-select').style.display = 'block';
        row.querySelector('.status-display').style.display = 'none';
        row.querySelector('.status-select').style.display = 'block';
        row.querySelector('.edit-btn').style.display = 'none';
        row.querySelector('.save-btn').style.display = 'inline-flex';
        row.querySelector('.cancel-btn').style.display = 'inline-flex';
    }

    function exitEditMode(row) {
        row.querySelector('.validator-display').style.display = 'inline';
        row.querySelector('.validator-select').style.display = 'none';
        row.querySelector('.status-display').style.display = 'inline-flex';
        row.querySelector('.status-select').style.display = 'none';
        row.querySelector('.edit-btn').style.display = 'inline-flex';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    }

    function saveRow(row) {
        const id = row.dataset.id;
        const validatorId = row.querySelector('.validator-select').value;
        const status = row.querySelector('.status-select').value;

        if (!validatorId) {
            alert('Please select a validator');
            return;
        }

        fetch(`/queue-sales/${id}/inline-update`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                validator_id: validatorId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Record updated successfully');
                location.reload();
            } else {
                alert('Failed to update: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update record');
        });
    }

    function toggleConnection(id, row) {
        const connectBtn = row.querySelector('.connect-btn');
        const isConnected = connectBtn.dataset.connected === '1';

        fetch(`/queue-sales/${id}/toggle-connection`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = row.querySelector('.connected-cell .status-badge');
                const connectedAtCell = row.querySelector('.connected-at-cell');

                if (data.is_connected === 1) {
                    badge.className = 'status-badge badge-connected';
                    badge.textContent = 'Connected';
                    connectBtn.textContent = 'Disconnect';
                    connectBtn.dataset.connected = '1';
                    connectedAtCell.textContent = data.connected_at;
                } else if (data.is_connected === 0) {
                    badge.className = 'status-badge badge-disconnected';
                    badge.textContent = 'Disconnected';
                    connectBtn.textContent = 'Connect';
                    connectBtn.dataset.connected = '0';
                    connectedAtCell.textContent = data.disconnected_at;
                }
            } else {
                alert('Failed to toggle connection: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to toggle connection');
        });
    }

    function openModal(id) {
        currentQueueSaleId = id;
        
        fetch(`/queue-sales/${id}/show`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDetails(data.data);
                    displayComments(data.data.comments);
                    modal.classList.add('show');
                } else {
                    alert('Failed to load details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load details');
            });
    }

    function formatDateToNewYork(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString + ' UTC');
        return date.toLocaleString('en-US', { 
            timeZone: 'America/New_York',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    function displayDetails(queueSale) {
        const detailsHtml = `
            <div class="detail-item">
                <div class="detail-label">ID</div>
                <div class="detail-value">${queueSale.id}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created At (NY)</div>
                <div class="detail-value">${formatDateToNewYork(queueSale.created_at)}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Customer Name</div>
                <div class="detail-value">${queueSale.customer_full_name || 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">State</div>
                <div class="detail-value">${queueSale.state || 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Carrier</div>
                <div class="detail-value">${queueSale.carrier || 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Client</div>
                <div class="detail-value">${queueSale.client_name || 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Closer</div>
                <div class="detail-value">${queueSale.closed_call?.closername || 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Validator</div>
                <div class="detail-value">${queueSale.validator ? queueSale.validator.code + ' - ' + queueSale.validator.name : 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">${queueSale.status}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Connected Status</div>
                <div class="detail-value">${queueSale.is_connected === 1 ? 'Connected' : (queueSale.is_connected === 0 && queueSale.connected_at ? 'Disconnected' : 'Not Connected')}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Connection Timestamp (NY)</div>
                <div class="detail-value">${formatDateToNewYork(queueSale.connected_at)}</div>
            </div>
        `;
        
        document.getElementById('modalDetails').innerHTML = detailsHtml;
    }

    function displayComments(comments) {
        const commentsList = document.getElementById('commentsList');
        
        if (!comments || comments.length === 0) {
            commentsList.innerHTML = '<p style="color: #6b7280; font-style: italic;">No comments yet.</p>';
            return;
        }

        let html = '';
        comments.forEach(comment => {
            html += renderComment(comment);
        });
        
        commentsList.innerHTML = html;
        attachCommentEventListeners();
    }

    function renderComment(comment, isReply = false) {
        const commentDate = new Date(comment.created_at + ' UTC').toLocaleString('en-US', {
            timeZone: 'America/New_York',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });

        let html = `
            <div class="comment ${isReply ? 'reply' : ''}" data-comment-id="${comment.id}">
                <div class="comment-header">
                    <span class="comment-author">${comment.user.name}</span>
                    <span class="comment-date">${commentDate} NY</span>
                </div>
                <div class="comment-content">${escapeHtml(comment.content)}</div>
                <div class="comment-actions">
                    <button class="comment-action-btn reply-btn" data-comment-id="${comment.id}">Reply</button>
                    <button class="comment-action-btn delete delete-btn" data-comment-id="${comment.id}">Delete</button>
                </div>
                <div class="reply-form-container" id="reply-form-${comment.id}"></div>
        `;

        if (comment.replies && comment.replies.length > 0) {
            comment.replies.forEach(reply => {
                html += renderComment(reply, true);
            });
        }

        html += `</div>`;
        return html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function attachCommentEventListeners() {
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                showReplyForm(commentId);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                deleteComment(commentId);
            });
        });
    }

    function showReplyForm(commentId) {
        const container = document.getElementById(`reply-form-${commentId}`);
        
        if (container.innerHTML.trim() !== '') {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = `
            <div class="reply-form">
                <textarea id="reply-text-${commentId}" placeholder="Write a reply..."></textarea>
                <button class="btn btn-primary btn-sm mt-2" onclick="submitReply(${commentId})">Post Reply</button>
                <button class="btn btn-sm mt-2" onclick="cancelReply(${commentId})">Cancel</button>
            </div>
        `;
    }

    window.submitReply = function(parentId) {
        const content = document.getElementById(`reply-text-${parentId}`).value;
        
        if (!content.trim()) {
            alert('Please write a reply');
            return;
        }

        fetch(`/queue-sales/${currentQueueSaleId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: content,
                parent_id: parentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openModal(currentQueueSaleId);
            } else {
                alert('Failed to post reply: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to post reply');
        });
    };

    window.cancelReply = function(commentId) {
        document.getElementById(`reply-form-${commentId}`).innerHTML = '';
    };

    function submitComment() {
        const content = document.getElementById('newCommentText').value;
        
        if (!content.trim()) {
            alert('Please write a comment');
            return;
        }

        fetch(`/queue-sales/${currentQueueSaleId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: content,
                parent_id: null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('newCommentText').value = '';
                openModal(currentQueueSaleId);
            } else {
                alert('Failed to post comment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to post comment');
        });
    }

    function deleteComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) {
            return;
        }

        fetch(`/queue-sales/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openModal(currentQueueSaleId);
            } else {
                alert('Failed to delete comment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete comment');
        });
    }
});
</script>

@endsection