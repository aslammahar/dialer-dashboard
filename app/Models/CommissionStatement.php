<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_name', 'agent_no', 'level', 'contract_code', 'policy_no',
        'insured_name', 'plan_name', 'issue_date', 'process_date', 'due_date',
        'check_date', 'check_no', 'annual_premium', 'monthly_premium',
        'commission_rate', 'description', 'debit', 'commission_credit',
        'balance', 'parent_id', 'month', 'year', 'month_no', 'file_name',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'process_date' => 'date',
        'due_date' => 'date',
        'check_date' => 'date',
        'annual_premium' => 'decimal:2',
        'monthly_premium' => 'decimal:2',
        'debit' => 'decimal:2',
        'commission_credit' => 'decimal:2',
        'balance' => 'decimal:2',
        'year' => 'integer',
        'month_no' => 'integer',
    ];

    /**
     * Relationship: Commission Statement belongs to Closed Call
     * Using policy_no to match with policy_id in closed_calls
     */
    public function closedCall()
    {
        return $this->belongsTo(ClosedCall::class, 'policy_no', 'policy_id');
    }

    /**
     * Scope: Filter by month and year
     */
    public function scopeByMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month_no', $month);
    }

    /**
     * Scope: Filter by agent name
     */
    public function scopeByAgent($query, $agentName)
    {
        return $query->where('agent_name', $agentName);
    }

    /**
     * Get all unique statement months
     */
    public static function getAvailableMonths()
    {
        return self::select('year', 'month_no', 'month')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month_no', 'desc')
            ->get();
    }

    /**
     * Get total revenue for a policy
     */
    public static function getTotalRevenue($policyNo)
    {
        return self::where('policy_no', $policyNo)->sum('commission_credit');
    }

    /**
     * Get latest process date for a policy
     */
    public static function getLatestProcessDate($policyNo)
    {
        return self::where('policy_no', $policyNo)
            ->whereNotNull('process_date')
            ->orderBy('process_date', 'desc')
            ->value('process_date');
    }
}