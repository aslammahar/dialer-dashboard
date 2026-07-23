<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    protected $fillable = [
        'salary_structure_id',
        'component_name',
        'component_type',
        'amount',
        'is_taxable'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean'
    ];

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }
}