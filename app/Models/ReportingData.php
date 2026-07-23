<?php
// app/Models/ReportingData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportingData extends Model
{
    use HasFactory;

    protected $table = 'reporting_data';


    protected $fillable = [
        'employee_id',
        'report_date',
        'name',
        'talktime',
        'talktime_seconds',
        'avg_talktime',
        'avg_talktime_seconds',
        'total_avatar_jcs_xfers',
        'avatar_xfer',
        'jcs_xfers',
        'working_days',
        'late_min',
        'total_submitted_sales',
        'underwriting_ho',
        'total_approved',
        'average_approved',
        'premium_approved_spd',
        'total_conv_calls_submission',
        'total_conv_approved_submission',
        'avatar_xfer_submitted_sales',
        'avatar_xfer_approved_sales',
        'avatar_xfer_conv_calls_submission',
        'avatar_xfer_conv_approved_submission',
        'jcs_submitted',
        'jcs_approved',
        'jcs_conv_calls_submission',
        'jcs_conv_approved_submission',
        'calls_dur_less_than_200_secs',
        'calls_dur_between_200_400_secs',
        'calls_dur_greater_than_400_secs',
        'rec_1_200_sec_duration',
        'rec_2_400_sec_duration',
        'rec_3_600_sec_duration'
    ];

    protected $casts = [
        'report_date' => 'date',
        'talktime_seconds' => 'integer',
        'avg_talktime_seconds' => 'integer',
        'total_avatar_jcs_xfers' => 'integer',
        'avatar_xfer' => 'integer',
        'jcs_xfers' => 'integer',
        'working_days' => 'integer',
        'late_min' => 'integer',
        'total_submitted_sales' => 'integer',
        'underwriting_ho' => 'integer',
        'total_approved' => 'integer',
        'average_approved' => 'decimal:2',
        'premium_approved_spd' => 'decimal:2',
        'total_conv_calls_submission' => 'decimal:2',
        'total_conv_approved_submission' => 'decimal:2',
        'avatar_xfer_submitted_sales' => 'integer',
        'avatar_xfer_approved_sales' => 'integer',
        'avatar_xfer_conv_calls_submission' => 'decimal:2',
        'avatar_xfer_conv_approved_submission' => 'decimal:2',
        'jcs_submitted' => 'integer',
        'jcs_approved' => 'integer',
        'jcs_conv_calls_submission' => 'decimal:2',
        'jcs_conv_approved_submission' => 'decimal:2',
        'calls_dur_less_than_200_secs' => 'integer',
        'calls_dur_between_200_400_secs' => 'integer',
        'calls_dur_greater_than_400_secs' => 'integer',
    ];

    /**
     * Convert H:M:S to seconds
     */
    public static function timeToSeconds($time)
    {
        if (empty($time)) return 0;
        
        $parts = explode(':', $time);
        if (count($parts) != 3) return 0;
        
        return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
    }

    /**
     * Convert seconds to H:M:S format
     */
    public static function secondsToTime($seconds)
    {
        if ($seconds <= 0) return '0:00:00';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Get formatted talktime
     */
    public function getFormattedTalktimeAttribute()
    {
        return $this->talktime ?: '0:00:00';
    }

    /**
     * Get formatted avg talktime
     */
    public function getFormattedAvgTalktimeAttribute()
    {
        return $this->avg_talktime ?: '0:00:00';
    }
}