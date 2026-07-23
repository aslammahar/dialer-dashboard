<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'accountant_id',
        'month_year',
        'expense_category',
        'description',
        'amount',
        'type',
    ];

    public function accountant()
    {
        return $this->belongsTo(AccountingEntry::class, 'accountant_id');
    }

    public function accountingEntry()
    {
        return $this->belongsTo(AccountingEntry::class, 'accountant_id');
    }
}