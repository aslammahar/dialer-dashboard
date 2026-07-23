{{-- resources/views/reporting/index.blade.php - COMPLETE WITH ENHANCED FULLSCREEN --}}

@extends('layouts.admin')

@section('title', 'Reporting Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Reporting Dashboard</h3>
                    <div class="btn-group">
                        <a href="{{ route('reporting.upload.form') }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Excel
                        </a>
                        <a href="{{ route('reporting.export', ['date' => $selectedDate, 'view_type' => $viewType, 'month' => $selectedMonth]) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export Excel
                        </a>
                        @can('export closed calls')
                            <a href="{{ route('reporting.closed-calls.export.form') }}" class="btn btn-outline-success">
                                <i class="fas fa-file-excel"></i> Closed Calls Export
                            </a>
                        @endcan
                        <button type="button" class="btn btn-info" onclick="refreshData()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-warning" onclick="toggleFullscreen()" id="fullscreenBtn" title="Enter true fullscreen mode">
                            <i class="fas fa-expand"></i> Fullscreen
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="filterForm" method="GET" action="{{ route('reporting.index') }}">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="viewType" class="form-label">View Type:</label>
                                <select name="view_type" id="viewType" class="form-select" onchange="toggleViewControls()">
                                    <option value="monthly" {{ $viewType == 'monthly' ? 'selected' : '' }}>Monthly View</option>
                                    <option value="daily" {{ $viewType == 'daily' ? 'selected' : '' }}>Daily View</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="monthFilter" class="form-label">Select Month:</label>
                                <select name="month" id="monthFilter" class="form-select" onchange="updateDateOptions()">
                                    <option value="{{ date('Y-m') }}" {{ $selectedMonth == date('Y-m') ? 'selected' : '' }}>
                                        This Month ({{ date('F Y') }})
                                    </option>
                                    @foreach($availableMonths as $month)
                                        @if($month != date('Y-m'))
                                            <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                                {{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2" id="dateControls" style="{{ $viewType == 'daily' ? '' : 'display: none;' }}">
                                <label for="dateFilter" class="form-label">Date:</label>
                                <select name="date" id="dateFilter" class="form-select">
                                    <option value="all" {{ request('show_all_dates') || $selectedDate == 'all' ? 'selected' : '' }}>All Dates</option>
                                    @foreach($monthDates as $date)
                                        <option value="{{ $date }}" {{ $selectedDate == $date && $selectedDate != 'all' ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::parse($date)->format('M j, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">
                                    <span id="viewTypeLabel">
                                        @if($viewType == 'monthly')
                                            Monthly Stats ({{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }}):
                                        @elseif(request('show_all_dates') || $selectedDate == 'all')
                                            Monthly Stats ({{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} - All Dates):
                                        @else
                                            @if($selectedDate && $selectedDate != 'all')
                                                Daily Stats ({{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}):
                                            @else
                                                Monthly Stats ({{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} - All Dates):
                                            @endif
                                        @endif
                                    </span>
                                </label>
                                <div id="quickStats" class="d-flex gap-2 flex-wrap">
                                    <span class="badge bg-primary">Loading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="show_all_dates" id="showAllDates" value="{{ request('show_all_dates') ? '1' : '0' }}">
                    </form>

                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>
                                    @if($viewType == 'monthly')
                                        Monthly View - {{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }}
                                        <small class="text-muted">(Records from this month only)</small>
                                    @elseif(request('show_all_dates') || $selectedDate == 'all')
                                        Monthly View - {{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} (All Dates)
                                        <small class="text-muted">(All records from this month)</small>
                                    @else
                                        @if($selectedDate && $selectedDate != 'all')
                                            Daily View - {{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                                            <small class="text-muted">(Records for this specific date only)</small>
                                        @else
                                            Monthly View - {{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} (All Dates)
                                            <small class="text-muted">(All records from this month)</small>
                                        @endif
                                    @endif
                                </strong>
                                @if(isset($reportingData) && count($reportingData) > 0)
                                    <span class="badge bg-secondary ms-2">{{ count($reportingData) }} records found</span>
                                @endif
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> Office Hours: 07:45 AM onwards | 
                                        <i class="fas fa-exclamation-triangle text-warning"></i> Late minutes calculated from 07:45 AM | 
                                        <i class="fas fa-sort"></i> Click headers to sort (except Names) |
                                        <i class="fas fa-expand"></i> Press F11 or click fullscreen for best view
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box Size Info -->
                    <div class="box-info mb-2">
                        <i class="fas fa-info-circle"></i>
                        <strong>Table Box:</strong> Height = 70% of screen | 
                        <span id="boxDimensions">Calculating...</span> |
                        <strong>Scrolling:</strong> Horizontal + Vertical enabled |
                        <strong>Fullscreen:</strong> <span id="fullscreenSupport">Checking...</span>
                    </div>

                    <!-- Table Container with Fixed Header -->
                    <div class="table-container">
                        <div class="table-wrapper">
                            <table id="reportingTable" class="table table-bordered table-striped align-middle sticky-header-table">
                                <thead class="sticky-header">
                                    <tr>
                                        <th class="names-column">Names</th>
                                        <th class="sortable-header" data-column="working_days">
                                            Working Days <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="late-minutes-column sortable-header" data-column="late_minutes">
                                            Late Minutes <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="talktime">
                                            Talktime <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="avg_talktime">
                                            Avg Talktime <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="total_calls">
                                            Total Avatar/JCs XFERS <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="total_submitted">
                                            TOTAL Submitted Sales <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="underwriting">
                                            Underwriting/HO <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="total_approved">
                                            TOTAL Approved <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="average_approved">
                                            Average Approved <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="premium_spd">
                                            Premium Approved SPD <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="total_conv_calls">
                                            TOTAL Conv% (Calls/Submission) <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="total_conv_approved">
                                            TOTAL Conv% (Approved/Submission) <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="avatar_xfer">
                                            Avatar Xfer <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="avatar_submitted">
                                            Avatar Xfer Submitted Sales <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="avatar_approved">
                                            Avatar Xfer approved Sales <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="avatar_conv_calls">
                                            Avatar Xfer Conv% (Calls/Submission) <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="avatar_conv_approved">
                                            Avatar Xfer Conv% (Approved/Submission) <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="jcs_xfers">
                                            JCs Xfers <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="jcs_submitted">
                                            JCs Submitted <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="jcs_approved">
                                            JCs Approved <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="jcs_conv_calls">
                                            JCs Conv% (Calls/Submission) <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="jcs_conv_approved">
                                            JCs Conv% (Approved/Submission) <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="calls_200">
                                            Calls Dur Less Than 200 secs <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="calls_200_400">
                                            Between 200 secs & 400 secs <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        <th class="sortable-header" data-column="calls_400">
                                            Calls Dur Greater Than 400 secs <i class="fas fa-sort sort-icon"></i>
                                        </th>
                                        @if($viewType == 'daily')
                                            <th>Rec 1 200Sec Duration</th>
                                            <th>Rec 2 400 sec Duration</th>
                                            <th>Rec 3 600 Sec Duration</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportingData as $data)
                                        @php
                                            // Calculate conversion percentages
                                            $avatarXfer = $data['Avatar Xfer'] ?? 0;
                                            $avatarSubmitted = $data['Avatar Xfer Submitted Sales'] ?? 0;
                                            $avatarApproved = $data['Avatar Xfer approved Sales'] ?? 0;
                                            $jcsXfers = $data['JCs Xfers'] ?? 0;
                                            $jcsSubmitted = $data['JCs Submitted'] ?? 0;
                                            $jcsApproved = $data['JCs Approved'] ?? 0;
                                            $totalApproved = $data['TOTAL Approved'] ?? 0;
                                            $totalSubmitted = $data['TOTAL Submitted Sales'] ?? 0;
                                            $workingDays = $data['Working Days'] ?? 1;
                                            
                                            // Avatar conversions
                                            $avatarCallsConv = $avatarSubmitted > 0 ? round(($avatarXfer / $avatarSubmitted) * 100, 2) : 0;
                                            $avatarApprovedConv = $avatarSubmitted > 0 ? round(($avatarApproved / $avatarSubmitted) * 100, 2) : 0;
                                            
                                            // JCs conversions
                                            $jcsCallsConv = $jcsSubmitted > 0 ? round(($jcsXfers / $jcsSubmitted) * 100, 2) : 0;
                                            $jcsApprovedConv = $jcsSubmitted > 0 ? round(($jcsApproved / $jcsSubmitted) * 100, 2) : 0;
                                            
                                            // Premium Approved SPD
                                            $premiumApprovedSpd = $workingDays > 0 ? round($totalApproved / $workingDays, 2) : 0;
                                            
                                            // TOTAL Conv% (Approved/Submission) - from backend calculation
                                            $totalApprovedConv = $data['TOTAL Conv% (Approved/Submission)'] ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="names-column"><strong>{{ $data['Names'] }}</strong></td>
                                            <td><span class="badge bg-info">{{ $data['Working Days'] ?: '0' }}</span></td>
                                            <td class="late-minutes-column">
                                                @php
                                                    $lateMinutes = $data['Late Min'] ?? 0;
                                                    $lateMinutes = is_numeric($lateMinutes) ? (int)$lateMinutes : 0;
                                                    if ($lateMinutes == 0) {
                                                        $badgeClass = 'bg-success';
                                                        $icon = 'fa-check';
                                                    } elseif ($lateMinutes <= 15) {
                                                        $badgeClass = 'bg-warning text-dark';
                                                        $icon = 'fa-clock';
                                                    } elseif ($lateMinutes <= 30) {
                                                        $badgeClass = 'bg-warning';
                                                        $icon = 'fa-exclamation-triangle';
                                                    } elseif ($lateMinutes < 60) {
                                                        $badgeClass = 'bg-danger';
                                                        $icon = 'fa-times-circle';
                                                    } else {
                                                        $badgeClass = 'bg-dark';
                                                        $icon = 'fa-ban';
                                                    }
                                                    if ($lateMinutes < 60) {
                                                        $displayText = $lateMinutes . ' min';
                                                    } else {
                                                        $hours = floor($lateMinutes / 60);
                                                        $mins = $lateMinutes % 60;
                                                        $displayText = $hours . 'h' . ($mins > 0 ? ' ' . $mins . 'min' : '');
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}" title="Late minutes: {{ $lateMinutes }}{{ $viewType == 'monthly' ? ' (total this month)' : ' (today)' }} | Office time: 07:45 AM">
                                                    <i class="fas {{ $icon }}"></i> {{ $displayText }}
                                                </span>
                                            </td>
                                            <td>{{ $data['Talktime'] ?: '0:00:00' }}</td>
                                            <td>{{ $data['Avg Talktime'] ?: '0:00:00' }}</td>
                                            <td><strong class="text-primary">{{ $data['Total Avatar/JCs XFERS'] ?: '0' }}</strong></td>
                                            <td><span class="badge bg-secondary">{{ $data['TOTAL Submitted Sales'] ?: '0' }}</span></td>
                                            <td>{{ $data['Underwriting/HO'] ?: '0' }}</td>
                                            <td><strong class="text-success">{{ $data['TOTAL Approved'] ?: '0' }}</strong></td>
                                            <td>{{ $data['Average Approved'] ?: '0' }}</td>
                                            <td><strong class="text-warning">{{ $premiumApprovedSpd }}</strong></td>
                                            <td>{{ $data['TOTAL Conv% (Calls/Submission)'] ?: '0' }}%</td>
                                            <td><strong class="text-primary">{{ $totalApprovedConv }}%</strong></td>
                                            <td><strong class="text-success">{{ $data['Avatar Xfer'] ?: '0' }}</strong></td>
                                            <td>{{ $data['Avatar Xfer Submitted Sales'] ?: '0' }}</td>
                                            <td>{{ $data['Avatar Xfer approved Sales'] ?: '0' }}</td>
                                            <td><span class="badge bg-info">{{ $avatarCallsConv }}%</span></td>
                                            <td><span class="badge bg-success">{{ $avatarApprovedConv }}%</span></td>
                                            <td>{{ $data['JCs Xfers'] ?: '0' }}</td>
                                            <td>{{ $data['JCs Submitted'] ?: '0' }}</td>
                                            <td>{{ $data['JCs Approved'] ?: '0' }}</td>
                                            <td><span class="badge bg-info">{{ $jcsCallsConv }}%</span></td>
                                            <td><span class="badge bg-success">{{ $jcsApprovedConv }}%</span></td>
                                            <td>
                                                @if($data['Calls Dur Less Than 200 secs'] > 0)
                                                    <span class="badge bg-info">{{ $data['Calls Dur Less Than 200 secs'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data['Between 200 secs & 400 secs'] > 0)
                                                    <span class="badge bg-warning text-dark">{{ $data['Between 200 secs & 400 secs'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data['Calls Dur Greater Than 400 secs'] > 0)
                                                    <span class="badge bg-success">{{ $data['Calls Dur Greater Than 400 secs'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            @if($viewType == 'daily')
                                                <td class="recording-column">
                                                    @if(isset($data['Rec 1 200Sec Duration']) && !empty($data['Rec 1 200Sec Duration']) && $data['Rec 1 200Sec Duration'] !== 'N/A')
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ $data['Rec 1 200Sec Duration'] }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1" title="Play &lt;200s Recording">
                                                                <i class="fas fa-play"></i> Play
                                                            </a>
                                                            <small class="text-muted text-truncate" style="max-width: 120px;" title="{{ $data['Rec 1 200Sec Duration'] }}">
                                                                {{ Str::limit($data['Rec 1 200Sec Duration'], 15) }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No Recording</span>
                                                    @endif
                                                </td>
                                                <td class="recording-column">
                                                    @if(isset($data['Rec 2 400 sec Duration']) && !empty($data['Rec 2 400 sec Duration']) && $data['Rec 2 400 sec Duration'] !== 'N/A')
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ $data['Rec 2 400 sec Duration'] }}" target="_blank" class="btn btn-sm btn-outline-warning mb-1" title="Play 200-400s Recording">
                                                                <i class="fas fa-play"></i> Play
                                                            </a>
                                                            <small class="text-muted text-truncate" style="max-width: 120px;" title="{{ $data['Rec 2 400 sec Duration'] }}">
                                                                {{ Str::limit($data['Rec 2 400 sec Duration'], 15) }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No Recording</span>
                                                    @endif
                                                </td>
                                                <td class="recording-column">
                                                    @if(isset($data['Rec 3 600 Sec Duration']) && !empty($data['Rec 3 600 Sec Duration']) && $data['Rec 3 600 Sec Duration'] !== 'N/A')
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ $data['Rec 3 600 Sec Duration'] }}" target="_blank" class="btn btn-sm btn-outline-success mb-1" title="Play &gt;400s Recording">
                                                                <i class="fas fa-play"></i> Play
                                                            </a>
                                                            <small class="text-muted text-truncate" style="max-width: 120px;" title="{{ $data['Rec 3 600 Sec Duration'] }}">
                                                                {{ Str::limit($data['Rec 3 600 Sec Duration'], 15) }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No Recording</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $viewType == 'daily' ? '29' : '26' }}" class="text-center py-4">
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>No data found for 
                                                    @if($viewType == 'monthly')
                                                        {{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }}
                                                    @elseif(request('show_all_dates') || $selectedDate == 'all')
                                                        {{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} (All Dates)
                                                    @else
                                                        @if($selectedDate && $selectedDate != 'all')
                                                            {{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                                                        @else
                                                            {{ Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} (All Dates)
                                                        @endif
                                                    @endif
                                                    </strong>
                                                    <br><small class="text-muted">Try uploading data for this period or selecting a different time range.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <span class="text-muted small">
                                            <i class="fas fa-info-circle"></i> Late Minutes Legend:
                                        </span>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> 0 min (On Time)
                                        </span>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock"></i> 1-15 min
                                        </span>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 16-30 min
                                        </span>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i> 31-59 min
                                        </span>
                                        <span class="badge bg-dark">
                                            <i class="fas fa-ban"></i> 60+ min
                                        </span>
                                        <span class="text-muted small ms-auto">
                                            @if($viewType == 'monthly')
                                                <i class="fas fa-calendar-alt"></i> Monthly totals | Office Hours: 7:45 AM | Click headers to sort
                                            @else
                                                <i class="fas fa-calendar-day"></i> Daily totals | Office Hours: 7:45 AM | Click headers to sort
                                            @endif
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="fas fa-calculator"></i> 
                                            <strong>Conversion Formulas:</strong> 
                                            Avatar Conv% (Calls/Sub) = Avatar Xfer ÷ Avatar Submitted × 100 | 
                                            Avatar Conv% (App/Sub) = Avatar Approved ÷ Avatar Submitted × 100 | 
                                            JCs Conv% (Calls/Sub) = JCs Xfers ÷ JCs Submitted × 100 | 
                                            JCs Conv% (App/Sub) = JCs Approved ÷ JCs Submitted × 100 | 
                                            Premium SPD = Total Approved ÷ Working Days
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Box Info */
    .box-info {
        padding: 8px 12px;
        background: #e9ecef;
        border-radius: 4px;
        font-size: 14px;
        border-left: 4px solid #007bff;
    }

    /* Enhanced Fullscreen CSS */
    body.fullscreen-active {
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }

    /* True fullscreen mode styles */
    .fullscreen-mode {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999 !important;
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .fullscreen-mode .card {
        height: 100vh !important;
        margin: 0 !important;
        border-radius: 0 !important;
        border: none !important;
    }

    .fullscreen-mode .card-header {
        padding: 8px 15px !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    .fullscreen-mode .card-body {
        height: calc(100vh - 60px) !important;
        padding: 0 !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .fullscreen-mode .table-container {
        max-height: 100vh !important;
        height: 100vh !important;
        flex: 1 !important;
        margin: 0 !important;
        border: none !important;
        border-radius: 0 !important;
    }

    /* Hide everything except table in fullscreen */
    .fullscreen-mode .row.mb-3,
    .fullscreen-mode .row.mb-2,
    .fullscreen-mode .box-info,
    .fullscreen-mode .row.mt-3 {
        display: none !important;
    }

    .fullscreen-mode .container-fluid {
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }

    /* Fullscreen exit button positioning */
    .fullscreen-exit-btn {
        position: fixed !important;
        top: 15px !important;
        right: 15px !important;
        z-index: 10000 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
    }

    /* Fullscreen instructions */
    .fullscreen-instructions {
        position: fixed;
        top: 60px;
        right: 15px;
        z-index: 10001;
        background: rgba(0, 123, 255, 0.95);
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        animation: slideInFromRight 0.3s ease-out;
        max-width: 300px;
    }

    .fullscreen-instructions .instruction-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fullscreen-instructions kbd {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }

    .fullscreen-instructions .btn-close-instruction {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.2s;
    }

    .fullscreen-instructions .btn-close-instruction:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Animation for instructions */
    @keyframes slideInFromRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Fullscreen table optimizations */
    .fullscreen-mode .sticky-header-table {
        font-size: 13px;
    }

    .fullscreen-mode .sticky-header-table thead.sticky-header th {
        padding: 8px 6px;
        font-size: 12px;
    }

    .fullscreen-mode .sticky-header-table tbody td {
        padding: 6px 4px;
        font-size: 12px;
    }

    .fullscreen-mode .names-column {
        min-width: 160px !important;
        max-width: 160px !important;
    }

    .fullscreen-mode .late-minutes-column {
        min-width: 120px !important;
        max-width: 140px !important;
    }

    .fullscreen-mode .recording-column {
        min-width: 130px !important;
        max-width: 150px !important;
    }

    /* Enhanced scrollbar for fullscreen */
    .fullscreen-mode .table-container::-webkit-scrollbar {
        width: 14px;
        height: 14px;
    }

    .fullscreen-mode .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 7px;
    }

    .fullscreen-mode .table-container::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border-radius: 7px;
        border: 2px solid #f1f1f1;
    }

    .fullscreen-mode .table-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
    }

    .fullscreen-mode .table-container::-webkit-scrollbar-corner {
        background: #f1f1f1;
    }

    /* Fullscreen header adjustments */
    .fullscreen-mode .card-title {
        font-size: 18px;
        margin: 0;
    }

    .fullscreen-mode .btn-group .btn {
        padding: 4px 8px;
        font-size: 13px;
    }

    /* Fullscreen filter form adjustments */
    .fullscreen-mode .form-select,
    .fullscreen-mode .form-label {
        font-size: 13px;
    }

    .fullscreen-mode .alert {
        padding: 8px 12px;
        margin-bottom: 10px;
    }

    .fullscreen-mode .box-info {
        padding: 6px 10px;
        font-size: 13px;
        margin-bottom: 10px;
    }

    /* Hide elements in fullscreen that take up space */
    .fullscreen-mode .row.mt-3 {
        margin-top: 10px !important;
    }

    /* Browser compatibility notices */
    .fullscreen-not-supported {
        background: #fff3cd;
        color: #856404;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid #ffeaa7;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .fullscreen-not-supported i {
        color: #f39c12;
        margin-right: 5px;
    }

    /* Sortable Header Styling - FIXED */
    .sortable-header {
        cursor: pointer !important;
        user-select: none !important;
        position: relative !important;
        transition: background-color 0.2s ease !important;
    }

    .sortable-header:hover {
        background-color: #2a2a2a !important;
        cursor: pointer !important;
    }

    .sortable-header.active {
        background-color: #007bff !important;
    }

    .sort-icon {
        margin-left: 5px !important;
        font-size: 0.8rem !important;
        opacity: 0.7 !important;
        transition: all 0.2s ease !important;
        color: #ffffff !important;
    }

    .sortable-header:hover .sort-icon {
        opacity: 1 !important;
        color: #ffffff !important;
    }

    .sortable-header.active .sort-icon {
        opacity: 1 !important;
        color: #ffffff !important;
    }

    .sortable-header.asc .sort-icon:before {
        content: '\f0de' !important; /* fa-sort-up */
    }

    .sortable-header.desc .sort-icon:before {
        content: '\f0dd' !important; /* fa-sort-down */
    }

    /* Force clickable appearance */
    .sticky-header-table thead.sticky-header th.sortable-header {
        cursor: pointer !important;
    }

    .sticky-header-table thead.sticky-header th.sortable-header:hover {
        background-color: #2a2a2a !important;
        cursor: pointer !important;
    }

    /* Table Container - ENABLE HORIZONTAL SCROLL */
    .table-container {
        position: relative;
        max-height: 70vh;
        overflow: auto; /* Both horizontal and vertical scroll */
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: white;
    }

    .table-wrapper {
        position: relative;
        min-height: 200px;
        width: max-content; /* Force horizontal scroll */
        min-width: 100%;
    }

    /* Table - FORCE WIDE TABLE */
    .sticky-header-table {
        margin-bottom: 0;
        width: max-content; /* Make table wider than container */
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .sticky-header-table thead.sticky-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        font-weight: bold !important;
    }

    .sticky-header-table thead.sticky-header th {
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        font-weight: bold !important;
        border-top: none;
        border-bottom: 2px solid #000;
        padding: 12px 8px;
        white-space: nowrap;
        min-width: 120px;
    }

    /* Names Column - STICKY LEFT COLUMN */
    .names-column {
        position: sticky !important;
        left: 0 !important;
        z-index: 11 !important;
        min-width: 180px !important;
        max-width: 180px !important;
        white-space: nowrap !important;
        text-align: left !important;
        vertical-align: middle !important;
        background-color: #f8f9fa !important;
        border-right: 2px solid #007bff !important;
        padding-left: 15px !important;
        font-weight: bold !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1) !important;
    }

    /* Names Column Header - Higher Z-Index */
    .sticky-header .names-column {
        z-index: 12 !important;
        background-color: #1a1a1a !important;
        color: #ffffff !important;
    }

    /* Late Minutes Column */
    .late-minutes-column {
        min-width: 140px !important;
        max-width: 160px !important;
        white-space: nowrap !important;
        text-align: center !important;
        vertical-align: middle !important;
        background-color: #fff3cd !important;
        border-right: 2px solid #ffc107 !important;
    }

    /* Table Body Styles */
    .sticky-header-table tbody tr {
        background-color: white;
        height: auto !important;
    }

    .sticky-header-table tbody tr:hover {
        background-color: #f0f0f0 !important;
    }

    /* Enhanced Hover Effects for Fixed Column */
    .sticky-header-table tbody tr:hover .names-column {
        background-color: #e3f2fd !important;
        box-shadow: 3px 0 8px rgba(0,0,0,0.15) !important;
    }

    .sticky-header-table tbody tr:hover .late-minutes-column {
        background-color: #fff8e1 !important;
    }

    .sticky-header-table tbody td {
        padding: 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    /* Scrollbar Styling - ENHANCED FOR BOTH DIRECTIONS */
    .table-container::-webkit-scrollbar {
        width: 12px;
        height: 12px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 6px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .table-container::-webkit-scrollbar-corner {
        background: #f1f1f1;
    }

    /* Badge Styling */
    .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 12px;
    }

    .badge.bg-success {
        background-color: #28a745 !important;
        color: white;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .badge.bg-warning.text-dark {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
        color: white;
    }

    .badge.bg-dark {
        background-color: #343a40 !important;
        color: white;
    }

    .badge.bg-info {
        background-color: #17a2b8 !important;
        color: white;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
        color: white;
    }

    /* Button Styling */
    .btn-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
        border-radius: 0.2rem;
    }

    /* Text Colors */
    .text-primary {
        color: #007bff !important;
        font-weight: bold;
    }

    .text-success {
        color: #28a745 !important;
        font-weight: bold;
    }

    .text-warning {
        color: #ffc107 !important;
        font-weight: bold;
    }

    /* Recording Column Styling - Only for Daily View */
    .recording-column {
        min-width: 140px !important;
        max-width: 160px !important;
        white-space: nowrap !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .recording-column .btn {
        margin-bottom: 2px;
    }

    .recording-column small {
        font-size: 0.65rem;
        line-height: 1.1;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Loading State */
    body.loading::after {
        content: "Loading...";
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 15px 25px;
        border-radius: 8px;
        font-weight: bold;
        color: #007bff;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Fullscreen loading overlay */
    body.fullscreen-active.loading::after {
        background: rgba(0, 0, 0, 0.8);
        color: white;
        font-size: 18px;
        padding: 20px 30px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table-container {
            max-height: 60vh;
        }
        
        .sticky-header-table thead.sticky-header th {
            min-width: 100px;
            font-size: 0.875rem;
            padding: 8px 4px;
        }
        
        .sticky-header-table tbody td {
            font-size: 0.875rem;
            padding: 6px 4px;
            max-width: 150px;
        }

        .names-column {
            min-width: 120px !important;
            max-width: 140px !important;
        }

        .late-minutes-column {
            min-width: 120px !important;
            max-width: 140px !important;
        }

        .recording-column {
            min-width: 120px !important;
            max-width: 140px !important;
        }
        
        .recording-column small {
            font-size: 0.6rem;
        }

        .fullscreen-mode .sticky-header-table {
            font-size: 11px;
        }
        
        .fullscreen-mode .sticky-header-table thead.sticky-header th {
            padding: 6px 3px;
            font-size: 10px;
            min-width: 80px;
        }
        
        .fullscreen-mode .sticky-header-table tbody td {
            padding: 4px 2px;
            font-size: 10px;
        }
        
        .fullscreen-mode .names-column {
            min-width: 100px !important;
            max-width: 120px !important;
        }
        
        .fullscreen-mode .late-minutes-column {
            min-width: 100px !important;
            max-width: 120px !important;
        }
        
        .fullscreen-instructions {
            right: 10px;
            top: 50px;
            max-width: 250px;
            font-size: 12px;
            padding: 8px 12px;
        }
        
        .fullscreen-exit-btn {
            top: 10px !important;
            right: 10px !important;
            padding: 4px 8px !important;
            font-size: 12px !important;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Global variables for sorting and fullscreen
let currentSortColumn = null;
let currentSortDirection = null;
let originalTableData = [];
let isFullscreen = false;

$(document).ready(function() {
    console.log('Document ready - initializing...');
    updateBoxDimensions();
    loadQuickStats();
    checkFullscreenSupport();
    
    // Initialize sorting immediately
    initializeSorting();
    
    $(window).resize(function() {
        updateBoxDimensions();
    });
});

// ============ ENHANCED FULLSCREEN FUNCTIONALITY ============

function toggleFullscreen() {
    if (isFullscreen) {
        exitFullscreen();
    } else {
        enterFullscreen();
    }
}

function enterFullscreen() {
    const container = document.querySelector('.container-fluid');
    const btn = document.getElementById('fullscreenBtn');
    
    // Check if Fullscreen API is supported
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen()
            .then(() => {
                activateFullscreenMode(container, btn);
            })
            .catch((err) => {
                console.log('Fullscreen API failed, using fallback:', err);
                activateFullscreenMode(container, btn);
            });
    } else if (document.documentElement.webkitRequestFullscreen) {
        // Safari support
        document.documentElement.webkitRequestFullscreen();
        activateFullscreenMode(container, btn);
    } else if (document.documentElement.msRequestFullscreen) {
        // IE/Edge support
        document.documentElement.msRequestFullscreen();
        activateFullscreenMode(container, btn);
    } else {
        // Fallback to browser-only fullscreen
        console.log('Fullscreen API not supported, using browser fullscreen');
        activateFullscreenMode(container, btn);
    }
}

function exitFullscreen() {
    const container = document.querySelector('.container-fluid');
    const btn = document.getElementById('fullscreenBtn');
    
    // Exit browser fullscreen if active
    if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
        if (document.exitFullscreen) {
            document.exitFullscreen()
                .then(() => {
                    deactivateFullscreenMode(container, btn);
                })
                .catch((err) => {
                    console.log('Exit fullscreen failed:', err);
                    deactivateFullscreenMode(container, btn);
                });
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
            deactivateFullscreenMode(container, btn);
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
            deactivateFullscreenMode(container, btn);
        }
    } else {
        deactivateFullscreenMode(container, btn);
    }
}

function activateFullscreenMode(container, btn) {
    isFullscreen = true;
    container.classList.add('fullscreen-mode');
    btn.innerHTML = '<i class="fas fa-compress"></i> Exit Fullscreen (ESC)';
    btn.classList.remove('btn-warning');
    btn.classList.add('btn-danger', 'fullscreen-exit-btn');
    
    // Hide body scrollbar
    document.body.style.overflow = 'hidden';
    
    // Add fullscreen-specific styles
    document.body.classList.add('fullscreen-active');
    
    // Update table dimensions
    setTimeout(updateBoxDimensions, 100);
    
    // Show instructions
    showFullscreenInstructions();
}

function deactivateFullscreenMode(container, btn) {
    isFullscreen = false;
    container.classList.remove('fullscreen-mode');
    btn.innerHTML = '<i class="fas fa-expand"></i> Fullscreen';
    btn.classList.remove('btn-danger', 'fullscreen-exit-btn');
    btn.classList.add('btn-warning');
    
    // Restore body scrollbar
    document.body.style.overflow = '';
    
    // Remove fullscreen-specific styles
    document.body.classList.remove('fullscreen-active');
    
    // Update table dimensions
    setTimeout(updateBoxDimensions, 100);
    
    // Hide instructions
    hideFullscreenInstructions();
}

function showFullscreenInstructions() {
    // Remove existing instructions if any
    hideFullscreenInstructions();
    
    const instructions = document.createElement('div');
    instructions.id = 'fullscreen-instructions';
    instructions.className = 'fullscreen-instructions';
    instructions.innerHTML = `
        <div class="instruction-content">
            <i class="fas fa-info-circle"></i>
            <span>Press <kbd>ESC</kbd> or click the exit button to exit fullscreen</span>
            <button type="button" class="btn-close-instruction" onclick="hideFullscreenInstructions()">×</button>
        </div>
    `;
    
    document.body.appendChild(instructions);
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        hideFullscreenInstructions();
    }, 3000);
}

function hideFullscreenInstructions() {
    const instructions = document.getElementById('fullscreen-instructions');
    if (instructions) {
        instructions.remove();
    }
}

// Enhanced escape key listener
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (isFullscreen) {
            exitFullscreen();
        }
    }
    // Additional keyboard shortcuts
    if (e.key === 'F11') {
        e.preventDefault();
        toggleFullscreen();
    }
});

// Listen for fullscreen change events
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);
document.addEventListener('MSFullscreenChange', handleFullscreenChange);

function handleFullscreenChange() {
    const container = document.querySelector('.container-fluid');
    const btn = document.getElementById('fullscreenBtn');
    
    // Check if we're actually in fullscreen mode
    const isInFullscreen = !!(document.fullscreenElement || 
                            document.webkitFullscreenElement || 
                            document.mozFullScreenElement ||
                            document.msFullscreenElement);
    
    if (!isInFullscreen && isFullscreen) {
        // User exited fullscreen using ESC or browser controls
        deactivateFullscreenMode(container, btn);
    }
}

// Detect fullscreen support and update button accordingly
function checkFullscreenSupport() {
    const btn = document.getElementById('fullscreenBtn');
    const supportInfo = document.getElementById('fullscreenSupport');
    if (!btn || !supportInfo) return;
    
    const hasFullscreenAPI = !!(document.documentElement.requestFullscreen || 
                               document.documentElement.webkitRequestFullscreen || 
                               document.documentElement.msRequestFullscreen);
    
    if (hasFullscreenAPI) {
        btn.title = 'Enter true fullscreen mode (F11 or click)';
        supportInfo.innerHTML = '<span class="text-success">True Fullscreen Supported</span>';
    } else {
        btn.title = 'Expand to browser window (fullscreen API not supported)';
        supportInfo.innerHTML = '<span class="text-warning">Browser Fullscreen Only</span>';
    }
}

// ============ SORTING FUNCTIONALITY ============

function initializeSorting() {
    console.log('Initializing sorting...');
    
    // Store original table data
    storeOriginalTableData();
    
    // Remove any existing click handlers first
    $('.sortable-header').off('click.sorting');
    
    // Add click handlers to sortable headers
    $('.sortable-header').on('click.sorting', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const column = $(this).data('column');
        console.log('Header clicked:', column);
        
        // If clicking the same column, toggle direction or reset
        if (currentSortColumn === column) {
            if (currentSortDirection === 'asc') {
                currentSortDirection = 'desc';
            } else if (currentSortDirection === 'desc') {
                // Reset to original order
                resetToOriginalOrder();
                return;
            } else {
                currentSortDirection = 'asc';
            }
        } else {
            // New column, start with ascending
            currentSortColumn = column;
            currentSortDirection = 'asc';
        }
        
        console.log('Sorting:', column, currentSortDirection);
        
        // Update UI and sort
        updateSortUI();
        sortTable(column, currentSortDirection);
    });
    
    console.log('Sorting initialized. Found', $('.sortable-header').length, 'sortable headers');
}

function storeOriginalTableData() {
    originalTableData = [];
    $('#reportingTable tbody tr').each(function() {
        if (!$(this).find('td').hasClass('text-center')) {
            originalTableData.push($(this).clone(true));
        }
    });
    console.log('Stored', originalTableData.length, 'original rows');
}

function resetToOriginalOrder() {
    console.log('Resetting to original order');
    currentSortColumn = null;
    currentSortDirection = null;
    
    // Clear all active states
    $('.sortable-header').removeClass('active asc desc');
    $('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    $('.sortable-header').css('background-color', '');
    
    // Restore original table data
    const tbody = $('#reportingTable tbody');
    tbody.empty();
    
    if (originalTableData.length > 0) {
        originalTableData.forEach(row => {
            tbody.append(row.clone(true));
        });
    }
}

function updateSortUI() {
    // Clear all active states
    $('.sortable-header').removeClass('active asc desc');
    $('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    $('.sortable-header').css('background-color', '');
    
    if (currentSortColumn) {
        const activeHeader = $(`.sortable-header[data-column="${currentSortColumn}"]`);
        activeHeader.addClass('active ' + currentSortDirection);
        activeHeader.css('background-color', '#007bff');
        
        const icon = activeHeader.find('.sort-icon');
        icon.removeClass('fa-sort');
        if (currentSortDirection === 'asc') {
            icon.addClass('fa-sort-up');
        } else {
            icon.addClass('fa-sort-down');
        }
    }
}

function sortTable(column, direction) {
    console.log('Sorting table by', column, direction);
    const tbody = $('#reportingTable tbody');
    const rows = tbody.find('tr').toArray();
    
    // Filter out "no data" rows
    const dataRows = rows.filter(row => !$(row).find('td').hasClass('text-center'));
    
    if (dataRows.length === 0) {
        console.log('No data rows found');
        return;
    }
    
    console.log('Sorting', dataRows.length, 'rows');
    
    dataRows.sort(function(a, b) {
        const aValue = getSortValue($(a), column);
        const bValue = getSortValue($(b), column);
        
        let comparison = 0;
        
        if (typeof aValue === 'number' && typeof bValue === 'number') {
            comparison = aValue - bValue;
        } else {
            comparison = String(aValue).localeCompare(String(bValue), undefined, {numeric: true});
        }
        
        return direction === 'asc' ? comparison : -comparison;
    });
    
    // Clear tbody and append sorted rows
    tbody.empty();
    dataRows.forEach(row => tbody.append(row));
    
    console.log('Table sorted successfully');
}

function getSortValue(row, column) {
    const cells = row.find('td');
    let value = '';
    
    switch(column) {
        case 'working_days':
            value = cells.eq(1).find('.badge').text().trim() || cells.eq(1).text().trim();
            break;
        case 'late_minutes':
            const lateText = cells.eq(2).find('.badge').attr('title') || '';
            const lateMatch = lateText.match(/Late minutes: (\d+)/);
            value = lateMatch ? parseInt(lateMatch[1]) : 0;
            break;
        case 'talktime':
            value = timeToSeconds(cells.eq(3).text().trim());
            break;
        case 'avg_talktime':
            value = timeToSeconds(cells.eq(4).text().trim());
            break;
        case 'total_calls':
            value = cells.eq(5).find('strong').text().trim() || cells.eq(5).text().trim();
            break;
        case 'total_submitted':
            value = cells.eq(6).find('.badge').text().trim() || cells.eq(6).text().trim();
            break;
        case 'underwriting':
            value = cells.eq(7).text().trim();
            break;
        case 'total_approved':
            value = cells.eq(8).find('strong').text().trim() || cells.eq(8).text().trim();
            break;
        case 'average_approved':
            value = cells.eq(9).text().trim();
            break;
        case 'premium_spd':
            value = cells.eq(10).find('strong').text().trim() || cells.eq(10).text().trim();
            break;
        case 'total_conv_calls':
            value = parseFloat(cells.eq(11).text().replace('%', '').trim()) || 0;
            break;
        case 'total_conv_approved':
            value = parseFloat(cells.eq(12).find('strong').text().replace('%', '').trim()) || 0;
            break;
        case 'avatar_xfer':
            value = cells.eq(13).find('strong').text().trim() || cells.eq(13).text().trim();
            break;
        case 'avatar_submitted':
            value = cells.eq(14).text().trim();
            break;
        case 'avatar_approved':
            value = cells.eq(15).text().trim();
            break;
        case 'avatar_conv_calls':
            value = parseFloat(cells.eq(16).find('.badge').text().replace('%', '').trim()) || 0;
            break;
        case 'avatar_conv_approved':
            value = parseFloat(cells.eq(17).find('.badge').text().replace('%', '').trim()) || 0;
            break;
        case 'jcs_xfers':
            value = cells.eq(18).text().trim();
            break;
        case 'jcs_submitted':
            value = cells.eq(19).text().trim();
            break;
        case 'jcs_approved':
            value = cells.eq(20).text().trim();
            break;
        case 'jcs_conv_calls':
            value = parseFloat(cells.eq(21).find('.badge').text().replace('%', '').trim()) || 0;
            break;
        case 'jcs_conv_approved':
            value = parseFloat(cells.eq(22).find('.badge').text().replace('%', '').trim()) || 0;
            break;
        case 'calls_200':
            value = cells.eq(23).find('.badge').text().trim() || cells.eq(23).text().trim();
            break;
        case 'calls_200_400':
            value = cells.eq(24).find('.badge').text().trim() || cells.eq(24).text().trim();
            break;
        case 'calls_400':
            value = cells.eq(25).find('.badge').text().trim() || cells.eq(25).text().trim();
            break;
        default:
            value = cells.eq(0).text().trim();
    }
    
    // Convert to number if possible
    const numValue = parseFloat(value);
    return isNaN(numValue) ? value : numValue;
}

function timeToSeconds(timeStr) {
    if (!timeStr || timeStr === '0:00:00') return 0;
    
    const parts = timeStr.split(':');
    if (parts.length === 3) {
        return parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseInt(parts[2]);
    }
    return 0;
}

// ============ UTILITY FUNCTIONS ============

function updateBoxDimensions() {
    const box = document.querySelector('.table-container');
    if (box) {
        const width = box.offsetWidth;
        const height = box.offsetHeight;
        const screenHeight = window.innerHeight;
        const heightPercent = Math.round((height / screenHeight) * 100);
        document.getElementById('boxDimensions').textContent = `${width}px × ${height}px (${heightPercent}% of screen height)`;
    }
}

function loadQuickStats() {
    const viewType = '{{ $viewType }}';
    const selectedDate = '{{ $selectedDate }}';
    const selectedMonth = '{{ $selectedMonth }}';
    const showAllDates = '{{ request("show_all_dates") ? "1" : "0" }}';
    
    let url = '';
    
    if (viewType === 'monthly' || showAllDates === '1' || selectedDate === 'all') {
        url = `{{ route('reporting.api.summary') }}?view_type=monthly&month=${selectedMonth}`;
    } else {
        url = `{{ route('reporting.api.summary') }}?view_type=daily&date=${selectedDate}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.summary;
                document.getElementById('quickStats').innerHTML = `
                    <span class="badge bg-primary">Employees: ${stats.total_employees}</span>
                    <span class="badge bg-success">Total Calls: ${stats.total_calls}</span>
                    <span class="badge bg-info">Total Talk Time: ${stats.total_talktime}</span>
                    <span class="badge bg-warning text-dark">Avg Talk Time: ${stats.avg_talktime}</span>
                    <span class="badge bg-secondary">Avatar Xfers: ${stats.total_avatar_xfers}</span>
                    <span class="badge bg-dark">JC Xfers: ${stats.total_jcs_xfers}</span>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            document.getElementById('quickStats').innerHTML = '<span class="badge bg-danger">Error loading stats</span>';
        });
}

function toggleViewControls() {
    const viewType = document.getElementById('viewType').value;
    const dateControls = document.getElementById('dateControls');
    
    if (viewType === 'monthly') {
        dateControls.style.display = 'none';
        document.getElementById('showAllDates').value = '0';
    } else {
        dateControls.style.display = 'block';
    }
    
    updateViewTypeLabel();
    
    // Reinitialize sorting after view change
    setTimeout(function() {
        initializeSorting();
    }, 100);
}

function updateDateOptions() {
    const selectedMonth = document.getElementById('monthFilter').value;
    const viewType = document.getElementById('viewType').value;
    
    if (viewType === 'daily') {
        const dateFilter = document.getElementById('dateFilter');
        dateFilter.innerHTML = '<option value="">Loading dates...</option>';
        dateFilter.disabled = true;
        
        fetch(`{{ route('reporting.index') }}?month=${selectedMonth}&get_dates_only=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            dateFilter.innerHTML = '<option value="all">All Dates</option>';
            
            if (data.dates && data.dates.length > 0) {
                data.dates.forEach(date => {
                    const option = document.createElement('option');
                    option.value = date;
                    option.textContent = new Date(date).toLocaleDateString('en-US', { 
                        month: 'short', day: 'numeric', year: 'numeric' 
                    });
                    dateFilter.appendChild(option);
                });
            }
            dateFilter.disabled = false;
        })
        .catch(error => {
            console.error('Error fetching dates:', error);
            dateFilter.innerHTML = '<option value="all">All Dates</option>';
            dateFilter.disabled = false;
        });
    }
}

function updateViewTypeLabel() {
    const viewType = document.getElementById('viewType').value;
    const selectedMonth = document.getElementById('monthFilter').value;
    
    let labelText = '';
    if (viewType === 'monthly') {
        const monthDate = new Date(selectedMonth + '-01');
        labelText = `Monthly Stats (${monthDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}):`;
    } else {
        const monthDate = new Date(selectedMonth + '-01');
        labelText = `Daily Stats (${monthDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}):`;
    }
    
    document.getElementById('viewTypeLabel').textContent = labelText;
}

function refreshData() {
    document.body.classList.add('loading');
    location.reload();
}

// ============ INITIALIZATION ============

document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('dateFilter');
    const showAllDatesInput = document.getElementById('showAllDates');
    
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            if (this.value === 'all') {
                showAllDatesInput.value = '1';
            } else {
                showAllDatesInput.value = '0';
            }
        });
    }
    
    toggleViewControls();
    
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        document.body.classList.add('loading');
    });
    
    // Initialize fullscreen support check
    checkFullscreenSupport();
});
</script>

@endsection