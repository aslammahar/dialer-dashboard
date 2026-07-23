<?php
// app/Models/VendorList.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VendorList extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'list_id',
        'sales',
        'dialer_name',
        'vendor_name',
        'file_name',
        'total_numbers',
        'dnc',
        'duplicate',
        'clean',
        'sales_conversion',
        'xfers',
        'xfers_sales_conversion',
        'xfers_clean_conversion'
    ];

    protected $casts = [
        'sales_conversion' => 'decimal:4',
        'xfers_sales_conversion' => 'decimal:4',
        'xfers_clean_conversion' => 'decimal:10', // Adjusted to accommodate larger values
    ];

    /**
     * Generate vendor lists from closed_calls table
     */
    public static function generateVendorLists()
    {
        // Get approved calls data with sales count and dialer name
        $closedCallsData = DB::table('closed_calls')
            ->select(
                DB::raw('COALESCE(list_id_1, list_id_2) as list_id'),
                DB::raw('COUNT(*) as sales'),
                DB::raw('MAX(dialername) as dialername')
            )
            ->whereIn('status', ['approved']) // Adjust status field name if different
            ->where(function($query) {
                $query->whereNotNull('list_id_1')
                      ->orWhereNotNull('list_id_2');
            })
            ->groupBy(DB::raw('COALESCE(list_id_1, list_id_2)'))
            ->get();

        foreach ($closedCallsData as $data) {
            // Get xfers count from avatar_leads table
            $xfersCount = DB::table('avatar_leads')
                ->where('entry_list_id', $data->list_id)
                ->count();

            // Check if record exists
            $existingRecord = self::where('list_id', $data->list_id)->first();

            // Calculate conversions
            $salesConversion = $data->sales > 0 && $existingRecord && $existingRecord->clean > 0 
                ? round($data->sales / $existingRecord->clean, 4) 
                : 0;
            
            $xfersSalesConversion = $data->sales > 0 && $xfersCount > 0 
                ? round($data->sales / $xfersCount, 4) 
                : 0;
            
            $xfersCleanConversion = $existingRecord && $existingRecord->clean > 0 && $xfersCount > 0 
                ? round($existingRecord->clean / $xfersCount, 4) 
                : 0;

            if ($existingRecord) {
                // Update calculated fields only
                $existingRecord->update([
                    'sales' => $data->sales,
                    'dialer_name' => $data->dialername,
                    'xfers' => $xfersCount,
                    'sales_conversion' => $existingRecord->clean > 0 ? round($data->sales / $existingRecord->clean, 4) : 0,
                    'xfers_sales_conversion' => $xfersSalesConversion,
                    'xfers_clean_conversion' => $existingRecord->clean > 0 && $xfersCount > 0 ? round($existingRecord->clean / $xfersCount, 4) : 0,
                ]);
            } else {
                // Create new record
                self::create([
                    'list_id' => $data->list_id,
                    'sales' => $data->sales,
                    'dialer_name' => $data->dialername,
                    'xfers' => $xfersCount,
                    'sales_conversion' => 0, // Will be calculated after user enters clean numbers
                    'xfers_sales_conversion' => $xfersSalesConversion,
                    'xfers_clean_conversion' => 0, // Will be calculated after user enters clean numbers
                ]);
            }
        }

        return self::all();
    }

    /**
     * Update conversions after editing manual fields
     */
    public function updateConversions()
    {
        $this->sales_conversion = $this->clean > 0 ? round($this->sales / $this->clean, 4) : 0;
        $this->xfers_sales_conversion = $this->xfers > 0 ? round($this->sales / $this->xfers, 4) : 0;
        $this->xfers_clean_conversion = $this->xfers > 0 && $this->clean > 0 ? round($this->clean / $this->xfers, 4) : 0;
        $this->save();
    }
}