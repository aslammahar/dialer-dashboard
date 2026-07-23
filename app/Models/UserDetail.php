<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'father_name',
        'pseudo_name',
        'phone',
        'email',
        'team_leader',
        'cnic_number',
        'address',
        'date_of_birth',
        'date_of_joining',
        'source_of_joining',
        'emergency_phone',
        'city',
        'designation',
        'work_from',
        'employee_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankDetails()
    {
        return $this->hasMany(UserBankDetail::class, 'user_id', 'user_id');
    }

    /**
     * POLYMORPHIC RELATIONSHIP WITH ATTACHMENTS
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * SPECIFIC ATTACHMENT TYPES - DYNAMIC RELATIONSHIPS
     */
    public function cnicFront()
    {
        return $this->morphOne(Attachment::class, 'attachable')
                    ->where('category', 'cnic_front');
    }

    public function cnicBack()
    {
        return $this->morphOne(Attachment::class, 'attachable')
                    ->where('category', 'cnic_back');
    }

    /**
     * HELPER METHODS FOR ATTACHMENTS
     */
    public function getCnicFrontUrlAttribute()
    {
        return $this->cnicFront ? $this->cnicFront->url : null;
    }

    public function getCnicBackUrlAttribute()
    {
        return $this->cnicBack ? $this->cnicBack->url : null;
    }

    /**
     * Check if specific attachment exists
     */
    public function hasAttachment($category)
    {
        return $this->attachments()->where('category', $category)->exists();
    }

    /**
     * Get attachment by category
     */
    public function getAttachment($category)
    {
        return $this->attachments()->where('category', $category)->first();
    }
}