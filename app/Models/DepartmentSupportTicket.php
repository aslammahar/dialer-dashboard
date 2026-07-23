<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentSupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_support_id',
        'user_id',
        'subject',
        'description',
        'status',
        'response',
    ];

    // Ticket ka parent support
    public function support()
    {
        return $this->belongsTo(DepartmentSupport::class, 'department_support_id');
    }

    // Ticket ko create karne wala user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Assigned users ke liye many-to-many relation
    public function assignedUsers()
    {
        return $this->belongsToMany(
            User::class, 
            'department_support_ticket_user', // pivot table
            'dept_suprt_tckt_id',   // this model ka FK
            'user_id'                         // related model ka FK
        );
    }
}
