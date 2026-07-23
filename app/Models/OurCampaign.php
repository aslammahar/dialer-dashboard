<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurCampaign extends Model
{
    use HasFactory;

    protected $table = 'our_campaigns';
    protected $fillable = ['our_project_id', 'name', 'description'];
    

    public function project()
    {
        return $this->belongsTo(OurProject::class);
    }

    public function newFormFields() // Relationship name (important!)
    {
        return $this->hasMany(NewFormField::class, 'our_campaign_id')->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(CampaignResponse::class, 'campaign_id');
    }
}