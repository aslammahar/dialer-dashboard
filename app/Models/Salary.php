<?php

// app/Models/Salary.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_name',
        'designation',
        'account_number',
        'bank_name',
        'account_title',
        'salary',
        'salary_month',
        'bank_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}