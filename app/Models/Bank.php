<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope for active banks
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordering by category and name
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')->orderBy('name');
    }
}