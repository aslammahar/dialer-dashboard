<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_salary_id',
        'slip_number',
        'file_path',
        'status',
        'generated_at',
        'generated_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    // Relationships
    public function monthlySalary()
    {
        return $this->belongsTo(MonthlySalary::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Accessors
    public function getPeriodAttribute()
    {
        return $this->monthlySalary 
            ? date('F Y', mktime(0, 0, 0, $this->monthlySalary->month, 1, $this->monthlySalary->year))
            : null;
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? \Storage::url($this->file_path) : null;
    }

    public function getDownloadUrlAttribute()
    {
        return route('salary-slips.download', $this->id);
    }

    // Helper Methods
    public static function generateSlipNumber($year, $month)
    {
        $prefix = 'SLIP';
        $yearStr = $year;
        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        
        // Get the last slip number for this month
        $lastSlip = self::whereHas('monthlySalary', function($query) use ($year, $month) {
            $query->where('year', $year)->where('month', $month);
        })
        ->orderBy('id', 'desc')
        ->first();
        
        if ($lastSlip) {
            // Extract the sequence number from the last slip
            $parts = explode('-', $lastSlip->slip_number);
            $lastSequence = isset($parts[3]) ? intval($parts[3]) : 0;
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }
        
        $sequenceStr = str_pad($sequence, 5, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$yearStr}-{$monthStr}-{$sequenceStr}";
    }

    // Scopes
    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDownloaded($query)
    {
        return $query->where('status', 'downloaded');
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereHas('monthlySalary', function($q) use ($year) {
            $q->where('year', $year);
        });
    }

    public function scopeForMonth($query, $month)
    {
        return $query->whereHas('monthlySalary', function($q) use ($month) {
            $q->where('month', $month);
        });
    }

    public function scopeForPeriod($query, $year, $month)
    {
        return $query->whereHas('monthlySalary', function($q) use ($year, $month) {
            $q->where('year', $year)->where('month', $month);
        });
    }
}