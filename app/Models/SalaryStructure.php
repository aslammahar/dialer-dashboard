<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'salary_department_id',
        'basic_salary',
        'working_days',
        'punctuality',
        'effective_from',
        'effective_to',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'punctuality' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryDepartment()
    {
        return $this->belongsTo(SalaryDepartment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function components()
    {
        return $this->hasMany(SalaryComponent::class);
    }

    public function allowances()
    {
        return $this->components()->where('component_type', 'allowance');
    }

    public function deductions()
    {
        return $this->components()->where('component_type', 'deduction');
    }

    public function getTotalAllowancesAttribute()
    {
        return $this->allowances()->sum('amount');
    }

    public function getTotalDeductionsAttribute()
    {
        return $this->deductions()->sum('amount');
    }

    public function getGrossSalaryAttribute()
    {
        return $this->basic_salary + $this->punctuality + $this->total_allowances;
    }

    public function getNetSalaryAttribute()
    {
        return $this->gross_salary - $this->total_deductions;
    }
}