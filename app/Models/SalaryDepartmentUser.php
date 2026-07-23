<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDepartmentUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_department_id',
        'user_id',
        'assigned_date',
        'is_active'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function salaryDepartment()
    {
        return $this->belongsTo(SalaryDepartment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}