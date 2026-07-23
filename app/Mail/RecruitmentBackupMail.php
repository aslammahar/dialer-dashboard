<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;

class RecruitmentBackupMail extends Mailable
{
    use SerializesModels, Queueable;

   public $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }


   public function build()
    {
        return $this->subject('Recruitment Backup')
            ->view('emails.recruitment_backup') // create a blade view like emails/recruitment_backup.blade.php
            ->attach(storage_path('backup/' . $this->fileName));
    }
}
