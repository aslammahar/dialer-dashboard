<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurProject extends Model
{
    use HasFactory;

    protected $table = 'our_projects';
    protected $fillable = ['name', 'description'];

    public function campaigns()
    {
        return $this->hasMany(OurCampaign::class);
    }
}