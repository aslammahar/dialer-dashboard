<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterList extends Model
{
    protected $table = 'centerlist_tb'; // Assuming your table name is 'centerlist_tb'

    protected $fillable = [
        'centerName',
        'centerCode',
    ];
}