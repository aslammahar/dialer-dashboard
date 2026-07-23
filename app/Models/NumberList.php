<?php
// app/Models/NumberList.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NumberList extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'data_vendor',
        'file_name',
        'list_id',
        'total_numbers',
        'blocks_dubs_from_same_file',
        'dialer_scrubbing',
        'dnc_clean_numbers',
        'clean',
        'vendor_id'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    /**
     * Get sales count from closed_calls table
     * This will be used in the frontend for conversion calculation
     */
    public function getSalesAttribute()
    {
        return DB::table('closed_calls')
            ->where('list_id_1', $this->list_id)
            ->orWhere('list_id_2', $this->list_id)
            ->count();
    }

    /**
     * Get conversion rate (Clean / Sale)
     * This is computed attribute for frontend use
     */
    public function getConversionRateAttribute()
    {
        $sales = $this->sales;
        return $this->clean > 0 && $sales > 0 ? round(($this->clean / $sales), 4) : 0;
    }

    /**
     * Get the vendor associated with the number list
     */
    public function vendor()
    {
        return $this->belongsTo(DataVendor::class);
    }
}