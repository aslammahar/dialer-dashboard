<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'monthly_salary_id',
        'user_id',
        'user_bank_detail_id',
        'bank_name',
        'account_title',
        'account_number',
        'payment_amount',
        'payment_status',
        'remarks',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function monthlySalary()
    {
        return $this->belongsTo(MonthlySalary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankDetail()
    {
        return $this->belongsTo(UserBankDetail::class, 'user_bank_detail_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * POLYMORPHIC RELATIONSHIP WITH ATTACHMENTS (Payment Screenshots)
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get payment screenshot
     */
    public function paymentScreenshot()
    {
        return $this->morphOne(Attachment::class, 'attachable')
                    ->where('category', 'payment_screenshot');
    }

    /**
     * Get screenshot URL
     */
    public function getScreenshotUrlAttribute()
    {
        return $this->paymentScreenshot ? $this->paymentScreenshot->url : null;
    }

    /**
     * Check if payment is sent
     */
    public function isSent()
    {
        return $this->payment_status === 'sent';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if payment is declined
     */
    public function isDeclined()
    {
        return $this->payment_status === 'declined';
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('payment_status', 'sent');
    }

    public function scopeDeclined($query)
    {
        return $query->where('payment_status', 'declined');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-warning',
            'sent' => 'bg-success',
            'declined' => 'bg-danger'
        ][$this->payment_status] ?? 'bg-secondary';
    }
}