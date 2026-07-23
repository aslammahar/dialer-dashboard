<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'user_id',
        'state',
        'city',
        'zip_code ',
        'address',
        'age',
        'spouse_age',
        'beneficiary ',
        'plan',
        'smoker',
        'color_hobby',
        'licensed_agent_name ',
        'call_back_time',
        'pipeline_id',
        'stage_id',
        'sources',
        'products',
        'notes',
        'priorities',
        'labels',
        'groups',
        'order',
        'created_by',
        'is_active',
        'date',
    ];

    public function priorities()
    {
        if($this->priorities)
        {
            return Priority::whereIn('id', explode(',', $this->priorities))->get();
        }

        return false;
    }






    public function labels()
    {
        if($this->labels)
        {
            return Label::whereIn('id', explode(',', $this->labels))->get();
        }

        return false;
    }






    public function groups()
    {
        if($this->groups)
        {
            return Group::whereIn('id', explode(',', $this->groups))->get();
        }

        return false;
    }


 


    public function stage()
    {
        return $this->hasOne('App\Models\LeadStage', 'id', 'stage_id');
    }


     public function priority()
    {
        return $this->hasOne('App\Models\Priority', 'id', 'priorities');
    }


     public function group()
    {
        return $this->hasOne('App\Models\Group', 'id', 'groups');
    }




    public function files()
    {
        return $this->hasMany('App\Models\LeadFile', 'lead_id', 'id');
    }

    public function pipeline()
    {
        return $this->hasOne('App\Models\Pipeline', 'id', 'pipeline_id');
    }

    public function products()
    {
        if($this->products)
        {
            return ProductService::whereIn('id', explode(',', $this->products))->get();
        }

        return [];
    }

    public function sources()
    {
        if($this->sources)
        {
            return Source::whereIn('id', explode(',', $this->sources))->get();
        }

        return [];
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_leads', 'lead_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\LeadActivityLog', 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function discussions()
    {
        return $this->hasMany('App\Models\LeadDiscussion', 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function calls()
    {
        return $this->hasMany('App\Models\LeadCall', 'lead_id', 'id');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\LeadEmail', 'lead_id', 'id')->orderByDesc('id');
    }
}
