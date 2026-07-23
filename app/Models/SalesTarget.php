<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
 protected $fillable = [
    'month', 'spd_target', 'monthly_spd_target', 'raw_target',
    'milestone_1_label', 'milestone_2_label', 'milestone_2_amount',
    'milestone_3_label', 'reward_headline',
];
    protected $casts = ['month' => 'date'];
}