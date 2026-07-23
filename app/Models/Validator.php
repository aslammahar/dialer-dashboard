<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validator extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    public static $rules = [
        'code' => 'required|string|unique:validators|max:255',
        'name' => 'required|string|max:255',
    ];
}