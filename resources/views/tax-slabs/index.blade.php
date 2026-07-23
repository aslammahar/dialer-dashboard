@extends('layouts.admin')

@section('title', 'Tax Slabs Management')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Tax Slabs Management</h2>
        <a href="{{ route('tax-slabs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Tax Slab
        </a>
    </div>
</div>

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

<!-- Tax Calculator Card -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-calculator"></i> Quick Tax Calculator</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Enter Monthly Gross Salary</label>
                <input type="number" class="form-control" id="quickCalcSalary" placeholder="e.g., 100000">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-info w-100" onclick="calculateQuickTax()">
                    <i class="fas fa-calculator"></i> Calculate
                </button>
            </div>
            <div class="col-md-6" id="quickCalcResult" style="display: none;">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">Monthly Tax</small>
                                <h5 class="mb-0 text-danger" id="monthlyTax">0</h5>
                            </div>
                            <div>
                                <small class="text-muted">Yearly Tax</small>
                                <h5 class="mb-0 text-danger" id="yearlyTax">0</h5>
                            </div>
                            <div>
                                <small class="text-muted">Tax Formula</small>
                                <div id="taxFormula" class="small">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tax Slabs Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Salary Range (Yearly)</th>
                        <th>Fixed Amount</th>
                        <th>Tax Percentage</th>
                        <th>Tax Formula</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxSlabs as $slab)
                        <tr>
                            <td>{{ $loop->iteration + ($taxSlabs->currentPage() - 1) * $taxSlabs->perPage() }}</td>
                            <td>
                                <strong>{{ $slab->range }}</strong>
                            </td>
                            <td>
                                @if($slab->fixed_amount > 0)
                                    <span class="badge bg-warning text-dark">Rs. {{ number_format($slab->fixed_amount, 0) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $slab->tax_percentage }}%</span>
                            </td>
                            <td>
                                <small>{{ $slab->tax_formula }}</small>
                            </td>
                            <td>
                                @if($slab->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tax-slabs.show', $slab) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tax-slabs.edit', $slab) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tax-slabs.toggle-status', $slab) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $slab->is_active ? 'btn-secondary' : 'btn-success' }}" title="{{ $slab->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('tax-slabs.destroy', $slab) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax slab?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tax slabs found. <a href="{{ route('tax-slabs.create') }}">Create one now</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $taxSlabs->links() }}
        </div>
    </div>
</div>

<script>
function calculateQuickTax() {
    const salary = document.getElementById('quickCalcSalary').value;
    const resultDiv = document.getElementById('quickCalcResult');
    
    if (!salary || salary <= 0) {
        alert('Please enter a valid salary amount');
        return;
    }

    fetch('{{ route("tax-slabs.calculate-preview") }}?monthly_salary=' + salary)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('monthlyTax').textContent = 'Rs. ' + parseFloat(data.monthly_tax).toLocaleString();
            document.getElementById('yearlyTax').textContent = 'Rs. ' + parseFloat(data.yearly_tax).toLocaleString();
            document.getElementById('taxFormula').textContent = data.tax_formula;
            
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error calculating tax');
        });
}
</script>
@endsection