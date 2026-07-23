<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentConfig extends Model
{
    use HasFactory;

    protected $fillable = ['agent_name', 'advance_months', 'notes', 'is_active'];

    protected $casts = [
        'advance_months' => 'integer',
        'is_active' => 'boolean',  // FIXED: was 'active' => 'boolean'
    ];

    public function statements()
    {
        return $this->hasMany(CommissionStatement::class, 'agent_name', 'agent_name');
    }

    public function calculatePending()
    {
        $advances = $this->statements()
            ->where('description', 'LIKE', '%Advance%')
            ->get();

        if ($advances->isEmpty()) {
            return [
                'agent_name' => $this->agent_name,
                'advance_months' => $this->advance_months,
                'total_pending' => 0,
                'policies' => []
            ];
        }

        $policies = [];
        
        foreach ($advances as $stmt) {
            $policyNo = $stmt->policy_no;
            
            if (!isset($policies[$policyNo])) {
                $policies[$policyNo] = [
                    'policy_no' => $policyNo,
                    'insured_name' => $stmt->insured_name,
                    'advance_months' => $this->advance_months,
                    'credited_months' => 0,
                    'monthly_premium' => $stmt->monthly_premium ?? 0,
                ];
            }

            if ($stmt->commission_credit > 0) {
                $policies[$policyNo]['credited_months']++;
            }
        }

        $totalPending = 0;
        $details = [];

        foreach ($policies as $policy) {
            $pending = $policy['advance_months'] - $policy['credited_months'];
            $amount = $pending * $policy['monthly_premium'];
            
            $totalPending += $amount;
            
            $details[] = [
                'policy_no' => $policy['policy_no'],
                'insured_name' => $policy['insured_name'],
                'advance_months' => $policy['advance_months'],
                'credited_months' => $policy['credited_months'],
                'pending_months' => $pending,
                'monthly_premium' => $policy['monthly_premium'],
                'pending_amount' => $amount,
            ];
        }

        return [
            'agent_name' => $this->agent_name,
            'advance_months' => $this->advance_months,
            'total_pending' => $totalPending,
            'policies' => $details,
        ];
    }
}