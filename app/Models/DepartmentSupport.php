<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentSupport extends Model
{
    use HasFactory;

    protected $fillable = ['title','subject','description','role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'department_support_user'); // Many-to-Many
    }
}

