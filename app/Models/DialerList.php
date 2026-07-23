<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialerList extends Model
{
    protected $table = 'dialerlist_tb'; // Assuming your table name is 'dialerlists'

    protected $fillable = [
        'dialer_ip',
        'dialer_weblink',
        'dialer_access',
        'dialer_no',
        'dialer_team', // Add 'dialer_team' to the fillable array
    ];
    
   
    // Other model methods...
}