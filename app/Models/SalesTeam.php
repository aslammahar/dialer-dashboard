<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesTeam extends Model
{
    protected $fillable = ['name','target'];
    public function closers() { return $this->hasMany(SalesCloser::class); }
}