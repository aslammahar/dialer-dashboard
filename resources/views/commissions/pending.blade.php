@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Pending Commission</h2>
                <a href="{{ route('commission.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if(isset($pending))
                <!-- Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Agent: {{ $pending['agent_name'] }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Configuration</h6>
                                <p class="mb-1"><strong>Advance Months:</strong> {{ $pending['advance_months'] }}</p>
                                @if($config->notes)
                                    <p class="mb-0"><strong>Notes:</strong> {{ $config->notes }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success mb-0">
                                    <h4>Total Pending</h4>
                                    <h2>${{ number_format($pending['total_pending'], 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Pending Breakdown by Policy</h5>
                    </div>
                    <div class="card-body">
                        @if(count($pending['policies']) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Policy #</th>
                                            <th>Insured</th>
                                            <th>Advance Months</th>
                                            <th>Credited</th>
                                            <th>Pending</th>
                                            <th>Monthly Premium</th>
                                            <th>Pending Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pending['policies'] as $policy)
                                            <tr>
                                                <td>{{ $policy['policy_no'] }}</td>
                                                <td>{{ $policy['insured_name'] ?? '-' }}</td>
                                                <td>{{ $policy['advance_months'] }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ $policy['credited_months'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark">{{ $policy['pending_months'] }}</span>
                                                </td>
                                                <td>${{ number_format($policy['monthly_premium'], 2) }}</td>
                                                <td class="text-success">
                                                    <strong>${{ number_format($policy['pending_amount'], 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="6" class="text-end">Total:</th>
                                            <th class="text-success">
                                                ${{ number_format($pending['total_pending'], 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Info -->
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-info-circle"></i> How It's Calculated</h6>
                                <ul class="mb-0">
                                    <li><strong>Advance Months:</strong> Config setting for this agent</li>
                                    <li><strong>Credited:</strong> Months with positive commission received</li>
                                    <li><strong>Pending:</strong> Advance Months - Credited Months</li>
                                    <li><strong>Amount:</strong> Pending Months × Monthly Premium</li>
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                No advance policies found for this agent.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Select Agent -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Select Agent</h5>
                    </div>
                    <div class="card-body">
                        <p>Select an agent from the main page to view pending commissions.</p>
                        <a href="{{ route('commission.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection