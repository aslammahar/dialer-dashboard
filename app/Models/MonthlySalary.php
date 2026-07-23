<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySalary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monthly_salaries';

    protected $fillable = [
        'user_id',
        'salary_department_id',
        'salary_structure_id',
        'year',
        'month',
        'basic_salary',
        'working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'punctuality',
        'total_allowances',
        'total_deductions',
        'tax_amount',
        'tax_percentage',
        'tax_slab_id',
        'bonus',
        'gross_salary',
        'net_salary',
        'status',
        'remarks',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'working_days' => 'integer',
        'present_days' => 'integer',
        'absent_days' => 'integer',
        'leave_days' => 'integer',
        'punctuality' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'bonus' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryDepartment()
    {
        return $this->belongsTo(SalaryDepartment::class, 'salary_department_id');
    }

    public function department()
    {
        return $this->salaryDepartment();
    }

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function taxSlab()
    {
        return $this->belongsTo(TaxSlab::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function salarySlip()
    {
        return $this->hasOne(SalarySlip::class);
    }

    public function payment()
    {
        return $this->hasOne(SalaryPayment::class, 'monthly_salary_id');
    }

    public function getPeriodAttribute()
    {
        return date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    public function hasPayment()
    {
        return $this->payment()->exists();
    }

    public function getPaymentStatusAttribute()
    {
        return $this->payment ? $this->payment->payment_status : 'pending';
    }

    /**
     * Calculate and set tax for this salary
     */
    public function calculateTax()
    {
        // Calculate gross salary (basic + allowances - deductions before tax)
        $grossSalary = $this->basic_salary + $this->total_allowances - $this->total_deductions;
        
        $taxData = TaxSlab::calculateTax($grossSalary);
        
        $this->tax_amount = $taxData['tax_amount'];
        $this->tax_percentage = $taxData['tax_percentage'];
        $this->tax_slab_id = $taxData['tax_slab_id'];
        
        // Recalculate net salary (gross - tax)
        $this->net_salary = $grossSalary - $this->tax_amount + $this->bonus;
        
        return $this;
    }
}