<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueSaleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_sale_id',
        'user_id',
        'content',
        'parent_id',
    ];

    public function queueSale()
    {
        return $this->belongsTo(QueueSale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(QueueSaleComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(QueueSaleComment::class, 'parent_id')->with('user', 'replies')->latest();
    }
}