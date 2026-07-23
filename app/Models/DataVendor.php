<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataVendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_name'
    ];

    // relation with NumberList
    public function numberLists()
    {
        return $this->hasMany(NumberList::class, 'vendor_id');
        // specify foreign key 'vendor_id'
    }
}
