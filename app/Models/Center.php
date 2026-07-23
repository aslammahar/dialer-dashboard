<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $fillable = [
        'center_name',
        'description',
        'created_by',
    ];

     public function users()
    {
        return $this->hasMany(User::class, 'center_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}