<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'email'];

    // Relationship to get children users
    public function users()
    {
        return $this->hasMany(User::class, 'client_id');
    }
    
    // Relationship to get the parent user record
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'email', 'email')
                    ->where('is_parent', 1)
                    ->where('type', 'client');
    }
}