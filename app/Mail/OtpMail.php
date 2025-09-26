<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $subjectText;

    public function __construct($otp, $subjectText)
    {
        $this->otp = $otp;
        $this->subjectText = $subjectText;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.otp')
                    ->with([
                        'otp'    => $this->otp,
                        'title'  => $this->subjectText,
                    ]);
    }
}
