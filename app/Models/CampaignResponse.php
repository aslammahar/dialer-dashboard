<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'campaign_id',
        'submission_data',
        'refer_to',
       
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'submission_data' => 'array',
        'created_at' => 'datetime', // Recommended for timestamps
        'updated_at' => 'datetime', // Recommended for timestamps
    ];

    /**
     * Get the campaign that owns the response.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign(): BelongsTo // Type Hinting
    {
        return $this->belongsTo(OurCampaign::class, 'campaign_id');
    }

    /**
     * Get the user that created the response.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function createdBy(): ?BelongsTo // Optional relationship
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that last updated the response.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function updatedBy(): ?BelongsTo // Optional relationship
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function referredTo(): ?BelongsTo
    {
        return $this->belongsTo(User::class, 'refer_to');
    }
}