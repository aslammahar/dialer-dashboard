<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_type_id', // Updated to match the correct foreign key
        'date',
        'description',
        'type',
        'amount',
        'remarks',
    ];

    // Relationship to the AccountingEntry model
    public function accountingEntry()
    {
        return $this->belongsTo(AccountingEntry::class, 'expense_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'expense_type_id');
    }
}