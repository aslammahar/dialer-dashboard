@extends('layouts.admin')

@section('title', 'Tax Slab Details')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Tax Slab Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('tax-slabs.edit', $taxSlab) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('tax-slabs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tax Slab Information -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Tax Slab Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Salary Range (Yearly)</small>
                    <div class="fw-bold fs-5">{{ $taxSlab->range }}</div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Minimum Salary</small>
                        <div class="fw-bold">Rs. {{ number_format($taxSlab->min_salary, 2) }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Maximum Salary</small>
                        <div class="fw-bold">
                            {{ $taxSlab->max_salary ? 'Rs. ' . number_format($taxSlab->max_salary, 2) : '& Above' }}
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Fixed Tax Amount</small>
                        <div class="fw-bold text-warning">
                            {{ $taxSlab->fixed_amount > 0 ? 'Rs. ' . number_format($taxSlab->fixed_amount, 2) : 'None' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Tax Percentage</small>
                        <div class="fw-bold text-info">{{ $taxSlab->tax_percentage }}%</div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted">Tax Formula</small>
                    <div class="alert alert-info mb-0">
                        <strong>{{ $taxSlab->tax_formula }}</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Description</small>
                    <div>{{ $taxSlab->description ?? 'No description provided' }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Status</small>
                    <div>
                        @if($taxSlab->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Created At</small>
                        <div>{{ $taxSlab->created_at->format('d M, Y H:i') }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Updated At</small>
                        <div>{{ $taxSlab->updated_at->format('d M, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Calculator -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Tax Calculator</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Calculate tax for different salary amounts using this slab</p>
                
                <div class="mb-3">
                    <label class="form-label">Enter Monthly Gross Salary</label>
                    <input type="number" class="form-control" id="testSalary" placeholder="e.g., 100000">
                </div>

                <button class="btn btn-success w-100 mb-3" onclick="calculateTest()">
                    <i class="fas fa-calculator"></i> Calculate Tax
                </button>

                <div id="calculationResult" style="display: none;">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-3">Calculation Breakdown</h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">Monthly Gross Salary:</small>
                                <div class="fw-bold" id="resultMonthlyGross">-</div>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">Yearly Gross Salary:</small>
                                <div class="fw-bold" id="resultYearlyGross">-</div>
                            </div>

                            <hr>

                            <div class="mb-2">
                                <small class="text-muted">Fixed Tax:</small>
                                <div class="fw-bold text-warning" id="resultFixedTax">-</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Taxable Amount:</small>
                                <div class="fw-bold" id="resultTaxableAmount">-</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Variable Tax ({{ $taxSlab->tax_percentage }}%):</small>
                                <div class="fw-bold text-info" id="resultVariableTax">-</div>
                            </div>

                            <hr>

                            <div class="mb-2">
                                <small class="text-muted">Total Yearly Tax:</small>
                                <div class="fw-bold text-danger fs-5" id="resultYearlyTax">-</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Monthly Tax Deduction:</small>
                                <div class="fw-bold text-danger fs-4" id="resultMonthlyTax">-</div>
                            </div>

                            <hr>

                            <div class="alert alert-success mb-0">
                                <small class="text-muted">Net Monthly Salary:</small>
                                <div class="fw-bold fs-4" id="resultNetSalary">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Example Calculations -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-table"></i> Example Calculations</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Monthly Salary</th>
                        <th>Yearly Salary</th>
                        <th>Monthly Tax</th>
                        <th>Yearly Tax</th>
                        <th>Net Monthly</th>
                    </tr>
                </thead>
                <tbody id="exampleTable">
                    <!-- Examples will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Button -->
<div class="mt-3">
    <form action="{{ route('tax-slabs.destroy', $taxSlab) }}" method="POST" 
          onsubmit="return confirm('Are you sure you want to delete this tax slab?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Delete Tax Slab
        </button>
    </form>
</div>

<script>
function calculateTest() {
    const salary = document.getElementById('testSalary').value;
    const resultDiv = document.getElementById('calculationResult');
    
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

            document.getElementById('resultMonthlyGross').textContent = 'Rs. ' + parseFloat(data.monthly_gross).toLocaleString();
            document.getElementById('resultYearlyGross').textContent = 'Rs. ' + parseFloat(data.yearly_gross).toLocaleString();
            document.getElementById('resultFixedTax').textContent = 'Rs. ' + parseFloat(data.fixed_tax).toLocaleString();
            document.getElementById('resultTaxableAmount').textContent = 'Rs. ' + parseFloat(data.taxable_amount).toLocaleString();
            document.getElementById('resultVariableTax').textContent = 'Rs. ' + parseFloat(data.variable_tax).toLocaleString();
            document.getElementById('resultYearlyTax').textContent = 'Rs. ' + parseFloat(data.yearly_tax).toLocaleString();
            document.getElementById('resultMonthlyTax').textContent = 'Rs. ' + parseFloat(data.monthly_tax).toLocaleString();
            
            const netSalary = parseFloat(salary) - parseFloat(data.monthly_tax);
            document.getElementById('resultNetSalary').textContent = 'Rs. ' + netSalary.toLocaleString();
            
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error calculating tax');
        });
}

// Generate example calculations
document.addEventListener('DOMContentLoaded', function() {
    const exampleSalaries = [50000, 100000, 150000, 200000, 300000, 500000];
    const tbody = document.getElementById('exampleTable');
    
    exampleSalaries.forEach(salary => {
        fetch('{{ route("tax-slabs.calculate-preview") }}?monthly_salary=' + salary)
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>Rs. ${salary.toLocaleString()}</td>
                        <td>Rs. ${parseFloat(data.yearly_gross).toLocaleString()}</td>
                        <td class="text-danger">Rs. ${parseFloat(data.monthly_tax).toLocaleString()}</td>
                        <td class="text-danger">Rs. ${parseFloat(data.yearly_tax).toLocaleString()}</td>
                        <td class="text-success fw-bold">Rs. ${(salary - parseFloat(data.monthly_tax)).toLocaleString()}</td>
                    `;
                }
            });
    });
});
</script>
@endsection