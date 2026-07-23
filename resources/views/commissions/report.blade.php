@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Commission Report - {{ $monthName }}</h2>
                <a href="{{ route('commission.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-select" id="month" name="month">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-select" id="year" name="year">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All</option>
                                    <option value="agents" {{ $type == 'agents' ? 'selected' : '' }}>Agents</option>
                                    <option value="closers" {{ $type == 'closers' ? 'selected' : '' }}>Closers</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Total Policies</h6>
                            <h3>{{ count($data) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Total Revenue</h6>
                            <h3>${{ number_format(collect($data)->sum('total_revenue'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>Monthly Premium</h6>
                            <h3>${{ number_format(collect($data)->sum('monthly_premium'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6>Annual Premium</h6>
                            <h3>${{ number_format(collect($data)->sum('annual_premium'), 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detailed Report</h5>
                </div>
                <div class="card-body">
                    @if(count($data) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="reportTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Policy #</th>
                                        <th>Schedule Date</th>
                                        <th>Draft Date</th>
                                        <th>Process Date</th>
                                        <th>Last Updated</th>
                                        <th>Insured Name</th>
                                        <th>Closer</th>
                                        <th>Client</th>
                                        <th>Monthly</th>
                                        <th>Annual</th>
                                        <th>Description</th>
                                        <th>Calc. Commission</th>
                                        <th>Calc. %</th>
                                        <th>Total Revenue</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        @php $idx = $loop->index; @endphp
                                        
                                        {{-- Main row --}}
                                        <tr>
                                            <td>{{ $row['policy_no'] }}</td>
                                            <td>{{ $row['schedule_date'] ? $row['schedule_date']->format('m/d/Y') : '-' }}</td>
                                            <td>{{ $row['draft_date'] ? $row['draft_date']->format('m/d/Y') : '-' }}</td>
                                            <td>{{ $row['process_date'] ? $row['process_date']->format('m/d/Y') : '-' }}</td>
                                            <td>{{ $row['last_updated'] ? $row['last_updated']->format('m/d/Y') : '-' }}</td>
                                            <td>{{ $row['insured_name'] ?? '-' }}</td>
                                            <td>
                                                @if($row['is_closer'])
                                                    <span class="badge bg-success">{{ $row['closer_name'] }}</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $row['client_name'] }}</td>
                                            <td>${{ number_format($row['monthly_premium'], 2) }}</td>
                                            <td>${{ number_format($row['annual_premium'], 2) }}</td>
                                            <td>
                                                <span class="badge {{ str_contains(strtolower($row['description']), 'advance') ? 'bg-warning' : 'bg-info' }}">
                                                    {{ $row['description'] }}
                                                </span>
                                            </td>
                                            @php
                                                // Get first statement's commission rate for theoretical calculation
                                                $firstStmt = $row['statements']->first();
                                                $commissionRate = (float) ($firstStmt->commission_rate ?? 0);
                                                $monthlyPremium = (float) ($row['monthly_premium'] ?? 0);
                                                $annualPremium  = (float) ($row['annual_premium'] ?? 0);
                                                $totalRevenue   = (float) ($row['total_revenue'] ?? 0);
                                                
                                                // Calculated commission (theoretical): monthly × rate × 12 months
                                                $calculatedCommission = $monthlyPremium * ($commissionRate / 100) * 12;
                                                
                                                // Calculated percentage: what % of annual premium is the theoretical commission
                                                $calculatedPercentage = $annualPremium > 0 ? ($calculatedCommission / $annualPremium) * 100 : 0;
                                            @endphp
                                            <td class="{{ $calculatedCommission >= 0 ? 'text-info' : 'text-danger' }}">
                                                <strong>${{ number_format(abs($calculatedCommission), 2) }}</strong>
                                            </td>
                                            <td class="{{ $calculatedCommission >= 0 ? 'text-primary' : 'text-danger' }}">
                                                <strong>{{ number_format($calculatedPercentage, 1) }}%</strong>
                                            </td>
                                            <td class="{{ $totalRevenue >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>${{ number_format($totalRevenue, 2) }}</strong>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" id="viewBtn-{{ $idx }}" onclick="toggleDetail({{ $idx }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Expandable detail row --}}
                                        <tr id="detail-{{ $idx }}" style="display:none;">
                                            <td colspan="15" style="background:#f8f9fa;padding:20px;">
                                                <h6 class="text-primary mb-3">
                                                    <i class="fas fa-list-ul"></i>
                                                    Commission Breakdown — {{ $row['policy_no'] }}
                                                </h6>
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Month</th>
                                                            <th>Process Date</th>
                                                            <th>Due Date</th>
                                                            <th>Rate %</th>
                                                            <th>Calculated Commission</th>
                                                            <th>Commission Credit</th>
                                                            <th>Match</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($row['statements'] as $stmt)
                                                        @php
                                                            // Safely parse numeric values
                                                            $monthlyPremium = (float) ($row['monthly_premium'] ?? 0);
                                                            $commissionRate = (float) ($stmt->commission_rate ?? 0);
                                                            $actualComm     = (float) ($stmt->commission_credit ?? 0);
                                                            
                                                            // Calculate: monthly_premium × rate × 1 month
                                                            $expectedComm = $monthlyPremium * ($commissionRate / 100);
                                                            $isMatch      = abs($expectedComm - $actualComm) < 0.01;
                                                        @endphp
                                                        <tr>
                                                            <td><strong>{{ $stmt->month }}</strong></td>
                                                            <td>{{ $stmt->process_date ? $stmt->process_date->format('m/d/Y') : '—' }}</td>
                                                            <td>{{ $stmt->due_date ? $stmt->due_date->format('m/d/Y') : '—' }}</td>
                                                            <td>{{ number_format($commissionRate, 2) }}%</td>
                                                            <td class="text-muted">${{ number_format($expectedComm, 2) }}</td>
                                                            <td class="{{ $actualComm >= 0 ? 'text-success' : 'text-danger' }}">
                                                                <strong>${{ number_format($actualComm, 2) }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                @if($isMatch)
                                                                    <span class="text-success" style="font-size:16px;">✓</span>
                                                                @else
                                                                    <span class="text-danger" style="font-size:16px;">✗</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $stmt->description }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                                            <td class="{{ $row['total_revenue'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                                <strong>${{ number_format($row['total_revenue'], 2) }}</strong>
                                                            </td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        
                                                        {{-- Payment Progress Bar --}}
                                                        @if($row['annual_premium'] > 0)
                                                        @php
                                                            $annualPremium = (float) ($row['annual_premium'] ?? 0);
                                                            $totalRevenue  = (float) ($row['total_revenue'] ?? 0);
                                                            $paymentPercentage = $annualPremium > 0 ? ($totalRevenue / $annualPremium) * 100 : 0;
                                                        @endphp
                                                        <tr style="background:#e7f3ff;">
                                                            <td colspan="5" class="text-end" style="vertical-align:middle;">
                                                                <small>Payment Progress (of Annual Premium ${{ number_format($annualPremium, 2) }}):</small>
                                                            </td>
                                                            <td colspan="3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="progress flex-grow-1" style="height:10px;">
                                                                        <div class="progress-bar bg-primary" style="width:{{ min(100, $paymentPercentage) }}%"></div>
                                                                    </div>
                                                                    <strong class="text-primary" style="min-width:50px;">{{ number_format($paymentPercentage, 1) }}%</strong>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </tfoot>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            No data found for selected period.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle detail view
function toggleDetail(idx) {
    const row  = document.getElementById('detail-' + idx);
    const btn  = document.getElementById('viewBtn-' + idx);
    const open = row.style.display !== 'none';
    
    row.style.display = open ? 'none' : 'table-row';
    btn.innerHTML = open 
        ? '<i class="fas fa-eye"></i> View'
        : '<i class="fas fa-eye-slash"></i> Close';
    btn.classList.toggle('btn-outline-primary', open);
    btn.classList.toggle('btn-primary', !open);
}

// DataTable initialization
$(document).ready(function() {
    $('#reportTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 } // Disable sorting on Details column
        ]
    });
});
</script>
@endsection