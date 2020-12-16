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
    private $filename;
    public function __construct($file, $olddatafilename, $filename)
    {
        $this->file = $file;
        $this->olddatafilename = $olddatafilename;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \Log::debug("Data Dump sent for: {$this->filename}");
        return $this
                    ->from(env('MAIL_FROM_EMAIL'))
                    ->view('emails.data_sent_email')
                    ->attachFromStorage($this->file, "{$this->filename} Data.xlsx", ['mime' =>  \Storage::mimeType($this->file)])
                    ->attachFromStorage($this->olddatafilename, "{$this->filename} OLD PM.xlsx", ['mime' =>  \Storage::mimeType($this->olddatafilename)]);
    }
}
