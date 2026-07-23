<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Priority extends Model


{
    protected $fillable = [
        'name',
        'color',
        'pipeline_id',
        'created_by',
    ];

    public static $colors = [
        'primary',
        'secondary',
        'danger',
        'warning',
        'info',
        'success',
    ];
}
