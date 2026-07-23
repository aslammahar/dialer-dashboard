<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Define the relationship where a Role belongs to many Users
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'role_users', 'role_id', 'user_id');
    }
}
