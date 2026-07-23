<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DialerServer extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'dialer_servers';

    // Primary key
    protected $primaryKey = 'id';

    // Disable timestamps if not used
    public $timestamps = false;

    // Mass assignable fields
    protected $fillable = [
        'dialer_name',
        'server_no',
        'server_ip',
        'folder_name',
        'server_status',
    ];
}
