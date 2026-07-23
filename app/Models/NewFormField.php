<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewFormField extends Model
{
    use HasFactory;

    protected $table = 'campaign_form_fields'; // Important: Specify the existing table name
    protected $fillable = ['our_campaign_id', 'label', 'name', 'type', 'options', 'required', 'order','role','field_role','show_to'];
  
    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function campaign()
    {
        return $this->belongsTo(OurCampaign::class, 'our_campaign_id');
    }
}