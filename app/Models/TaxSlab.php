<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxSlab extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'min_salary',
        'max_salary',
        'fixed_amount',
        'tax_percentage',
        'description',
        'is_active'
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function monthlySalaries()
    {
        return $this->hasMany(MonthlySalary::class);
    }

    /**
     * Calculate monthly tax based on YEARLY gross salary
     * Following Pakistani Tax System (Progressive/Cumulative Tax)
     * 
     * @param float $monthlyGrossSalary - Monthly gross salary
     * @return array ['tax_amount' => monthly tax, 'tax_percentage', 'tax_slab_id', 'breakdown']
     */
    public static function calculateTax($monthlyGrossSalary)
    {
        // Convert monthly to yearly
        $yearlyGrossSalary = floatval($monthlyGrossSalary) * 12;

        // Get all active tax slabs ordered by min_salary
        $slabs = self::where('is_active', true)
                    ->orderBy('min_salary', 'asc')
                    ->get();

        if ($slabs->isEmpty()) {
            return [
                'tax_amount' => 0,
                'tax_percentage' => 0,
                'tax_slab_id' => null,
                'yearly_tax' => 0,
                'breakdown' => []
            ];
        }

        // Find applicable slab
        $applicableSlab = null;
        foreach ($slabs as $slab) {
            if ($yearlyGrossSalary >= floatval($slab->min_salary)) {
                if (is_null($slab->max_salary) || $yearlyGrossSalary <= floatval($slab->max_salary)) {
                    $applicableSlab = $slab;
                    break;
                }
            }
        }

        if (!$applicableSlab) {
            return [
                'tax_amount' => 0,
                'tax_percentage' => 0,
                'tax_slab_id' => null,
                'yearly_tax' => 0,
                'breakdown' => []
            ];
        }

        // Calculate tax: Fixed Amount + (Percentage × Exceeding Amount)
        $fixedAmount = floatval($applicableSlab->fixed_amount ?? 0);
        $taxableAmount = max(0, $yearlyGrossSalary - floatval($applicableSlab->min_salary));
        $variableTax = ($taxableAmount * floatval($applicableSlab->tax_percentage)) / 100;
        
        $yearlyTaxAmount = $fixedAmount + $variableTax;
        
        // Convert to monthly tax
        $monthlyTaxAmount = $yearlyTaxAmount / 12;

        return [
            'tax_amount' => round($monthlyTaxAmount, 2),
            'tax_percentage' => floatval($applicableSlab->tax_percentage),
            'tax_slab_id' => $applicableSlab->id,
            'yearly_tax' => round($yearlyTaxAmount, 2),
            'breakdown' => [
                'yearly_gross' => $yearlyGrossSalary,
                'slab_min' => floatval($applicableSlab->min_salary),
                'slab_max' => floatval($applicableSlab->max_salary),
                'fixed_amount' => $fixedAmount,
                'taxable_amount' => $taxableAmount,
                'variable_tax' => round($variableTax, 2),
                'total_yearly_tax' => round($yearlyTaxAmount, 2)
            ]
        ];
    }

    /**
     * Get formatted salary range
     */
    public function getRangeAttribute()
    {
        $min = number_format($this->min_salary, 0);
        $max = $this->max_salary ? number_format($this->max_salary, 0) : '& Above';
        return "Rs. {$min} - {$max}";
    }

    /**
     * Get formatted tax formula
     */
    public function getTaxFormulaAttribute()
    {
        if ($this->tax_percentage == 0) {
            return 'No Tax';
        }

        $fixed = $this->fixed_amount > 0 ? 'Rs. ' . number_format($this->fixed_amount, 0) . ' + ' : '';
        $percentage = $this->tax_percentage . '%';
        
        if ($this->fixed_amount > 0) {
            return $fixed . $percentage . ' of amount exceeding Rs. ' . number_format($this->min_salary, 0);
        } else {
            return $percentage . ' of amount exceeding Rs. ' . number_format($this->min_salary, 0);
        }
    }

    /**
     * Get detailed tax calculation breakdown (for display purposes)
     */
    public static function getTaxBreakdown($monthlyGrossSalary)
    {
        $result = self::calculateTax($monthlyGrossSalary);
        
        if (!$result['tax_slab_id']) {
            return null;
        }

        $slab = self::find($result['tax_slab_id']);

        return [
            'monthly_gross' => $monthlyGrossSalary,
            'yearly_gross' => $result['breakdown']['yearly_gross'],
            'slab_range' => $slab->range,
            'tax_formula' => $slab->tax_formula,
            'fixed_tax' => $result['breakdown']['fixed_amount'],
            'taxable_amount' => $result['breakdown']['taxable_amount'],
            'tax_percentage' => $result['tax_percentage'],
            'variable_tax' => $result['breakdown']['variable_tax'],
            'yearly_tax' => $result['yearly_tax'],
            'monthly_tax' => $result['tax_amount']
        ];
    }
}