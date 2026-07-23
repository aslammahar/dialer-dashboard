<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesCloser extends Model
{
    protected $fillable = ['name', 'sales_team_id', 'active'];
    public function team() { return $this->belongsTo(SalesTeam::class, 'sales_team_id'); }
}