<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DataDumpSent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $file;
    private $olddatafilename;
    public function __construct($file, $olddatafilename)
    {
        $this->file = $file;
        $this->olddatafilename = $olddatafilename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                    ->from(env('MAIL_FROM_EMAIL'))
                    ->view('emails.data_sent_email')
                    ->attachFromStorage($this->file, 'UNSOS Data.xlsx', ['mime' =>  \Storage::mimeType($this->file)])
                    ->attachFromStorage($this->olddatafilename, 'UNSOS OLD PM.xlsx', ['mime' =>  \Storage::mimeType($this->olddatafilename)]);
    }
}
