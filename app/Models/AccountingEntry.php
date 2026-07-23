<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'accountant_title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenseEntries()
{
    return $this->hasMany(ExpenseEntry::class, 'expense_type_id');
}

public function monthlyExpenses()
{
    return $this->hasMany(MonthlyExpense::class, 'accountant_id');
}

}