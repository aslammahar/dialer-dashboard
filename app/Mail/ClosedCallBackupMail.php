<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ClosedCallBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }


    public function build()
    {
        return $this->subject('Closed Call Backup')
            ->view('emails.closedcall_backup')
            ->attach(storage_path('backup/' . $this->fileName));
    }
}
