<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryDepartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'role_type',
        'description',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'salary_department_users')
                    ->withPivot('assigned_date', 'is_active')
                    ->withTimestamps();
    }

    public function activeUsers()
    {
        return $this->users()->wherePivot('is_active', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salaryStructures()
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function monthlySalaries()
    {
        return $this->hasMany(MonthlySalary::class);
    }

    // Get available users by role type
    public static function getAvailableUsersByRole($roleType)
    {
        return User::where('type', $roleType)->get();
    }

    
}