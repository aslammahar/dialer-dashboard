<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyDuplicatesReport extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Daily Duplicate Leads Report')
            ->view('emails.daily_duplicates')
            ->attach($this->filePath);
    }
}
